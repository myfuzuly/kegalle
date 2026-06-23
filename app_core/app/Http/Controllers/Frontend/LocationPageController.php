<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Location;

class LocationPageController extends Controller
{
    public function index()
    {
        $locations = Location::query()
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function ($location) {
                $location->listings_count = Listing::published()->where('location', $location->name)->count();

                return $location;
            });

        return view('frontend.locations.index', compact('locations'));
    }
}
