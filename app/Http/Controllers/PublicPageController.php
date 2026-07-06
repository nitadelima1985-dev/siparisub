<?php

namespace App\Http\Controllers;

use App\Enums\RoleCode;
use App\Enums\WorkflowStatus;
use App\Models\Article;
use App\Models\Destination;
use App\Models\DestinationCategory;
use App\Models\District;
use App\Models\Event;
use App\Models\User;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function home(): View
    {
        $featuredDestinations = Destination::query()
            ->published()
            ->where('is_featured', true)
            ->with(['category', 'district', 'coverMedia'])
            ->orderByDesc('published_at')
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();

        if ($featuredDestinations->count() < 6) {
            $fallbackDestinations = Destination::query()
                ->published()
                ->whereNotIn('id', $featuredDestinations->pluck('id'))
                ->with(['category', 'district', 'coverMedia'])
                ->orderByDesc('published_at')
                ->orderByDesc('updated_at')
                ->limit(6 - $featuredDestinations->count())
                ->get();

            $featuredDestinations = $featuredDestinations
                ->merge($fallbackDestinations)
                ->take(6)
                ->values();
        }

        $destinationCount = Destination::query()
            ->where('workflow_status', WorkflowStatus::Published)
            ->where('is_active', true)
            ->count();

        $collaborationActorCount = User::query()
            ->where('is_active', true)
            ->whereHas('role', function ($query): void {
                $query->whereIn('code', array_map(
                    fn (RoleCode $role): string => $role->value,
                    RoleCode::cases(),
                ));
            })
            ->count();

        $yearlyEventCount = Event::query()
            ->where('workflow_status', WorkflowStatus::Published)
            ->where('is_active', true)
            ->whereYear('start_date', now()->year)
            ->count();

        $totalContent = Destination::query()->count()
            + Event::query()->count()
            + Article::query()->count();

        $publishedContent = Destination::query()
            ->where('workflow_status', WorkflowStatus::Published)
            ->count()
            + Event::query()
                ->where('workflow_status', WorkflowStatus::Published)
                ->count()
            + Article::query()
                ->where('workflow_status', WorkflowStatus::Published)
                ->count();

        $validatedContentPercentage = $totalContent > 0
            ? (int) round(($publishedContent / $totalContent) * 100)
            : 0;

        return view('public.home', [
            'categories' => DestinationCategory::query()->where('is_active', true)->orderBy('name')->get(),
            'districts' => District::query()->orderBy('name')->get(),
            'featuredDestinations' => $featuredDestinations,
            'destinationCount' => $destinationCount,
            'collaborationActorCount' => $collaborationActorCount,
            'yearlyEventCount' => $yearlyEventCount,
            'validatedContentPercentage' => $validatedContentPercentage,
        ]);
    }
}