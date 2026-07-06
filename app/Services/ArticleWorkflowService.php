<?php

namespace App\Services;

use App\Enums\WorkflowStatus;
use App\Models\Approval;
use App\Models\ApprovalLog;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ArticleWorkflowService
{
    public function submit(Article $article, User $actor, ?string $note = null): void
    {
        $this->transition($article, $actor, WorkflowStatus::Submitted, 'submit_article', $note, [
            'submitted_by' => $actor->id,
            'submitted_at' => now(),
        ]);
    }

    public function markUnderReview(Article $article, User $actor, ?string $note = null): void
    {
        $this->transition($article, $actor, WorkflowStatus::UnderReview, 'review_article', $note, [
            'current_reviewer_id' => $actor->id,
            'reviewed_at' => now(),
        ]);
    }

    public function requestRevision(Article $article, User $actor, ?string $note = null): void
    {
        $this->transition($article, $actor, WorkflowStatus::RevisionNeeded, 'request_article_revision', $note, [
            'current_reviewer_id' => $actor->id,
            'reviewed_at' => now(),
        ]);
    }

    public function approve(Article $article, User $actor, ?string $note = null): void
    {
        $this->transition($article, $actor, WorkflowStatus::Approved, 'approve_article', $note, [
            'current_reviewer_id' => $actor->id,
            'reviewed_at' => now(),
            'approved_at' => now(),
        ], [
            'approved_by' => $actor->id,
        ]);
    }

    public function publish(Article $article, User $actor, ?string $note = null): void
    {
        $this->transition($article, $actor, WorkflowStatus::Published, 'publish_article', $note, [
            'published_at' => now(),
        ], [
            'approved_by' => $article->approved_by ?: $actor->id,
            'published_at' => now(),
            'is_active' => true,
        ]);
    }

    public function archive(Article $article, User $actor, ?string $note = null): void
    {
        $this->transition($article, $actor, WorkflowStatus::Archived, 'archive_article', $note, [], [
            'is_active' => false,
        ]);
    }

    public function restoreArchive(Article $article, User $actor, ?string $note = null): void
    {
        $previousStatus = ApprovalLog::query()
            ->whereHas('approval', function ($query) use ($article): void {
                $query->where('approvable_type', $article->getMorphClass())
                    ->where('approvable_id', $article->id);
            })
            ->where('action_type', 'archive_article')
            ->latest()
            ->value('from_status');

        $toStatus = $previousStatus instanceof WorkflowStatus
            ? $previousStatus
            : WorkflowStatus::tryFrom((string) $previousStatus);

        if (! $toStatus || $toStatus === WorkflowStatus::Archived) {
            $toStatus = WorkflowStatus::Draft;
        }

        $this->transition($article, $actor, $toStatus, 'restore_article_archive', $note, [], [
            'is_active' => true,
        ]);
    }
    private function transition(
        Article $article,
        User $actor,
        WorkflowStatus $toStatus,
        string $actionType,
        ?string $note = null,
        array $approvalData = [],
        array $articleData = [],
    ): void {
        DB::transaction(function () use ($article, $actor, $toStatus, $actionType, $note, $approvalData, $articleData): void {
            $article->refresh();
            $fromStatus = $article->workflow_status;
            $approvalKey = [
                'approvable_type' => $article->getMorphClass(),
                'approvable_id' => $article->id,
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

            $article->update([
                ...$articleData,
                'workflow_status' => $toStatus,
                'updated_by' => $actor->id,
            ]);
        });
    }
}
