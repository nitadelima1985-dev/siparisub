<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\DestinationCategory;
use App\Models\District;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicMapController extends Controller
{
    public function index(Request $request): View
    {
        $destinations = Destination::query()
            ->published()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['category', 'district', 'coverMedia'])
            ->when($request->filled('category'), fn (Builder $query) => $query->where('destination_category_id', $request->integer('category')))
            ->when($request->filled('district'), fn (Builder $query) => $query->where('district_id', $request->integer('district')))
            ->orderBy('name')
            ->get();

        $markers = $destinations->map(fn (Destination $destination): array => [
            'id' => $destination->id,
            'name' => $destination->name,
            'category' => $destination->category?->name,
            'district' => $destination->district?->name,
            'latitude' => (float) $destination->latitude,
            'longitude' => (float) $destination->longitude,
            'cover_url' => $destination->coverMedia?->file_path
                ? asset('storage/'.$destination->coverMedia->file_path)
                : $destination->coverMedia?->external_url,
            'detail_url' => route('public.destinations.show', $destination->slug),
            'address' => $destination->address,
        ])->values();

        return view('public.map.index', [
            'destinations' => $destinations,
            'categories' => DestinationCategory::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'districts' => District::query()
                ->orderBy('name')
                ->get(),
            'markers' => $markers,
        ]);
    }
}