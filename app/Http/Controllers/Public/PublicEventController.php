<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicEventController extends Controller
{
    public function index(Request $request): View
    {
        $today = now()->toDateString();
        $period = $request->string('period')->toString();

        $events = Event::query()
            ->published()
            ->with(['destination'])
            ->when($period === 'upcoming', function (Builder $query) use ($today): void {
                $query->where(function (Builder $query) use ($today): void {
                    $query->whereDate('start_date', '>=', $today)
                        ->orWhereDate('end_date', '>=', $today);
                });
            })
            ->when($period === 'past', function (Builder $query) use ($today): void {
                $query->where(function (Builder $query) use ($today): void {
                    $query->whereDate('end_date', '<', $today)
                        ->orWhere(function (Builder $query) use ($today): void {
                            $query->whereNull('end_date')
                                ->whereDate('start_date', '<', $today);
                        });
                });
            })
            ->when($period === 'past', fn (Builder $query) => $query->latest('start_date'), fn (Builder $query) => $query->orderBy('start_date'))
            ->paginate(9)
            ->withQueryString();

        return view('public.events.index', [
            'events' => $events,
            'period' => in_array($period, ['upcoming', 'past'], true) ? $period : '',
        ]);
    }

    public function show(string $slug): View
    {
        $event = Event::query()
            ->published()
            ->where('slug', $slug)
            ->with(['destination.category', 'destination.district'])
            ->firstOrFail();

        return view('public.events.show', [
            'event' => $event,
        ]);
    }
}

