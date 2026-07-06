<?php

namespace App\Services;

use App\Enums\WorkflowStatus;
use App\Models\Approval;
use App\Models\ApprovalLog;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DestinationWorkflowService
{
    public function submit(Destination $destination, User $actor, ?string $note = null): void
    {
        $this->transition(
            destination: $destination,
            actor: $actor,
            toStatus: WorkflowStatus::Submitted,
            actionType: 'submit_destination',
            note: $note,
            approvalData: [
                'submitted_by' => $actor->id,
                'submitted_at' => now(),
            ],
        );
    }

    public function markUnderReview(Destination $destination, User $actor, ?string $note = null): void
    {
        $this->transition(
            destination: $destination,
            actor: $actor,
            toStatus: WorkflowStatus::UnderReview,
            actionType: 'review_destination',
            note: $note,
            approvalData: [
                'current_reviewer_id' => $actor->id,
                'reviewed_at' => now(),
            ],
        );
    }

    public function requestRevision(Destination $destination, User $actor, ?string $note = null): void
    {
        $this->transition(
            destination: $destination,
            actor: $actor,
            toStatus: WorkflowStatus::RevisionNeeded,
            actionType: 'request_destination_revision',
            note: $note,
            approvalData: [
                'current_reviewer_id' => $actor->id,
                'reviewed_at' => now(),
            ],
        );
    }

    public function approve(Destination $destination, User $actor, ?string $note = null): void
    {
        $this->transition(
            destination: $destination,
            actor: $actor,
            toStatus: WorkflowStatus::Approved,
            actionType: 'approve_destination',
            note: $note,
            approvalData: [
                'current_reviewer_id' => $actor->id,
                'reviewed_at' => now(),
                'approved_at' => now(),
            ],
            destinationData: [
                'approved_by' => $actor->id,
            ],
        );
    }

    public function publish(Destination $destination, User $actor, ?string $note = null): void
    {
        $this->transition(
            destination: $destination,
            actor: $actor,
            toStatus: WorkflowStatus::Published,
            actionType: 'publish_destination',
            note: $note,
            approvalData: [
                'published_at' => now(),
            ],
            destinationData: [
                'approved_by' => $destination->approved_by ?: $actor->id,
                'published_at' => now(),
                'is_active' => true,
            ],
        );
    }

    public function archive(Destination $destination, User $actor, ?string $note = null): void
    {
        $this->transition(
            destination: $destination,
            actor: $actor,
            toStatus: WorkflowStatus::Archived,
            actionType: 'archive_destination',
            note: $note,
            destinationData: [
                'is_active' => false,
            ],
        );
    }

    public function restoreArchive(Destination $destination, User $actor, ?string $note = null): void
    {
        $previousStatus = ApprovalLog::query()
            ->whereHas('approval', function ($query) use ($destination): void {
                $query->where('approvable_type', $destination->getMorphClass())
                    ->where('approvable_id', $destination->id);
            })
            ->where('action_type', 'archive_destination')
            ->latest()
            ->value('from_status');

        $toStatus = $previousStatus instanceof WorkflowStatus
            ? $previousStatus
            : WorkflowStatus::tryFrom((string) $previousStatus);

        if (! $toStatus || $toStatus === WorkflowStatus::Archived) {
            $toStatus = WorkflowStatus::Draft;
        }

        $this->transition(
            destination: $destination,
            actor: $actor,
            toStatus: $toStatus,
            actionType: 'restore_destination_archive',
            note: $note,
            destinationData: [
                'is_active' => true,
            ],
        );
    }

    private function transition(
        Destination $destination,
        User $actor,
        WorkflowStatus $toStatus,
        string $actionType,
        ?string $note = null,
        array $approvalData = [],
        array $destinationData = [],
    ): void {
        DB::transaction(function () use ($destination, $actor, $toStatus, $actionType, $note, $approvalData, $destinationData): void {
            $destination->refresh();
            $fromStatus = $destination->workflow_status;
            $approvalKey = [
                'approvable_type' => $destination->getMorphClass(),
                'approvable_id' => $destination->id,
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

            $destination->update([
                ...$destinationData,
                'workflow_status' => $toStatus,
                'updated_by' => $actor->id,
            ]);
        });
    }
}

