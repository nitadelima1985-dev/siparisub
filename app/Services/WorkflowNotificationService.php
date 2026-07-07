<?php

namespace App\Services;

use App\Enums\RoleCode;
use App\Enums\WorkflowStatus;
use App\Mail\WorkflowNotificationMail;
use App\Models\Article;
use App\Models\Destination;
use App\Models\Event;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Throwable;

class WorkflowNotificationService
{
    public function notify(Model $content, WorkflowStatus $status, string $actionType, ?string $note = null): void
    {
        $recipients = $this->recipients($content, $actionType);

        if ($recipients->isEmpty()) {
            return;
        }

        $contentType = $this->contentType($content);
        $contentName = $this->contentName($content);
        $statusLabel = $status->label();
        $title = $this->title($contentType, $status, $actionType);
        $message = $this->message($contentType, $contentName, $statusLabel, $actionType, $note);
        $actionUrl = $this->actionUrl($content);

        foreach ($recipients as $user) {
            try {
                if (Schema::hasTable('notifications')) {
                    Notification::create([
                        'user_id' => $user->id,
                        'type' => 'workflow',
                        'title' => $title,
                        'message' => $message,
                        'subject_type' => $content->getMorphClass(),
                        'subject_id' => $content->getKey(),
                        'content_type' => $contentType,
                        'status' => $status->value,
                        'action_url' => $actionUrl,
                        'data' => [
                            'action_type' => $actionType,
                            'content_name' => $contentName,
                            'status_label' => $statusLabel,
                            'note' => $note,
                        ],
                    ]);
                }

                if (! $this->canReceiveEmail($user)) {
                    continue;
                }

                $mail = new WorkflowNotificationMail(
                    title: $title,
                    contentType: $contentType,
                    contentName: $contentName,
                    status: $statusLabel,
                    message: $message,
                    actionUrl: $actionUrl,
                );

                if (config('queue.default') && config('queue.default') !== 'sync') {
                    Mail::to($user->email)->queue($mail);
                } else {
                    Mail::to($user->email)->send($mail);
                }
            } catch (Throwable $exception) {
                report($exception);
            }
        }
    }

    private function recipients(Model $content, string $actionType): Collection
    {
        $recipients = collect();

        if (str_starts_with($actionType, 'submit_')) {
            $recipients = User::query()
                ->where('is_active', true)
                ->whereHas('role', function ($query): void {
                    $query->whereIn('code', [
                        RoleCode::AdminDinas->value,
                        RoleCode::ReviewerAkademik->value,
                    ]);
                })
                ->get();
        }

        if (str_contains($actionType, '_revision')
            || str_starts_with($actionType, 'approve_')
            || str_starts_with($actionType, 'publish_')) {
            $creator = method_exists($content, 'creator') ? $content->creator()->first() : null;

            if ($creator) {
                $recipients->push($creator);
            }
        }

        return $recipients
            ->filter(fn (User $user): bool => (bool) $user->is_active)
            ->unique('id')
            ->values();
    }

    private function canReceiveEmail(User $user): bool
    {
        return $user->is_active
            && filled($user->email)
            && filter_var($user->email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function contentType(Model $content): string
    {
        return match (true) {
            $content instanceof Destination => 'Destinasi',
            $content instanceof Event => 'Event',
            $content instanceof Article => 'Artikel',
            default => class_basename($content),
        };
    }

    private function contentName(Model $content): string
    {
        return (string) ($content->name ?? $content->title ?? 'Konten SIPARISUB');
    }

    private function title(string $contentType, WorkflowStatus $status, string $actionType): string
    {
        return match (true) {
            str_starts_with($actionType, 'submit_') => "Pengajuan {$contentType} Baru",
            str_contains($actionType, '_revision') => "Revisi {$contentType} Diperlukan",
            str_starts_with($actionType, 'approve_') => "{$contentType} Disetujui",
            str_starts_with($actionType, 'publish_') => "{$contentType} Dipublikasikan",
            default => "Update Workflow {$contentType}: {$status->label()}",
        };
    }

    private function message(string $contentType, string $contentName, string $statusLabel, string $actionType, ?string $note): string
    {
        $baseMessage = match (true) {
            str_starts_with($actionType, 'submit_') => "{$contentType} \"{$contentName}\" telah disubmit dan menunggu review.",
            str_contains($actionType, '_revision') => "{$contentType} \"{$contentName}\" membutuhkan revisi.",
            str_starts_with($actionType, 'approve_') => "{$contentType} \"{$contentName}\" telah disetujui.",
            str_starts_with($actionType, 'publish_') => "{$contentType} \"{$contentName}\" telah dipublikasikan.",
            default => "Status {$contentType} \"{$contentName}\" berubah menjadi {$statusLabel}.",
        };

        return $note ? "{$baseMessage} Catatan: {$note}" : $baseMessage;
    }

    private function actionUrl(Model $content): ?string
    {
        return match (true) {
            $content instanceof Destination => route('dashboard.destinations.show', $content),
            $content instanceof Event => route('dashboard.events.show', $content),
            $content instanceof Article => route('dashboard.articles.show', $content),
            default => null,
        };
    }
}