<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Location;

class LocationPageController extends Controller
{
    public function index()
    {
        [$parents, $childMap, $totalAds] = cache()->remember('locations_page_data', 600, function () {
            $counts = Listing::published()
                ->selectRaw('location_id, COUNT(*) as cnt')
                ->whereNotNull('location_id')
                ->groupBy('location_id')
                ->pluck('cnt', 'location_id');

            $all = Location::query()
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->each(function ($loc) use ($counts) {
                    $loc->listings_count = (int) ($counts[$loc->id] ?? 0);
                });

            $parents  = $all->whereNull('parent_id')->values();
            $childMap = $all->whereNotNull('parent_id')->groupBy('parent_id');

            // Sum child counts into parent
            $parents->each(function ($p) use ($childMap) {
                $p->total_count = $p->listings_count
                    + $childMap->get($p->id, collect())->sum('listings_count');
            });

            $totalAds = $parents->sum('total_count');

            return [$parents, $childMap, $totalAds];
        });

        return view('frontend.locations.index', compact('parents', 'childMap', 'totalAds'));
    }
}
