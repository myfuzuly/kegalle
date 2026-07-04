<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ChatThread;
use App\Models\Favorite;
use App\Models\Listing;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreController extends Controller
{
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
            return redirect()->route('dashboard.stores.index')->with('success', "You have reached your store limit ({$storeLimit}). Contact admin to increase it.");
        }
        $locations = \App\Models\Location::where('is_active', 1)->orderBy('sort_order')->orderBy('name')->get();
        return view('dashboard.stores.form', [
            'store' => new Store,
            'mode' => 'create',
            'locations' => $locations,
        ]);
    }

    public function store(Request $request)
    {
        $storeLimit = (int) (auth()->user()->store_limit ?? 1);
        if (! in_array(auth()->user()->role, ['admin', 'super_admin']) && Store::where('user_id', auth()->id())->count() >= $storeLimit) {
            return redirect()->route('dashboard.stores.index')->with('success', "You have reached your store limit ({$storeLimit}). Contact admin to increase it.");
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:190'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        unset($data['logo'], $data['banner']);
        $data = self::normalizePhones($data);

        $data['user_id'] = auth()->id();
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['status'] = 'pending';

        if ($request->hasFile('logo')) {
            $ext = strtolower($request->file('logo')->getClientOriginalExtension()) ?: 'jpg';
            $data['logo'] = $request->file('logo')->storeAs('stores', \Illuminate\Support\Str::slug($data['name']) . '-logo-in-kegalle.' . $ext, 'public');
            $data['logo'] = \App\Helpers\ImageHelper::finalize($data['logo'], false, 500);
        }
        if ($request->hasFile('banner')) {
            $ext = strtolower($request->file('banner')->getClientOriginalExtension()) ?: 'jpg';
            $data['banner'] = $request->file('banner')->storeAs('stores', \Illuminate\Support\Str::slug($data['name']) . '-banner-in-kegalle.' . $ext, 'public');
            $data['banner'] = \App\Helpers\ImageHelper::finalize($data['banner'], false, 1600);
        }

        Store::create($data);

        return redirect()
            ->route('dashboard.stores.index')
            ->with('success', 'Store submitted for approval.');
    }

    public function show(Store $store)
    {
        $this->authorizeStore($store);

        $products = Listing::query()
            ->where('store_id', $store->id)
            ->latest()
            ->take(8)
            ->get();

        $stats = [
            'products' => Listing::where('store_id', $store->id)->count(),
            'approved' => Listing::where('store_id', $store->id)->where('status', 'approved')->count(),
            'pending' => Listing::where('store_id', $store->id)->where('status', 'pending')->count(),
            'views' => Schema::hasColumn('listings', 'views')
                ? Listing::where('store_id', $store->id)->sum('views')
                : 0,
        ];

        return view('dashboard.stores.show', compact('store', 'products', 'stats'));
    }

    public function edit(Store $store)
    {
        $this->authorizeStore($store);
        $locations = \App\Models\Location::where('is_active', 1)->orderBy('sort_order')->orderBy('name')->get();

        return view('dashboard.stores.form', [
            'store' => $store,
            'mode' => 'edit',
            'locations' => $locations,
        ]);
    }

    public function update(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:190'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_banner' => ['nullable', 'boolean'],
        ]);

        unset($data['logo'], $data['banner'], $data['remove_logo'], $data['remove_banner']);
        $data = self::normalizePhones($data);

        if ($store->name !== $data['name']) {
            $data['slug'] = $this->uniqueSlug($data['name'], $store->id);
        }

        $storeSlug = \Illuminate\Support\Str::slug($data['name'] ?? $store->name);
        if ($request->hasFile('logo')) {
            if ($store->logo) {
                Storage::disk('public')->delete($store->logo);
            }
            $ext = strtolower($request->file('logo')->getClientOriginalExtension()) ?: 'jpg';
            $data['logo'] = $request->file('logo')->storeAs('stores', $storeSlug . '-logo-in-kegalle.' . $ext, 'public');
            $data['logo'] = \App\Helpers\ImageHelper::finalize($data['logo'], false, 500);
        } elseif ($request->boolean('remove_logo') && $store->logo) {
            Storage::disk('public')->delete($store->logo);
            $data['logo'] = null;
        }

        if ($request->hasFile('banner')) {
            if ($store->banner) {
                Storage::disk('public')->delete($store->banner);
            }
            $ext = strtolower($request->file('banner')->getClientOriginalExtension()) ?: 'jpg';
            $data['banner'] = $request->file('banner')->storeAs('stores', $storeSlug . '-banner-in-kegalle.' . $ext, 'public');
            $data['banner'] = \App\Helpers\ImageHelper::finalize($data['banner'], false, 1600);
        } elseif ($request->boolean('remove_banner') && $store->banner) {
            Storage::disk('public')->delete($store->banner);
            $data['banner'] = null;
        }

        $store->update($data);

        return redirect()
            ->route('dashboard.stores.show', $store)
            ->with('success', 'Store profile updated.');
    }

    public function analytics(Store $store)
    {
        $this->authorizeStore($store);

        $listingIds = Listing::where('store_id', $store->id)->pluck('id');

        $totalListings = $listingIds->count();
        $activeListings = Listing::where('store_id', $store->id)->where('status', 'approved')->count();
        $totalViews = Schema::hasColumn('listings', 'views')
            ? Listing::where('store_id', $store->id)->sum('views')
            : 0;
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

        $statusBreakdown = [
            'approved' => Listing::where('store_id', $store->id)->where('status', 'approved')->count(),
            'pending'  => Listing::where('store_id', $store->id)->where('status', 'pending')->count(),
            'rejected' => Listing::where('store_id', $store->id)->where('status', 'rejected')->count(),
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

        return view('dashboard.stores.reviews', compact('store'));
    }

    /**
     * Normalize Sri Lankan numbers: phone → +94XXXXXXXXX, whatsapp → 94XXXXXXXXX.
     */
    public static function normalizePhones(array $data): array
    {
        if (!empty($data['phone'])) {
            $digits = preg_replace('/\D/', '', $data['phone']);
            if (str_starts_with($digits, '0')) $digits = '94' . substr($digits, 1);
            if (!str_starts_with($digits, '94')) $digits = '94' . $digits;
            $data['phone'] = '+' . $digits;
        }
        if (!empty($data['whatsapp'])) {
            $digits = preg_replace('/\D/', '', $data['whatsapp']);
            if (str_starts_with($digits, '0')) $digits = '94' . substr($digits, 1);
            if (!str_starts_with($digits, '94')) $digits = '94' . $digits;
            $data['whatsapp'] = $digits;
        }
        return $data;
    }

    private function authorizeStore(Store $store): void
    {
        abort_if((int) $store->user_id !== (int) auth()->id(), 403);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            Store::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
