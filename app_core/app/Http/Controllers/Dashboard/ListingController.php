<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\Location;
use App\Models\ListingFieldValue;
use App\Models\Store;
use App\Http\Requests\StoreListingRequest;
use App\Services\ListingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Helpers\ImageHelper;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    public function __construct(private readonly ListingService $listingService) {}

    public function index(Request $request)
    {
        $listings = Listing::query()
            ->with(['category', 'store', 'images' => fn($q) => $q->orderBy('sort_order')->limit(1)])
            ->where('user_id', auth()->id())
            ->when($request->status && $request->status !== '', fn ($q) => $q->where('status', $request->status))
            ->when($request->q, fn ($q) => $q->where('title', 'like', '%'.$request->q.'%'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'total'      => $listings->total(),
                'rows'       => view('dashboard.listings._rows', compact('listings'))->render(),
                'pagination' => (string) $listings->links('vendor.pagination.dashboard'),
            ]);
        }

        $row = Listing::where('user_id', auth()->id())
            ->selectRaw("COUNT(*) as total, SUM(status='approved') as approved, SUM(status IN ('pending','draft')) as pending, SUM(is_featured=1) as featured")
            ->first();
        $stats = ['total' => (int)$row->total, 'approved' => (int)$row->approved, 'pending' => (int)$row->pending, 'featured' => (int)$row->featured];

        return view('dashboard.listings.index', compact('listings', 'stats'));
    }

    public function create()
    {
        $uid    = auth()->id();
        $stores = Cache::remember("sidebar_stores_{$uid}", 60, fn() =>
            Store::where('user_id', $uid)->select('id','name','slug','status','city','phone','whatsapp','address')->orderBy('name')->get()
        );
        $hasStore = $stores->isNotEmpty();

        if ($hasStore) {
            if ($redirect = $this->requirePhoneVerified()) return $redirect;
        } else {
            if ($redirect = $this->requireProfileForClassified()) return $redirect;
        }

        $categories = Cache::remember('dash_listing_categories', 600, fn() =>
            Category::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get()
        );
        $locations = Cache::remember('dash_locations', 3600, fn() =>
            \App\Models\Location::where('is_active', true)->orderBy('parent_id')->orderBy('name')->get()
        );
        $store = $stores->first();

        return view('dashboard.listings.create', compact('categories', 'store', 'stores', 'hasStore', 'locations'));
    }

    public function store(StoreListingRequest $request)
    {
        $uid      = auth()->id();
        $hasStore = Store::where('user_id', $uid)->exists();

        // Block store owners from submitting classified ads
        if ($hasStore && $request->input('type') === 'classified') {
            return redirect()->back()->with('error', 'Store owners must post ads through their store.');
        }

        if ($hasStore) {
            if ($redirect = $this->requirePhoneVerified()) return $redirect;
        } else {
            if ($redirect = $this->requireProfileForClassified()) return $redirect;
        }

        $user = auth()->user();
        $listing = $this->listingService->create($request, $request->validated(), $user);

        if ($user->role === 'user') {
            $user->role = 'seller';
            $user->save();
        }

        return redirect('/dashboard/listings')
            ->with('success', 'Listing submitted for admin approval.')
            ->with('listing_submitted', $listing->title);
    }

    public function show(Listing $listing)
    {
        $this->authorizeListing($listing);
        return redirect()->route('dashboard.listings.edit', $listing);
    }

    public function edit(Listing $listing)
    {
        $this->authorizeListing($listing);
        $listing->load(['images', 'values.field']);
        $categories = Cache::remember('dash_listing_categories', 600, fn() =>
            Category::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get()
        );
        $uid = auth()->id();
        $stores = Cache::remember("sidebar_stores_{$uid}", 60, fn() =>
            Store::where('user_id', $uid)->select('id','name','slug','status')->orderBy('name')->get()
        );

        $locations = Cache::remember('dash_locations', 3600, fn() =>
            \App\Models\Location::where('is_active', true)->orderBy('parent_id')->orderBy('name')->get()
        );

        return view('dashboard.listings.edit', compact('listing', 'categories', 'stores', 'locations'));
    }

    public function update(StoreListingRequest $request, Listing $listing)
    {
        $this->authorizeListing($listing);
        $this->listingService->update($request, $request->validated(), $listing);

        return redirect('/dashboard/listings')->with('success', 'Listing updated and sent for admin re-approval.');
    }

    public function destroy(Listing $listing)
    {
        $this->authorizeListing($listing);
        $this->listingService->delete($listing);

        return redirect('/dashboard/listings')->with('success', 'Listing deleted.');
    }

    public function markSold(Listing $listing)
    {
        $this->authorizeListing($listing);
        $listing->update(['status' => 'sold']);

        return redirect('/dashboard/listings')->with('success', '"'.$listing->title.'" marked as sold.');
    }

    public function renew(Listing $listing)
    {
        $this->authorizeListing($listing);
        $base = $listing->expires_at && $listing->expires_at->isFuture()
            ? $listing->expires_at
            : now();
        $listing->update(['expires_at' => $base->addDays(30)]);

        return redirect('/dashboard/listings')->with('success', '"'.$listing->title.'" renewed for 30 days.');
    }

    public function bump(Listing $listing)
    {
        $this->authorizeListing($listing);

        $lastBump = $listing->bumped_at;
        if ($lastBump && $lastBump->gt(now()->subHours(24))) {
            $next = $lastBump->addHours(24)->diffForHumans();
            return redirect('/dashboard/listings')->with('error', "You can boost this listing again {$next}.");
        }

        $listing->update(['bumped_at' => now()]);

        return redirect('/dashboard/listings')->with('success', '"'.$listing->title.'" has been boosted to the top!');
    }

    public function updateStock(Request $request, Listing $listing)
    {
        $this->authorizeListing($listing);
        $data = $request->validate(['stock' => 'nullable|integer|min:0|max:99999']);
        $listing->update(['stock' => $data['stock'] ?? null]);
        return response()->json(['ok' => true, 'stock' => $listing->stock]);
    }

    private function authorizeListing(Listing $listing): void
    {
        abort_if((int) $listing->user_id !== (int) auth()->id(), 403);
    }

    private function requirePhoneVerified(): ?\Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        if (empty($user->phone)) {
            return redirect()->route('dashboard.profile')
                ->with('warning', 'Please add your phone number before posting an ad.');
        }
        $isAdmin = in_array($user->role, ['admin', 'super_admin']);
        if (!$isAdmin && empty($user->phone_verified_at)) {
            return redirect('/phone/verify')
                ->with('warning', 'Please verify your phone number via SMS before posting an ad.');
        }
        return null;
    }

    private function requireProfileForClassified(): ?\Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        $isAdmin = in_array($user->role, ['admin', 'super_admin']);
        $missing = [];
        if (empty($user->phone)) {
            $missing[] = 'phone number';
        }
        if (empty($user->location_id)) {
            $missing[] = 'location';
        }
        if (!empty($missing)) {
            return redirect()->route('dashboard.profile')
                ->with('warning', 'Please add your ' . implode(' and ', $missing) . ' before posting an ad.');
        }
        if (!$isAdmin && empty($user->phone_verified_at)) {
            return redirect('/phone/verify')
                ->with('warning', 'Please verify your phone number via SMS before posting a classified ad.');
        }
        return null;
    }
}
