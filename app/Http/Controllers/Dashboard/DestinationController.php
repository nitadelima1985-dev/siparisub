<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\WorkflowStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\DestinationMediaRequest;
use App\Http\Requests\Dashboard\DestinationRequest;
use App\Http\Requests\Dashboard\DestinationWorkflowRequest;
use App\Models\Destination;
use App\Models\DestinationCategory;
use App\Models\DestinationMedia;
use App\Models\District;
use App\Services\DestinationWorkflowService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Destination::class);

        $destinations = Destination::query()
            ->with(['category', 'district', 'creator'])
            ->when(! $request->user()->hasRole('super_admin', 'admin_dinas', 'reviewer_akademik'), function ($query) use ($request): void {
                $query->where('created_by', $request->user()->id);
            })
            ->when($request->filled('category'), fn ($query) => $query->where('destination_category_id', $request->integer('category')))
            ->when($request->filled('district'), fn ($query) => $query->where('district_id', $request->integer('district')))
            ->when($request->filled('status'), fn ($query) => $query->where('workflow_status', $request->input('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.destinations.index', [
            'destinations' => $destinations,
            'categories' => $this->categories(),
            'districts' => $this->districts(),
            'statuses' => WorkflowStatus::cases(),
            'reviewQueueStatuses' => [WorkflowStatus::Submitted, WorkflowStatus::UnderReview, WorkflowStatus::RevisionNeeded],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Destination::class);

        return view('dashboard.destinations.create', [
            'destination' => new Destination([
                'workflow_status' => WorkflowStatus::Draft,
                'is_active' => true,
                'is_featured' => false,
            ]),
            'categories' => $this->categories(),
            'districts' => $this->districts(),
        ]);
    }

    public function store(DestinationRequest $request, DestinationWorkflowService $workflow): RedirectResponse
    {
        $data = $this->destinationData($request);

        $destination = DB::transaction(function () use ($request, $data): Destination {
            return Destination::create([
                ...$data,
                'created_by' => $request->user()->id,
                'slug' => $this->uniqueSlug($data['name']),
                'workflow_status' => WorkflowStatus::Draft,
                'last_content_update_at' => now(),
            ]);
        });

        if ($request->input('intent') === 'submit' && $request->user()->can('submit', $destination)) {
            $workflow->submit($destination, $request->user());

            return redirect()
                ->route('dashboard.destinations.show', $destination)
                ->with('success', 'Destinasi berhasil dibuat dan disubmit untuk review.');
        }

        return redirect()
            ->route('dashboard.destinations.show', $destination)
            ->with('success', 'Draft destinasi berhasil disimpan.');
    }

    public function show(Destination $destination): View
    {
        $this->authorize('view', $destination);

        $destination->load([
            'category',
            'district',
            'creator',
            'updater',
            'approver',
            'coverMedia',
            'latestApproval',
            'media' => fn ($query) => $query->latest(),
            'approvals.logs.actor',
        ]);

        return view('dashboard.destinations.show', [
            'destination' => $destination,
            'approvalLogs' => $destination->approvals->flatMap->logs->sortByDesc('created_at'),
        ]);
    }

    public function edit(Destination $destination): View
    {
        $this->authorize('update', $destination);

        return view('dashboard.destinations.edit', [
            'destination' => $destination,
            'categories' => $this->categories(),
            'districts' => $this->districts(),
        ]);
    }

    public function update(DestinationRequest $request, Destination $destination, DestinationWorkflowService $workflow): RedirectResponse
    {
        $data = $this->destinationData($request);

        DB::transaction(function () use ($request, $destination, $data): void {
            if ($destination->name !== $data['name']) {
                $data['slug'] = $this->uniqueSlug($data['name'], $destination->id);
            }

            $destination->update([
                ...$data,
                'updated_by' => $request->user()->id,
                'last_content_update_at' => now(),
            ]);
        });

        if ($request->input('intent') === 'submit' && $request->user()->can('submit', $destination)) {
            $workflow->submit($destination, $request->user());

            return redirect()
                ->route('dashboard.destinations.show', $destination)
                ->with('success', 'Destinasi berhasil diperbarui dan disubmit untuk review.');
        }

        return redirect()
            ->route('dashboard.destinations.show', $destination)
            ->with('success', 'Destinasi berhasil diperbarui.');
    }

    public function destroy(Destination $destination): RedirectResponse
    {
        $this->authorize('delete', $destination);

        DB::transaction(function () use ($destination): void {
            $destination->load(['approvals.logs', 'media']);

            foreach ($destination->approvals as $approval) {
                $approval->logs()->delete();
                $approval->delete();
            }

            $destination->activityLogs()->delete();

            foreach ($destination->media as $media) {
                if ($media->file_path) {
                    Storage::disk('public')->delete($media->file_path);
                }
            }

            $destination->media()->delete();
            $destination->articles()->update(['destination_id' => null]);
            $destination->events()->update(['destination_id' => null]);
            $destination->delete();
        });

        return redirect()
            ->route('dashboard.destinations.index')
            ->with('success', 'Destinasi berhasil dihapus.');
    }
    public function uploadCover(DestinationMediaRequest $request, Destination $destination): RedirectResponse
    {
        $path = $request->file('cover_image')->store('destinations/covers', 'public');

        DB::transaction(function () use ($request, $destination, $path): void {
            $destination->media()->where('is_cover', true)->update(['is_cover' => false]);

            $destination->media()->create([
                'uploaded_by' => $request->user()->id,
                'media_type' => DestinationMedia::TYPE_IMAGE,
                'file_path' => $path,
                'caption' => $request->input('caption'),
                'is_cover' => true,
                'sort_order' => 0,
            ]);

            $destination->update([
                'updated_by' => $request->user()->id,
                'last_content_update_at' => now(),
            ]);
        });

        return redirect()
            ->route('dashboard.destinations.show', $destination)
            ->with('success', 'Cover destinasi berhasil diunggah.');
    }

    public function submit(DestinationWorkflowRequest $request, Destination $destination, DestinationWorkflowService $workflow): RedirectResponse
    {
        $workflow->submit($destination, $request->user(), $request->input('note'));

        return redirect()
            ->route('dashboard.destinations.show', $destination)
            ->with('success', 'Destinasi berhasil disubmit untuk review.');
    }

    public function markUnderReview(DestinationWorkflowRequest $request, Destination $destination, DestinationWorkflowService $workflow): RedirectResponse
    {
        $workflow->markUnderReview($destination, $request->user(), $request->input('note'));

        return redirect()
            ->route('dashboard.destinations.show', $destination)
            ->with('success', 'Status destinasi diubah menjadi under review.');
    }

    public function requestRevision(DestinationWorkflowRequest $request, Destination $destination, DestinationWorkflowService $workflow): RedirectResponse
    {
        $workflow->requestRevision($destination, $request->user(), $request->input('note'));

        return redirect()
            ->route('dashboard.destinations.show', $destination)
            ->with('success', 'Catatan revisi berhasil dikirim ke pengusul.');
    }

    public function approve(DestinationWorkflowRequest $request, Destination $destination, DestinationWorkflowService $workflow): RedirectResponse
    {
        $workflow->approve($destination, $request->user(), $request->input('note'));

        return redirect()
            ->route('dashboard.destinations.show', $destination)
            ->with('success', 'Destinasi berhasil di-approve.');
    }

    public function publish(DestinationWorkflowRequest $request, Destination $destination, DestinationWorkflowService $workflow): RedirectResponse
    {
        $workflow->publish($destination, $request->user(), $request->input('note'));

        return redirect()
            ->route('dashboard.destinations.show', $destination)
            ->with('success', 'Destinasi berhasil dipublish.');
    }

    public function archive(DestinationWorkflowRequest $request, Destination $destination, DestinationWorkflowService $workflow): RedirectResponse
    {
        $workflow->archive($destination, $request->user(), $request->input('note'));

        return redirect()
            ->route('dashboard.destinations.show', $destination)
            ->with('success', 'Destinasi berhasil diarsipkan.');
    }

    public function restoreArchive(DestinationWorkflowRequest $request, Destination $destination, DestinationWorkflowService $workflow): RedirectResponse
    {
        $this->authorize('restoreArchive', $destination);

        $workflow->restoreArchive($destination, $request->user(), $request->input('note'));

        return redirect()
            ->route('dashboard.destinations.show', $destination)
            ->with('success', 'Arsip destinasi berhasil dibuka kembali.');
    }

    private function destinationData(DestinationRequest $request): array
    {
        return [
            ...$request->safe()->except(['intent', 'is_featured', 'is_active']),
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while (Destination::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function categories()
    {
        return DestinationCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function districts()
    {
        return District::query()
            ->orderBy('name')
            ->get();
    }
}

