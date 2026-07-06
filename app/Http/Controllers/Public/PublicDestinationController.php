<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\DestinationCategory;
use App\Models\District;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicDestinationController extends Controller
{
    public function index(Request $request): View
    {
        $destinations = Destination::query()
            ->published()
            ->with(['category', 'district', 'coverMedia'])
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $search = $request->string('q')->toString();

                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category'), fn (Builder $query) => $query->where('destination_category_id', $request->integer('category')))
            ->when($request->filled('district'), fn (Builder $query) => $query->where('district_id', $request->integer('district')))
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('public.destinations.index', [
            'destinations' => $destinations,
            'categories' => DestinationCategory::query()->where('is_active', true)->orderBy('name')->get(),
            'districts' => District::query()->orderBy('name')->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $destination = Destination::query()
            ->published()
            ->where('slug', $slug)
            ->with([
                'category',
                'district',
                'coverMedia',
                'media' => fn ($query) => $query
                    ->where('media_type', 'image')
                    ->orderByDesc('is_cover')
                    ->orderBy('sort_order')
                    ->latest(),
            ])
            ->firstOrFail();

        return view('public.destinations.show', [
            'destination' => $destination,
        ]);
    }
}
