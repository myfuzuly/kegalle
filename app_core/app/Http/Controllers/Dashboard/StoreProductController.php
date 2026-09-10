<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreProductController extends Controller
{
    private function ownedStore($storeId): Store
    {
        if (Auth::user()->isAdmin()) {
            return Store::findOrFail($storeId);
        }
        return Store::where('id', $storeId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    /** Allow if admin, or listing belongs to the user, or listing's store belongs to the user */
    private function authorizeProduct(Listing $listing): void
    {
        if (Auth::user()->isAdmin()) return;
        if ((int) $listing->user_id === (int) Auth::id()) return;

        $owned = Store::where('user_id', Auth::id())->pluck('id');
        abort_if(! $owned->contains($listing->store_id), 403);
    }

    public function index($store)
    {
        $store    = $this->ownedStore($store);
        $products = Listing::where('store_id', $store->id)
            ->with(['category:id,name,slug', 'images'])
            ->latest()->paginate(20);

        return view('dashboard.stores.products.index', compact('store', 'products'));
    }

    public function create($store)
    {
        $store = $this->ownedStore($store);
        if (empty(Auth::user()->phone_verified_at)) {
            return redirect('/phone/verify')
                ->with('warning', 'Please verify your phone number via SMS before adding products to your store.');
        }
        $categories = Category::where('is_active', 1)->orderBy('name')->get();
        $locations  = \App\Models\Location::where('is_active', true)->orderBy('parent_id')->orderBy('name')->get();

        return view('dashboard.stores.products.create', compact('store', 'categories', 'locations'));
    }

    public function store(Request $request, $store)
    {
        $store = $this->ownedStore($store);
        if (empty(Auth::user()->phone_verified_at)) {
            return redirect('/phone/verify')
                ->with('warning', 'Please verify your phone number via SMS before adding products to your store.');
        }

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:190'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'price'       => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:5000'],
            'location'    => ['nullable', 'string', 'max:190'],
            'images'      => ['nullable', 'array', 'max:10'],
            'images.*'    => ['image', 'mime_types:image/jpeg,image/png,image/webp', 'max:4096'],
        ]);

        $data['user_id']  = Auth::id();
        $data['store_id'] = $store->id;
        $data['type']     = 'product';
        $data['status']   = 'pending';
        $data['slug']     = Str::slug($data['title']) . '-' . time();

        $listing = Listing::create($data);

        $this->handleImages($request, $listing);

        return redirect()
            ->route('dashboard.stores.show', $store)
            ->with('success', 'Product submitted for approval.');
    }

    public function edit($store, Listing $product)
    {
        $this->authorizeProduct($product);

        $product->load('images');
        $categories = Category::where('is_active', 1)->orderBy('name')->get();
        $store = Store::findOrFail($store);
        $listing = $product; // alias for view

        $locations = \App\Models\Location::where('is_active', true)->orderBy('parent_id')->orderBy('name')->get();

        return view('dashboard.stores.products.edit', compact('store', 'listing', 'categories', 'locations'));
    }

    public function update(Request $request, $store, Listing $product)
    {
        $this->authorizeProduct($product);
        $listing = $product;

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:190'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'price'       => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:5000'],
            'location'    => ['nullable', 'string', 'max:190'],
            'images'      => ['nullable', 'array', 'max:10'],
            'images.*'    => ['image', 'mime_types:image/jpeg,image/png,image/webp', 'max:4096'],
        ]);

        $data['status'] = 'pending';
        $listing->update($data);

        $this->deleteImages($request, $listing);
        $this->handleImages($request, $listing, append: true);
        $this->setPrimaryImage($request, $listing);

        return redirect()
            ->route('dashboard.stores.products.index', $store)
            ->with('success', 'Product updated and submitted for review.');
    }

    // ── Image helpers ────────────────────────────────────────────────

    private function handleImages(Request $request, Listing $listing, bool $append = false): void
    {
        if (! $request->hasFile('images')) return;

        $nextSort = $append
            ? ((int) ($listing->images()->max('sort_order') ?? -1)) + 1
            : 0;

        foreach ($request->file('images') as $index => $file) {
            $ext     = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
            $seoName = ImageHelper::seoFilename($listing->slug, $nextSort + $index, $ext);
            $path    = $file->storeAs('listings', $seoName, 'public');
            $path    = ImageHelper::finalize($path);

            ListingImage::create([
                'listing_id' => $listing->id,
                'path'       => $path,
                'is_primary' => (! $append && $index === (int) $request->input('new_primary_index', 0) && $listing->images()->count() <= 1),
                'sort_order' => $nextSort + $index,
            ]);
        }
    }

    private function setPrimaryImage(Request $request, Listing $listing): void
    {
        $primaryId = (int) $request->input('primary_image_id');
        if (! $primaryId) return;

        ListingImage::where('listing_id', $listing->id)->update(['is_primary' => false]);
        ListingImage::where('id', $primaryId)->where('listing_id', $listing->id)->update(['is_primary' => true]);
    }

    private function deleteImages(Request $request, Listing $listing): void
    {
        $ids = $request->input('delete_images', []);
        if (empty($ids)) return;

        $images = ListingImage::where('listing_id', $listing->id)->whereIn('id', $ids)->get();
        foreach ($images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
    }
}
