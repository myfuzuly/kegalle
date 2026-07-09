<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Store;

class ModerationController extends Controller
{
    public function approvals()
    {
        $pendingListings = Listing::with(['user', 'store', 'category', 'images'])
            ->where('status', 'pending')
            ->latest()->get();

        $pendingStores = Store::with('user')->withCount('listings')
            ->where('status', 'pending')
            ->latest()->get();

        return view('admin.approvals.index', compact('pendingListings', 'pendingStores'));
    }

    public function inactive()
    {
        $inactiveListings = Listing::with(['user', 'store', 'category', 'images'])
            ->whereIn('status', ['rejected', 'suspended', 'expired', 'inactive'])
            ->latest()->get();

        $inactiveStores = Store::with('user')->withCount('listings')
            ->whereIn('status', ['suspended', 'rejected', 'inactive'])
            ->latest()->get();

        return view('admin.inactive.index', compact('inactiveListings', 'inactiveStores'));
    }
}
