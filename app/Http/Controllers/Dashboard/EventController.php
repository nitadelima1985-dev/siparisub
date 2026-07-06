<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\WorkflowStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\EventRequest;
use App\Http\Requests\Dashboard\EventWorkflowRequest;
use App\Models\Destination;
use App\Models\Event;
use App\Services\EventWorkflowService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Event::class);

        $events = Event::query()
            ->with(['destination', 'creator'])
            ->when(! $request->user()->hasRole('super_admin', 'admin_dinas', 'reviewer_akademik'), function ($query) use ($request): void {
                $query->where('created_by', $request->user()->id);
            })
            ->when($request->filled('status'), fn ($query) => $query->where('workflow_status', $request->input('status')))
            ->when($request->filled('destination'), fn ($query) => $query->where('destination_id', $request->integer('destination')))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('start_date', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('start_date', '<=', $request->date('date_to')))
            ->latest('start_date')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.events.index', [
            'events' => $events,
            'destinations' => $this->destinations(),
            'statuses' => WorkflowStatus::cases(),
            'reviewQueueStatuses' => [WorkflowStatus::Submitted, WorkflowStatus::UnderReview, WorkflowStatus::RevisionNeeded],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Event::class);

        return view('dashboard.events.create', [
            'event' => new Event([
                'workflow_status' => WorkflowStatus::Draft,
                'is_active' => true,
            ]),
            'destinations' => $this->destinations(),
        ]);
    }

    public function store(EventRequest $request, EventWorkflowService $workflow): RedirectResponse
    {
        $data = $this->eventData($request);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('events/covers', 'public');
        }

        $event = DB::transaction(function () use ($request, $data): Event {
            return Event::create([
                ...$data,
                'created_by' => $request->user()->id,
                'slug' => $this->uniqueSlug($data['title']),
                'workflow_status' => WorkflowStatus::Draft,
            ]);
        });

        if ($request->input('intent') === 'submit' && $request->user()->can('submit', $event)) {
            $workflow->submit($event, $request->user());

            return redirect()
                ->route('dashboard.events.show', $event)
                ->with('success', 'Event berhasil dibuat dan disubmit untuk review.');
        }

        return redirect()
            ->route('dashboard.events.show', $event)
            ->with('success', 'Draft event berhasil disimpan.');
    }

    public function show(Event $event): View
    {
        $this->authorize('view', $event);

        $event->load([
            'destination',
            'creator',
            'updater',
            'approver',
            'latestApproval',
            'approvals.logs.actor',
        ]);

        return view('dashboard.events.show', [
            'event' => $event,
            'approvalLogs' => $event->approvals->flatMap->logs->sortByDesc('created_at'),
        ]);
    }

    public function edit(Event $event): View
    {
        $this->authorize('update', $event);

        return view('dashboard.events.edit', [
            'event' => $event,
            'destinations' => $this->destinations(),
        ]);
    }

    public function update(EventRequest $request, Event $event, EventWorkflowService $workflow): RedirectResponse
    {
        $data = $this->eventData($request);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('events/covers', 'public');
        }

        DB::transaction(function () use ($request, $event, $data): void {
            if ($event->title !== $data['title']) {
                $data['slug'] = $this->uniqueSlug($data['title'], $event->id);
            }

            $event->update([
                ...$data,
                'updated_by' => $request->user()->id,
            ]);
        });

        if ($request->input('intent') === 'submit' && $request->user()->can('submit', $event)) {
            $workflow->submit($event, $request->user());

            return redirect()
                ->route('dashboard.events.show', $event)
                ->with('success', 'Event berhasil diperbarui dan disubmit untuk review.');
        }

        return redirect()
            ->route('dashboard.events.show', $event)
            ->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);

        DB::transaction(function () use ($event): void {
            $event->load('approvals.logs');

            foreach ($event->approvals as $approval) {
                $approval->logs()->delete();
                $approval->delete();
            }

            $event->activityLogs()->delete();

            if ($event->cover_image) {
                Storage::disk('public')->delete($event->cover_image);
            }

            $event->delete();
        });

        return redirect()
            ->route('dashboard.events.index')
            ->with('success', 'Event berhasil dihapus.');
    }
    public function submit(EventWorkflowRequest $request, Event $event, EventWorkflowService $workflow): RedirectResponse
    {
        $workflow->submit($event, $request->user(), $request->input('note'));

        return redirect()->route('dashboard.events.show', $event)->with('success', 'Event berhasil disubmit untuk review.');
    }

    public function markUnderReview(EventWorkflowRequest $request, Event $event, EventWorkflowService $workflow): RedirectResponse
    {
        $workflow->markUnderReview($event, $request->user(), $request->input('note'));

        return redirect()->route('dashboard.events.show', $event)->with('success', 'Status event diubah menjadi under review.');
    }

    public function requestRevision(EventWorkflowRequest $request, Event $event, EventWorkflowService $workflow): RedirectResponse
    {
        $workflow->requestRevision($event, $request->user(), $request->input('note'));

        return redirect()->route('dashboard.events.show', $event)->with('success', 'Catatan revisi event berhasil dikirim.');
    }

    public function approve(EventWorkflowRequest $request, Event $event, EventWorkflowService $workflow): RedirectResponse
    {
        $workflow->approve($event, $request->user(), $request->input('note'));

        return redirect()->route('dashboard.events.show', $event)->with('success', 'Event berhasil di-approve.');
    }

    public function publish(EventWorkflowRequest $request, Event $event, EventWorkflowService $workflow): RedirectResponse
    {
        $workflow->publish($event, $request->user(), $request->input('note'));

        return redirect()->route('dashboard.events.show', $event)->with('success', 'Event berhasil dipublish.');
    }

    public function archive(EventWorkflowRequest $request, Event $event, EventWorkflowService $workflow): RedirectResponse
    {
        $workflow->archive($event, $request->user(), $request->input('note'));

        return redirect()->route('dashboard.events.show', $event)->with('success', 'Event berhasil diarsipkan.');
    }

    public function restoreArchive(EventWorkflowRequest $request, Event $event, EventWorkflowService $workflow): RedirectResponse
    {
        $this->authorize('restoreArchive', $event);

        $workflow->restoreArchive($event, $request->user(), $request->input('note'));

        return redirect()
            ->route('dashboard.events.show', $event)
            ->with('success', 'Arsip event berhasil dibuka kembali.');
    }
    private function eventData(EventRequest $request): array
    {
        return [
            ...$request->safe()->except(['intent', 'cover_image', 'is_active']),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 2;

        while (Event::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function destinations()
    {
        return Destination::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
