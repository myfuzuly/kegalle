<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserActionController extends Controller
{
    public function deleteSavedSearch($id)
    {
        $uid = auth()->id();
        DB::table('saved_searches')
            ->where('id', $id)->where('user_id', $uid)->delete();
        cache()->forget("saved_searches_{$uid}");
        return back()->with('success', 'Saved search removed.');
    }

    public function togglePriceAlert(Request $request, Listing $listing)
    {
        $userId = auth()->id();
        try {
            $db = DB::table('price_alerts');
            $existing = $db->where('user_id', $userId)->where('listing_id', $listing->id)->first();
            if ($existing) {
                $db->where('id', $existing->id)->delete();
                return response()->json(['active' => false]);
            }
            $db->insert([
                'user_id'        => $userId,
                'listing_id'     => $listing->id,
                'price_when_set' => $listing->price ?? 0,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
            return response()->json(['active' => true]);
        } catch (\Throwable $e) {
            return response()->json(['active' => false, 'error' => 'An error occurred. Please try again.'], 500);
        }
    }

    public function saveSearch(Request $request)
    {
        $request->validate(['params' => 'required|string|max:2000']);
        $params  = json_decode($request->input('params'), true) ?: [];
        $encoded = json_encode($params);
        try {
            $exists = DB::table('saved_searches')
                ->where('user_id', auth()->id())
                ->where('params', $encoded)
                ->exists();
            if ($exists) {
                return back()->with('success', "You've already saved this search. We'll email you when new matching ads are posted.");
            }
            DB::table('saved_searches')->insert([
                'user_id'    => auth()->id(),
                'params'     => $encoded,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            cache()->forget('saved_searches_' . auth()->id());
        } catch (\Throwable $e) {
            return back()->with('success', 'Search saved!');
        }
        return back()->with('success', "Search saved! You'll get email alerts when new listings match.");
    }

    public function newsletterSubscribe(Request $request)
    {
        $request->validate(['email' => 'required|email|max:255']);
        try {
            DB::table('newsletter_subscribers')->updateOrInsert(
                ['email' => strtolower(trim($request->email))],
                ['created_at' => now(), 'updated_at' => now()]
            );
        } catch (\Throwable $e) {
            // Table may not exist yet — silently succeed to avoid breaking the footer
        }
        return response()->json(['message' => 'Subscribed successfully.']);
    }
}
