<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Listing;
use App\Models\Service;
use App\Models\Store;

class ModerationController extends Controller
{
    public function approvals()
    {
        $pendingListings = Listing::with(['user', 'store', 'category', 'images'])
            ->where('status', 'pending')
            ->latest()->paginate(50, ['*'], 'listings_page');

        $pendingStores = Store::with('user')->withCount('listings')
            ->where('status', 'pending')
            ->latest()->paginate(50, ['*'], 'stores_page');

        $pendingDeals = Deal::with(['listing.images', 'listing.category', 'store', 'user'])
            ->where('status', 'pending')
            ->latest()->paginate(50, ['*'], 'deals_page');

        $pendingServices = Service::with(['user', 'category'])
            ->where('status', 'pending')
            ->latest()->paginate(50, ['*'], 'services_page');

        return view('admin.approvals.index', compact('pendingListings', 'pendingStores', 'pendingDeals', 'pendingServices'));
    }

    public function inactive()
    {
        $inactiveListings = Listing::with(['user', 'store', 'category', 'images'])
            ->whereIn('status', ['rejected', 'suspended', 'expired', 'inactive'])
            ->latest()->paginate(50, ['*'], 'listings_page');

        $inactiveStores = Store::with('user')->withCount('listings')
            ->whereIn('status', ['suspended', 'rejected', 'inactive'])
            ->latest()->paginate(50, ['*'], 'stores_page');

        return view('admin.inactive.index', compact('inactiveListings', 'inactiveStores'));
    }
}
