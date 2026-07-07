<?php

namespace App\Services;

use App\Enums\WorkflowStatus;
use App\Models\Approval;
use App\Models\ApprovalLog;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EventWorkflowService
{
    public function __construct(private WorkflowNotificationService $notifications)
    {
    }

    public function submit(Event $event, User $actor, ?string $note = null): void
    {
        $this->transition(
            event: $event,
            actor: $actor,
            toStatus: WorkflowStatus::Submitted,
            actionType: 'submit_event',
            note: $note,
            approvalData: [
                'submitted_by' => $actor->id,
                'submitted_at' => now(),
            ],
        );
    }

    public function markUnderReview(Event $event, User $actor, ?string $note = null): void
    {
        $this->transition(
            event: $event,
            actor: $actor,
            toStatus: WorkflowStatus::UnderReview,
            actionType: 'review_event',
            note: $note,
            approvalData: [
                'current_reviewer_id' => $actor->id,
                'reviewed_at' => now(),
            ],
        );
    }

    public function requestRevision(Event $event, User $actor, ?string $note = null): void
    {
        $this->transition(
            event: $event,
            actor: $actor,
            toStatus: WorkflowStatus::RevisionNeeded,
            actionType: 'request_event_revision',
            note: $note,
            approvalData: [
                'current_reviewer_id' => $actor->id,
                'reviewed_at' => now(),
            ],
        );
    }

    public function approve(Event $event, User $actor, ?string $note = null): void
    {
        $this->transition(
            event: $event,
            actor: $actor,
            toStatus: WorkflowStatus::Approved,
            actionType: 'approve_event',
            note: $note,
            approvalData: [
                'current_reviewer_id' => $actor->id,
                'reviewed_at' => now(),
                'approved_at' => now(),
            ],
            eventData: [
                'approved_by' => $actor->id,
            ],
        );
    }

    public function publish(Event $event, User $actor, ?string $note = null): void
    {
        $this->transition(
            event: $event,
            actor: $actor,
            toStatus: WorkflowStatus::Published,
            actionType: 'publish_event',
            note: $note,
            approvalData: [
                'published_at' => now(),
            ],
            eventData: [
                'approved_by' => $event->approved_by ?: $actor->id,
                'published_at' => now(),
                'is_active' => true,
            ],
        );
    }

    public function archive(Event $event, User $actor, ?string $note = null): void
    {
        $this->transition(
            event: $event,
            actor: $actor,
            toStatus: WorkflowStatus::Archived,
            actionType: 'archive_event',
            note: $note,
            eventData: [
                'is_active' => false,
            ],
        );
    }

    public function restoreArchive(Event $event, User $actor, ?string $note = null): void
    {
        $previousStatus = ApprovalLog::query()
            ->whereHas('approval', function ($query) use ($event): void {
                $query->where('approvable_type', $event->getMorphClass())
                    ->where('approvable_id', $event->id);
            })
            ->where('action_type', 'archive_event')
            ->latest()
            ->value('from_status');

        $toStatus = $previousStatus instanceof WorkflowStatus
            ? $previousStatus
            : WorkflowStatus::tryFrom((string) $previousStatus);

        if (! $toStatus || $toStatus === WorkflowStatus::Archived) {
            $toStatus = WorkflowStatus::Draft;
        }

        $this->transition(
            event: $event,
            actor: $actor,
            toStatus: $toStatus,
            actionType: 'restore_event_archive',
            note: $note,
            eventData: [
                'is_active' => true,
            ],
        );
    }

    private function transition(
        Event $event,
        User $actor,
        WorkflowStatus $toStatus,
        string $actionType,
        ?string $note = null,
        array $approvalData = [],
        array $eventData = [],
    ): void {
        DB::transaction(function () use ($event, $actor, $toStatus, $actionType, $note, $approvalData, $eventData): void {
            $event->refresh();
            $fromStatus = $event->workflow_status;
            $approvalKey = [
                'approvable_type' => $event->getMorphClass(),
                'approvable_id' => $event->id,
            ];
            $existingApproval = Approval::query()->where($approvalKey)->first();

            $approval = Approval::updateOrCreate(
                $approvalKey,
                [
                    ...$approvalData,
                    'current_status' => $toStatus,
                    'latest_note' => $note ?? $existingApproval?->latest_note,
                ],
            );

            ApprovalLog::create([
                'approval_id' => $approval->id,
                'acted_by' => $actor->id,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'note' => $note,
                'action_type' => $actionType,
            ]);

            $event->update([
                ...$eventData,
                'workflow_status' => $toStatus,
                'updated_by' => $actor->id,
            ]);
        });

        $event->refresh();
        $this->notifications->notify($event, $toStatus, $actionType, $note);
    }
}
