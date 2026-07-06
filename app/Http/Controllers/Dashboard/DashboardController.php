<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\RoleCode;
use App\Enums\WorkflowStatus;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Destination;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load('role');
        $role = $user->roleCode();
        $dashboard = $this->dashboardFor($user, $role);

        return view('dashboard.index', [
            'user' => $user,
            'role' => $role,
            'menus' => $role?->dashboardMenus() ?? [],
            ...$dashboard,
        ]);
    }

    private function dashboardFor(User $user, ?RoleCode $role): array
    {
        return match ($role) {
            RoleCode::SuperAdmin, RoleCode::AdminDinas => $this->adminDashboard(),
            RoleCode::AdminPokdarwis => $this->pokdarwisDashboard($user),
            RoleCode::AdminHumas => $this->humasDashboard($user),
            RoleCode::KontenKreator => $this->creatorDashboard($user),
            RoleCode::ReviewerAkademik => $this->reviewerDashboard(),
            default => $this->emptyDashboard(),
        };
    }

    private function adminDashboard(): array
    {
        return [
            'stats' => [
                $this->stat('Total Destinasi', Destination::count(), 'fa-map-location-dot', 'text-bg-primary'),
                $this->stat('Total Event', Event::count(), 'fa-calendar-days', 'text-bg-info'),
                $this->stat('Total Artikel', Article::count(), 'fa-newspaper', 'text-bg-success'),
                $this->stat('Pending Review', $this->countByStatuses([WorkflowStatus::Submitted, WorkflowStatus::UnderReview]), 'fa-hourglass-half', 'text-bg-warning'),
                $this->stat('Revision Needed', $this->countByStatuses([WorkflowStatus::RevisionNeeded]), 'fa-pen-to-square', 'text-bg-danger'),
                $this->stat('Published', $this->countByStatuses([WorkflowStatus::Published]), 'fa-bullhorn', 'text-bg-dark'),
            ],
            'primaryTitle' => '5 Pengajuan Terbaru',
            'primaryItems' => $this->latestContent([WorkflowStatus::Submitted], null, 5),
            'secondaryTitle' => '5 Konten Perlu Diputuskan',
            'secondaryItems' => $this->latestContent([WorkflowStatus::Submitted, WorkflowStatus::UnderReview], null, 5),
        ];
    }

    private function pokdarwisDashboard(User $user): array
    {
        return [
            'stats' => [
                $this->stat('Destinasi Saya', Destination::where('created_by', $user->id)->count(), 'fa-map-location-dot', 'text-bg-primary'),
                $this->stat('Event Saya', Event::where('created_by', $user->id)->count(), 'fa-calendar-days', 'text-bg-info'),
                $this->stat('Draft', $this->countByStatuses([WorkflowStatus::Draft], $user), 'fa-file-lines', 'text-bg-secondary'),
                $this->stat('Submitted', $this->countByStatuses([WorkflowStatus::Submitted], $user), 'fa-paper-plane', 'text-bg-warning'),
                $this->stat('Revision Needed', $this->countByStatuses([WorkflowStatus::RevisionNeeded], $user), 'fa-pen-to-square', 'text-bg-danger'),
            ],
            'primaryTitle' => 'Revisi Terbaru',
            'primaryItems' => $this->latestContent([WorkflowStatus::RevisionNeeded], $user, 5),
            'secondaryTitle' => 'Pengajuan Saya Terbaru',
            'secondaryItems' => $this->latestContent([WorkflowStatus::Submitted, WorkflowStatus::UnderReview], $user, 5),
        ];
    }

    private function humasDashboard(User $user): array
    {
        return [
            'stats' => [
                $this->stat('Destinasi Saya', Destination::where('created_by', $user->id)->count(), 'fa-map-location-dot', 'text-bg-primary'),
                $this->stat('Event Saya', Event::where('created_by', $user->id)->count(), 'fa-calendar-days', 'text-bg-info'),
                $this->stat('Artikel Saya', Article::where('created_by', $user->id)->count(), 'fa-newspaper', 'text-bg-success'),
                $this->stat('Revisi Ditindaklanjuti', $this->countByStatuses([WorkflowStatus::RevisionNeeded], $user), 'fa-pen-to-square', 'text-bg-danger'),
            ],
            'primaryTitle' => 'Revisi yang Harus Ditindaklanjuti',
            'primaryItems' => $this->latestContent([WorkflowStatus::RevisionNeeded], $user, 5),
            'secondaryTitle' => 'Konten Saya Terbaru',
            'secondaryItems' => $this->latestContent(WorkflowStatus::cases(), $user, 5),
        ];
    }

    private function creatorDashboard(User $user): array
    {
        return [
            'stats' => [
                $this->stat('Artikel Saya', Article::where('created_by', $user->id)->count(), 'fa-newspaper', 'text-bg-success'),
                $this->stat('Artikel Draft', Article::where('created_by', $user->id)->where('workflow_status', WorkflowStatus::Draft)->count(), 'fa-file-lines', 'text-bg-secondary'),
                $this->stat('Artikel Submitted', Article::where('created_by', $user->id)->where('workflow_status', WorkflowStatus::Submitted)->count(), 'fa-paper-plane', 'text-bg-warning'),
                $this->stat('Artikel Revision Needed', Article::where('created_by', $user->id)->where('workflow_status', WorkflowStatus::RevisionNeeded)->count(), 'fa-pen-to-square', 'text-bg-danger'),
                $this->stat('Artikel Published', Article::where('created_by', $user->id)->where('workflow_status', WorkflowStatus::Published)->count(), 'fa-bullhorn', 'text-bg-dark'),
            ],
            'primaryTitle' => 'Artikel Revisi Terbaru',
            'primaryItems' => $this->latestArticles([WorkflowStatus::RevisionNeeded], $user, 5),
            'secondaryTitle' => 'Artikel Saya Terbaru',
            'secondaryItems' => $this->latestArticles(WorkflowStatus::cases(), $user, 5),
        ];
    }

    private function reviewerDashboard(): array
    {
        return [
            'stats' => [
                $this->stat('Submitted', $this->countByStatuses([WorkflowStatus::Submitted]), 'fa-paper-plane', 'text-bg-warning'),
                $this->stat('Under Review', $this->countByStatuses([WorkflowStatus::UnderReview]), 'fa-magnifying-glass', 'text-bg-info'),
                $this->stat('Revision Needed', $this->countByStatuses([WorkflowStatus::RevisionNeeded]), 'fa-pen-to-square', 'text-bg-danger'),
            ],
            'primaryTitle' => 'Konten Terbaru yang Perlu Direview',
            'primaryItems' => $this->latestContent([WorkflowStatus::Submitted, WorkflowStatus::UnderReview], null, 5),
            'secondaryTitle' => 'Catatan Revisi Terbaru',
            'secondaryItems' => $this->latestContent([WorkflowStatus::RevisionNeeded], null, 5),
        ];
    }

    private function emptyDashboard(): array
    {
        return [
            'stats' => [],
            'primaryTitle' => 'Aktivitas Terbaru',
            'primaryItems' => collect(),
            'secondaryTitle' => 'Informasi',
            'secondaryItems' => collect(),
        ];
    }

    private function stat(string $label, int $value, string $icon, string $variant): array
    {
        return compact('label', 'value', 'icon', 'variant');
    }

    private function countByStatuses(array $statuses, ?User $user = null): int
    {
        return Destination::query()->tap(fn ($query) => $this->applyStatusAndOwner($query, $statuses, $user))->count()
            + Event::query()->tap(fn ($query) => $this->applyStatusAndOwner($query, $statuses, $user))->count()
            + Article::query()->tap(fn ($query) => $this->applyStatusAndOwner($query, $statuses, $user))->count();
    }

    private function applyStatusAndOwner(Builder $query, array $statuses, ?User $user = null): void
    {
        $query->whereIn('workflow_status', array_map(fn (WorkflowStatus $status) => $status->value, $statuses));

        if ($user) {
            $query->where('created_by', $user->id);
        }
    }

    private function latestContent(array $statuses, ?User $user, int $limit): Collection
    {
        return collect([
            ...$this->latestDestinations($statuses, $user, $limit),
            ...$this->latestEvents($statuses, $user, $limit),
            ...$this->latestArticles($statuses, $user, $limit),
        ])->sortByDesc('updated_at')->take($limit)->values();
    }

    private function latestDestinations(array $statuses, ?User $user, int $limit): array
    {
        return Destination::query()
            ->with(['latestApproval', 'creator'])
            ->tap(fn ($query) => $this->applyStatusAndOwner($query, $statuses, $user))
            ->latest('updated_at')
            ->limit($limit)
            ->get()
            ->map(fn (Destination $destination) => $this->contentItem($destination, 'Destinasi', 'fa-map-location-dot', route('dashboard.destinations.show', $destination)))
            ->all();
    }

    private function latestEvents(array $statuses, ?User $user, int $limit): array
    {
        return Event::query()
            ->with(['latestApproval', 'creator'])
            ->tap(fn ($query) => $this->applyStatusAndOwner($query, $statuses, $user))
            ->latest('updated_at')
            ->limit($limit)
            ->get()
            ->map(fn (Event $event) => $this->contentItem($event, 'Event', 'fa-calendar-days', route('dashboard.events.show', $event)))
            ->all();
    }

    private function latestArticles(array $statuses, ?User $user, int $limit): array
    {
        return Article::query()
            ->with(['latestApproval', 'creator'])
            ->tap(fn ($query) => $this->applyStatusAndOwner($query, $statuses, $user))
            ->latest('updated_at')
            ->limit($limit)
            ->get()
            ->map(fn (Article $article) => $this->contentItem($article, 'Artikel', 'fa-newspaper', route('dashboard.articles.show', $article)))
            ->all();
    }

    private function contentItem(Model $model, string $type, string $icon, string $url): array
    {
        return [
            'title' => $model instanceof Article || $model instanceof Event ? $model->title : $model->name,
            'type' => $type,
            'icon' => $icon,
            'url' => $url,
            'status' => $model->workflow_status,
            'updated_at' => $model->updated_at,
            'creator' => $model->creator?->name,
            'note' => $model->latestApproval?->latest_note,
        ];
    }
}
