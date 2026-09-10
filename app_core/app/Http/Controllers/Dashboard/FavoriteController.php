<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Favorite::where('user_id', Auth::id())
            ->with(['listing.images', 'listing.store', 'listing.category', 'listing.locationModel'])
            ->latest()
            ->paginate(12);

        return view('dashboard.favorites.index', compact('favorites'));
    }

    public function toggle(Request $request, Listing $listing)
    {
        $favorite = Favorite::where('user_id', Auth::id())->where('listing_id', $listing->id)->first();

        if ($favorite) {
            $favorite->delete();
            $favorited = false;
        } else {
            Favorite::create([
                'user_id'      => Auth::id(),
                'listing_id'   => $listing->id,
                'price_at_save'=> $listing->price,
            ]);
            $favorited = true;
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['favorited' => $favorited]);
        }

        return back()->with('success', $favorited ? 'Added to favorites.' : 'Removed from favorites.');
    }
}
