<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Store;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'active_users' => User::where('status', 'active')->orWhereNull('status')->count(),
            'stores' => Store::count(),
            'approved_stores' => Store::whereIn('status', ['approved', 'active', 'published'])->count(),
            'pending_stores' => Store::where('status', 'pending')->count(),
            'listings' => Listing::count(),
            'approved_listings' => Listing::whereIn('status', ['approved', 'active', 'published'])->count(),
            'pending_listings' => Listing::where('status', 'pending')->count(),
            'products' => Listing::where('type', 'product')->count(),
            'classified' => Listing::where('type', 'classified')->count(),
            'categories' => Category::count(),
            'total_value' => Listing::whereIn('status', ['approved', 'active', 'published'])->sum('price'),
        ];

        $approvalQueue = Listing::with(['category', 'store', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->take(8)
            ->get();

        $pendingStores = Store::with('user')
            ->where('status', 'pending')
            ->latest()
            ->take(6)
            ->get();

        $topCategories = Category::withCount('listings')
            ->orderByDesc('listings_count')
            ->take(8)
            ->get();

        $latestListings = Listing::with(['category', 'store', 'user'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.index', compact(
            'stats',
            'approvalQueue',
            'pendingStores',
            'topCategories',
            'latestListings'
        ));
    }
}
