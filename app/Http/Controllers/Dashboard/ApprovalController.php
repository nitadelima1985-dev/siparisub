<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\WorkflowStatus;
use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Article;
use App\Models\Destination;
use App\Models\Event;
use App\Services\ArticleWorkflowService;
use App\Services\DestinationWorkflowService;
use App\Services\EventWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasRole('super_admin', 'admin_dinas', 'reviewer_akademik'), 403);

        $approvals = Approval::query()
            ->with(['approvable', 'submitter', 'currentReviewer'])
            ->when($request->filled('status'), fn ($query) => $query->where('current_status', $request->input('status')))
            ->when($request->filled('type'), function ($query) use ($request): void {
                $query->where('approvable_type', $this->morphClassForType($request->input('type')));
            })
            ->when($request->user()->hasRole('reviewer_akademik') && ! $request->user()->hasRole('super_admin', 'admin_dinas'), function ($query): void {
                $query->whereIn('current_status', [
                    WorkflowStatus::Submitted->value,
                    WorkflowStatus::UnderReview->value,
                    WorkflowStatus::RevisionNeeded->value,
                ]);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('dashboard.approvals.index', [
            'approvals' => $approvals,
            'statuses' => WorkflowStatus::cases(),
            'types' => ['destination' => 'Destinasi', 'event' => 'Event', 'article' => 'Artikel'],
        ]);
    }

    public function markUnderReview(Request $request, Approval $approval, DestinationWorkflowService $destinations, EventWorkflowService $events, ArticleWorkflowService $articles): RedirectResponse
    {
        $content = $this->approvable($approval);
        $this->authorize('review', $content);
        $this->serviceFor($content, $destinations, $events, $articles)->markUnderReview($content, $request->user(), $request->input('note'));

        return back()->with('success', 'Konten ditandai under review.');
    }

    public function requestRevision(Request $request, Approval $approval, DestinationWorkflowService $destinations, EventWorkflowService $events, ArticleWorkflowService $articles): RedirectResponse
    {
        $request->validate(['note' => ['required', 'string', 'max:2000']]);

        $content = $this->approvable($approval);
        $this->authorize('review', $content);
        $this->serviceFor($content, $destinations, $events, $articles)->requestRevision($content, $request->user(), $request->input('note'));

        return back()->with('success', 'Catatan revisi berhasil dikirim.');
    }

    public function approve(Request $request, Approval $approval, DestinationWorkflowService $destinations, EventWorkflowService $events, ArticleWorkflowService $articles): RedirectResponse
    {
        $content = $this->approvable($approval);
        $this->authorize('approve', $content);
        $this->serviceFor($content, $destinations, $events, $articles)->approve($content, $request->user(), $request->input('note'));

        return back()->with('success', 'Konten berhasil di-approve.');
    }

    public function publish(Request $request, Approval $approval, DestinationWorkflowService $destinations, EventWorkflowService $events, ArticleWorkflowService $articles): RedirectResponse
    {
        $content = $this->approvable($approval);
        $this->authorize('publish', $content);
        $this->serviceFor($content, $destinations, $events, $articles)->publish($content, $request->user(), $request->input('note'));

        return back()->with('success', 'Konten berhasil dipublish.');
    }

    public function archive(Request $request, Approval $approval, DestinationWorkflowService $destinations, EventWorkflowService $events, ArticleWorkflowService $articles): RedirectResponse
    {
        $content = $this->approvable($approval);
        $this->authorize('archive', $content);
        $this->serviceFor($content, $destinations, $events, $articles)->archive($content, $request->user(), $request->input('note'));

        return back()->with('success', 'Konten berhasil diarsipkan.');
    }

    private function approvable(Approval $approval): Destination|Event|Article
    {
        $approval->loadMissing('approvable');
        abort_unless($approval->approvable instanceof Destination || $approval->approvable instanceof Event || $approval->approvable instanceof Article, 404);

        return $approval->approvable;
    }

    private function serviceFor(Destination|Event|Article $content, DestinationWorkflowService $destinations, EventWorkflowService $events, ArticleWorkflowService $articles): DestinationWorkflowService|EventWorkflowService|ArticleWorkflowService
    {
        return match (true) {
            $content instanceof Destination => $destinations,
            $content instanceof Event => $events,
            $content instanceof Article => $articles,
        };
    }

    private function morphClassForType(?string $type): string
    {
        return match ($type) {
            'destination' => (new Destination())->getMorphClass(),
            'event' => (new Event())->getMorphClass(),
            'article' => (new Article())->getMorphClass(),
            default => (string) $type,
        };
    }
}