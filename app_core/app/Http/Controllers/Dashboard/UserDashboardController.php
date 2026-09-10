<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\MembershipPlan;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $uid  = $user->id;

        // Single-store users go straight to their store dashboard
        $firstStore = Store::where('user_id', $uid)->first();
        if ($firstStore && !Store::where('user_id', $uid)->where('id', '!=', $firstStore->id)->exists()) {
            return redirect("/dashboard/stores/{$firstStore->id}");
        }

        [$stats, $latestListings, $emptyStores, $store, $savedSearches] = Cache::remember("dashboard_data_{$uid}", 60, function () use ($uid) {
            $counts = Listing::where('user_id', $uid)
                ->selectRaw("COUNT(*) as total, SUM(status='approved') as approved, SUM(status='pending') as pending")
                ->first();

            $offerCount = 0;
            try {
                $offerCount = DB::table('offers')->where('seller_id', $uid)->count();
            } catch (\Throwable) {}

            $stats = [
                'listings' => (int) $counts->total,
                'approved' => (int) $counts->approved,
                'pending'  => (int) $counts->pending,
                'stores'   => Store::where('user_id', $uid)->count(),
                'offers'   => $offerCount,
            ];

            $latestListings = Listing::where('user_id', $uid)
                ->with(['category', 'store', 'images'])
                ->latest()->take(6)->get();

            $emptyStores = Store::where('user_id', $uid)
                ->where('status', 'approved')
                ->withCount('listings')
                ->having('listings_count', 0)
                ->get();

            $store = Store::where('user_id', $uid)->first();

            $savedSearches = collect();
            try {
                $savedSearches = \Illuminate\Support\Facades\DB::table('saved_searches')
                    ->where('user_id', $uid)
                    ->orderByDesc('created_at')->limit(4)->get();
            } catch (\Throwable) {}

            return [$stats, $latestListings, $emptyStores, $store, $savedSearches];
        });
        $isStoreUser = ($user->account_type === 'store');
        $onboarding = array_filter([
            'account_created'  => true,
            'profile_complete' => !empty($user->phone) && !empty($user->name),
            'store_created'    => $isStoreUser ? $stats['stores'] > 0 : null,
            'store_with_logo'  => $isStoreUser ? ($store && !empty($store->logo)) : null,
            'first_listing'    => $stats['listings'] > 0,
            'first_approved'   => $stats['approved'] > 0,
        ], fn ($v) => $v !== null);
        $onboardingDone = count(array_filter($onboarding));
        $onboardingTotal = count($onboarding);
        $showOnboarding = $onboardingDone < $onboardingTotal;

        $profileIncomplete = empty($user->phone) || empty($user->location_id) || empty($user->avatar);
        $isFirstLogin = session('first_login', false);

        // Current membership plan slug for the dashboard badge
        $currentSlug = null;
        try {
            $memberStore = $user->stores()->with('membershipPlan')->first();
            if ($memberStore && $memberStore->membership_plan_id && $memberStore->membership_expires_at?->isFuture()) {
                $currentSlug = $memberStore->membershipPlan?->slug;
            }
        } catch (\Throwable) {}

        return view('dashboard.index', compact('stats', 'latestListings', 'emptyStores', 'savedSearches', 'onboarding', 'onboardingDone', 'onboardingTotal', 'showOnboarding', 'profileIncomplete', 'isFirstLogin', 'currentSlug'));
    }
}
