<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ChatThread;
use App\Models\Favorite;
use App\Models\Listing;
use App\Models\Category;
use App\Models\Store;
use App\Http\Requests\DashboardStoreRequest;
use App\Services\StoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function __construct(private readonly StoreService $storeService) {}

    public function index()
    {
        $stores = Store::query()
            ->where('user_id', auth()->id())
            ->withCount('listings')
            ->latest()
            ->get();

        return view('dashboard.stores.index', compact('stores'));
    }

    public function create()
    {
        $storeLimit = (int) (auth()->user()->store_limit ?? 1);
        if (! in_array(auth()->user()->role, ['admin', 'super_admin']) && Store::where('user_id', auth()->id())->count() >= $storeLimit) {
            return redirect()->route('dashboard.stores.index')->with('error', "You have reached your store limit ({$storeLimit}). Contact the administrator to increase your limit and manage multiple stores from a single dashboard.");
        }
        $locations = \App\Models\Location::where('is_active', 1)->orderBy('sort_order')->orderBy('name')->get();
        $categories = Category::whereNull('parent_id')->where('is_active', 1)->orderBy('sort_order')->orderBy('name')->with('children.children')->get();
        return view('dashboard.stores.form', [
            'store' => new Store,
            'mode' => 'create',
            'locations' => $locations,
            'categories' => $categories,
        ]);
    }

    public function store(DashboardStoreRequest $request)
    {
        $storeLimit = (int) (auth()->user()->store_limit ?? 1);
        if (! in_array(auth()->user()->role, ['admin', 'super_admin']) && Store::where('user_id', auth()->id())->count() >= $storeLimit) {
            return redirect()->route('dashboard.stores.index')->with('error', "You have reached your store limit ({$storeLimit}). Contact the administrator to increase your limit and manage multiple stores from a single dashboard.");
        }

        $user = auth()->user();
        $store = $this->storeService->create($request, $request->validated(), $user);

        if ($user->role === 'user') {
            $user->role = 'seller';
            $user->save();
        }

        return redirect()
            ->route('dashboard.stores.index')
            ->with('success', 'Store submitted for approval.');
    }

    public function show(Store $store)
    {
        $this->authorizeStore($store);

        $products = Listing::query()
            ->where('store_id', $store->id)
            ->with(['images', 'category:id,name,slug'])
            ->latest()
            ->take(8)
            ->get();

        $agg = Listing::where('store_id', $store->id)
            ->selectRaw("COUNT(*) as products, SUM(status='approved') as approved, SUM(status='pending') as pending" . (Schema::hasColumn('listings', 'views') ? ', SUM(views) as views' : ', 0 as views'))
            ->first();
        $stats = [
            'products' => (int) $agg->products,
            'approved' => (int) $agg->approved,
            'pending'  => (int) $agg->pending,
            'views'    => (int) $agg->views,
        ];

        return view('dashboard.stores.show', compact('store', 'products', 'stats'));
    }

    public function edit(Store $store)
    {
        $this->authorizeStore($store);
        $locations = \App\Models\Location::where('is_active', 1)->orderBy('sort_order')->orderBy('name')->get();
        $categories = Category::whereNull('parent_id')->where('is_active', 1)->orderBy('sort_order')->orderBy('name')->with('children.children')->get();

        return view('dashboard.stores.form', [
            'store' => $store,
            'mode' => 'edit',
            'locations' => $locations,
            'categories' => $categories,
        ]);
    }

    public function update(DashboardStoreRequest $request, Store $store)
    {
        $this->authorizeStore($store);

        $this->storeService->update($request, $request->validated(), $store);

        return redirect()
            ->route('dashboard.stores.show', $store)
            ->with('success', 'Store profile updated.');
    }

    public function analytics(Store $store)
    {
        $this->authorizeStore($store);

        $agg = Listing::where('store_id', $store->id)
            ->selectRaw("COUNT(*) as total, SUM(status='approved') as active, SUM(views) as views")
            ->first();

        $listingIds = Listing::where('store_id', $store->id)->pluck('id');

        $totalListings  = (int) $agg->total;
        $activeListings = (int) $agg->active;
        $totalViews     = (int) $agg->views;
        $totalFavorites = $listingIds->isNotEmpty()
            ? Favorite::whereIn('listing_id', $listingIds)->count()
            : 0;
        $totalInquiries = $listingIds->isNotEmpty()
            ? ChatThread::where('seller_id', $store->user_id)
                ->whereIn('listing_id', $listingIds)
                ->count()
            : 0;

        $topListings = Listing::where('store_id', $store->id)
            ->orderByDesc('views')
            ->take(5)
            ->get();

        // Attach favorite counts to top listings
        if ($topListings->isNotEmpty()) {
            $favCounts = Favorite::whereIn('listing_id', $topListings->pluck('id'))
                ->selectRaw('listing_id, count(*) as fav_count')
                ->groupBy('listing_id')
                ->pluck('fav_count', 'listing_id');
            foreach ($topListings as $listing) {
                $listing->fav_count = $favCounts[$listing->id] ?? 0;
            }
        }

        $statusCounts = Listing::where('store_id', $store->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $statusBreakdown = [
            'approved' => $statusCounts['approved'] ?? 0,
            'pending'  => $statusCounts['pending']  ?? 0,
            'rejected' => $statusCounts['rejected'] ?? 0,
        ];

        $recentActivity = Listing::where('store_id', $store->id)
            ->orderByDesc('updated_at')
            ->take(10)
            ->get();

        return view('dashboard.stores.analytics', compact(
            'store',
            'totalListings',
            'activeListings',
            'totalViews',
            'totalFavorites',
            'totalInquiries',
            'topListings',
            'statusBreakdown',
            'recentActivity'
        ));
    }

    public function reviews(Store $store)
    {
        $this->authorizeStore($store);

        $reviews = \App\Models\Review::where('store_id', $store->id)
            ->with('user')
            ->latest()
            ->paginate(20);

        $reviewAgg = \App\Models\Review::where('store_id', $store->id)
            ->selectRaw("COUNT(*) as total, SUM(status='approved') as approved, SUM(status='pending') as pending, AVG(CASE WHEN status='approved' THEN rating END) as avg_rating")
            ->first();
        $stats = [
            'total'    => (int) $reviewAgg->total,
            'approved' => (int) $reviewAgg->approved,
            'pending'  => (int) $reviewAgg->pending,
            'avg'      => round((float) $reviewAgg->avg_rating, 1),
        ];

        return view('dashboard.stores.reviews', compact('store', 'reviews', 'stats'));
    }

    public function replyReview(Request $request, Store $store, \App\Models\Review $review)
    {
        $this->authorizeStore($store);

        abort_if((int) $review->store_id !== (int) $store->id, 403);

        $data = $request->validate([
            'reply' => 'required|string|min:2|max:1000',
        ]);

        $review->update([
            'reply'      => $data['reply'],
            'replied_at' => now(),
        ]);

        return back()->with('success', 'Your reply has been posted.');
    }

    /**
     * Normalize Sri Lankan numbers: phone → +94XXXXXXXXX, whatsapp → 94XXXXXXXXX.
     */
    public static function normalizePhones(array $data): array
    {
        if (! empty($data['phone'])) {
            $data['phone'] = StoreService::normalizePhone($data['phone']);
        }
        if (! empty($data['whatsapp'])) {
            $data['whatsapp'] = StoreService::normalizeWhatsapp($data['whatsapp']);
        }
        return $data;
    }

    private function authorizeStore(Store $store): void
    {
        abort_if((int) $store->user_id !== (int) auth()->id(), 403);
    }
}
