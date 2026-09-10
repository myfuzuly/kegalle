<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Location;
use App\Models\Store;
use App\Models\Event;

class TownController extends Controller
{
    public function index()
    {
        $towns = cache()->remember('towns_index_list', 600, fn() =>
            Location::where('is_active', 1)
                ->withCount(['listings' => fn ($q) => $q->where('status', 'approved')])
                ->orderBy('name')
                ->get()
        );

        return view('frontend.towns', compact('towns'));
    }

    public function show(string $slug)
    {
        $town = Location::where('slug', $slug)->where('is_active', 1)->firstOrFail();

        $listings = Listing::published()
            ->where('location_id', $town->id)
            ->with(['images', 'store', 'category'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $stores = Store::whereIn('status', ['approved', 'active', 'published'])
            ->where(function ($q) use ($town) {
                $q->where('city', 'like', '%' . $town->name . '%')
                  ->orWhere('address', 'like', '%' . $town->name . '%');
            })
            ->withCount('listings')
            ->take(8)
            ->get();

        $events = collect();
        try {
            $events = Event::upcoming()
                ->where('location', 'like', '%' . $town->name . '%')
                ->orderBy('event_date')
                ->take(6)
                ->get();
        } catch (\Throwable $e) {}

        $allTowns = Location::where('is_active', 1)
            ->where('id', '!=', $town->id)
            ->orderBy('name')
            ->get(['name', 'slug']);

        return view('frontend.town', compact('town', 'listings', 'stores', 'events', 'allTowns'));
    }
}
