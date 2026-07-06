<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\OrganizationType;
use App\Enums\WorkflowStatus;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Destination;
use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless($user && $user->hasRole('super_admin', 'admin_dinas'), 403);

        $destinationByCategory = Destination::query()
            ->join('destination_categories', 'destinations.destination_category_id', '=', 'destination_categories.id')
            ->selectRaw('destination_categories.name as label, count(*) as total')
            ->groupBy('destination_categories.name')
            ->orderBy('destination_categories.name')
            ->get();

        $destinationByDistrict = Destination::query()
            ->join('districts', 'destinations.district_id', '=', 'districts.id')
            ->selectRaw('districts.name as label, count(*) as total')
            ->groupBy('districts.name')
            ->orderByDesc('total')
            ->get();

        $eventByMonth = Event::query()
            ->selectRaw("DATE_FORMAT(start_date, '%Y-%m') as label, count(*) as total")
            ->whereNotNull('start_date')
            ->groupBy('label')
            ->orderBy('label')
            ->limit(12)
            ->get();

        $articleByCategory = Article::query()
            ->join('article_categories', 'articles.article_category_id', '=', 'article_categories.id')
            ->selectRaw('article_categories.name as label, count(*) as total')
            ->groupBy('article_categories.name')
            ->orderBy('article_categories.name')
            ->get();

        $workflowCounts = collect(WorkflowStatus::cases())->map(fn (WorkflowStatus $status): array => [
            'label' => $status->label(),
            'total' => $this->workflowTotal($status),
        ]);

        $userByRole = User::query()
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->selectRaw('roles.name as label, count(*) as total')
            ->groupBy('roles.name')
            ->orderBy('roles.name')
            ->get();

        $organizationByType = Organization::query()
            ->selectRaw('organization_type as label, count(*) as total')
            ->groupBy('organization_type')
            ->orderBy('organization_type')
            ->get()
            ->map(fn ($row): array => [
                'label' => OrganizationType::tryFrom($row->label)?->label() ?? $row->label,
                'total' => (int) $row->total,
            ]);

        $staleDestinations = Destination::query()
            ->with(['category', 'district', 'creator'])
            ->where(function (Builder $query): void {
                $query->whereNull('last_content_update_at')
                    ->orWhere('last_content_update_at', '<', now()->subDays(90));
            })
            ->latest('updated_at')
            ->limit(10)
            ->get();

        $pendingContents = $this->contentQueue([WorkflowStatus::Submitted, WorkflowStatus::UnderReview], 10);
        $revisionNeededContents = $this->contentQueue([WorkflowStatus::RevisionNeeded], 10);
        $activeUsers = $this->activeUsers();

        return view('dashboard.reports.index', [
            'summaryCards' => [
                ['label' => 'Destinasi', 'value' => Destination::count(), 'icon' => 'fa-map-location-dot', 'color' => 'success'],
                ['label' => 'Event', 'value' => Event::count(), 'icon' => 'fa-calendar-days', 'color' => 'primary'],
                ['label' => 'Artikel', 'value' => Article::count(), 'icon' => 'fa-newspaper', 'color' => 'warning'],
                ['label' => 'Pending Review', 'value' => $pendingContents->count(), 'icon' => 'fa-clock', 'color' => 'danger'],
            ],
            'destinationByCategory' => $destinationByCategory,
            'destinationByDistrict' => $destinationByDistrict,
            'eventByMonth' => $eventByMonth,
            'articleByCategory' => $articleByCategory,
            'workflowCounts' => $workflowCounts,
            'userByRole' => $userByRole,
            'organizationByType' => $organizationByType,
            'staleDestinations' => $staleDestinations,
            'pendingContents' => $pendingContents,
            'revisionNeededContents' => $revisionNeededContents,
            'activeUsers' => $activeUsers,
            'chartData' => [
                'destinationByCategory' => $this->chartData($destinationByCategory),
                'destinationByDistrict' => $this->chartData($destinationByDistrict),
                'eventByMonth' => $this->chartData($eventByMonth),
                'articleByCategory' => $this->chartData($articleByCategory),
                'workflowCounts' => $this->chartData($workflowCounts),
                'userByRole' => $this->chartData($userByRole),
                'organizationByType' => $this->chartData($organizationByType),
            ],
        ]);
    }

    private function workflowTotal(WorkflowStatus $status): int
    {
        return Destination::query()->where('workflow_status', $status)->count()
            + Event::query()->where('workflow_status', $status)->count()
            + Article::query()->where('workflow_status', $status)->count();
    }

    private function contentQueue(array $statuses, int $limit)
    {
        $statusValues = collect($statuses)->map(fn (WorkflowStatus $status) => $status->value)->all();

        return collect()
            ->merge($this->queueRows(Destination::class, 'Destinasi', 'name', $statusValues, $limit))
            ->merge($this->queueRows(Event::class, 'Event', 'title', $statusValues, $limit))
            ->merge($this->queueRows(Article::class, 'Artikel', 'title', $statusValues, $limit))
            ->sortByDesc('updated_at')
            ->take($limit)
            ->values();
    }

    private function queueRows(string $modelClass, string $type, string $titleColumn, array $statusValues, int $limit)
    {
        return $modelClass::query()
            ->with('creator')
            ->whereIn('workflow_status', $statusValues)
            ->latest('updated_at')
            ->limit($limit)
            ->get()
            ->map(fn ($item): array => [
                'type' => $type,
                'title' => $item->{$titleColumn},
                'status' => $item->workflow_status?->label() ?? (string) $item->workflow_status,
                'creator' => $item->creator?->name ?? '-',
                'updated_at' => $item->updated_at,
            ]);
    }

    private function activeUsers()
    {
        $destinationCounts = Destination::query()
            ->selectRaw('created_by as user_id, count(*) as total')
            ->whereNotNull('created_by')
            ->groupBy('created_by');

        $eventCounts = Event::query()
            ->selectRaw('created_by as user_id, count(*) as total')
            ->whereNotNull('created_by')
            ->groupBy('created_by');

        $articleCounts = Article::query()
            ->selectRaw('created_by as user_id, count(*) as total')
            ->whereNotNull('created_by')
            ->groupBy('created_by');

        $contentTotals = DB::query()
            ->fromSub($destinationCounts->unionAll($eventCounts)->unionAll($articleCounts), 'content_counts')
            ->selectRaw('user_id, sum(total) as total_content')
            ->groupBy('user_id');

        return User::query()
            ->joinSub($contentTotals, 'content_totals', 'users.id', '=', 'content_totals.user_id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.id', 'users.name', 'users.email', 'roles.name as role_name', 'content_totals.total_content')
            ->orderByDesc('content_totals.total_content')
            ->limit(10)
            ->get();
    }

    private function chartData($rows): array
    {
        return [
            'labels' => collect($rows)->pluck('label')->values(),
            'values' => collect($rows)->pluck('total')->map(fn ($value): int => (int) $value)->values(),
        ];
    }
}

