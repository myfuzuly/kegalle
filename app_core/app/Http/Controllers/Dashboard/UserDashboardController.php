<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'listings' => Listing::where('user_id', $user->id)->count(),
            'approved' => Listing::where('user_id', $user->id)->where('status', 'approved')->count(),
            'pending' => Listing::where('user_id', $user->id)->where('status', 'pending')->count(),
            'stores' => Store::where('user_id', $user->id)->count(),
        ];

        $latestListings = Listing::where('user_id', $user->id)
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.index', compact('stats', 'latestListings'));
    }
}
