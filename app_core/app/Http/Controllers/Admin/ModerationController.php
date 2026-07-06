<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\Location;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ModerationController extends Controller
{
    public function listings(Request $request)
    {
        // Only approved ads here — pending live in Approvals, rejected/suspended in Danger Zone
        $listings = Listing::with(['user', 'store', 'category', 'locationModel', 'images'])
            ->where('type', '!=', 'classified')
            ->where('status', 'approved')
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->q, fn ($q) => $q->where('title', 'like', '%'.$request->q.'%'))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.listings.index', compact('listings'));
    }

    public function classifieds(Request $request)
    {
        // Only approved ads here — pending live in Approvals, rejected/suspended in Danger Zone
        $listings = Listing::with(['user', 'store', 'category', 'locationModel', 'images'])
            ->where('type', 'classified')
            ->where('status', 'approved')
            ->when($request->q, fn ($q) => $q->where('title', 'like', '%'.$request->q.'%'))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.classifieds.index', compact('listings'));
    }

    public function createListing()
    {
        $users = User::orderBy('name')->get();
        $stores = Store::orderBy('name')->get();
        $categories = Category::with('parent')->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $locations = Location::with('parent')->where('is_active', true)->orderBy('parent_id')->orderBy('name')->get();

        return view('admin.listings.create', compact('users', 'stores', 'categories', 'locations'));
    }

    public function storeListing(Request $request)
    {
        $data = $this->validateListing($request);
        $this->validateListingImages($request);
        $payload = $this->listingPayload($request, $data);
        if (Schema::hasColumn('listings', 'created_by_admin_id')) {
            $payload['created_by_admin_id'] = auth()->id();
        }
        $listing = Listing::create($payload);
        $this->storeListingImages($request, $listing);
        $this->saveCustomFields($request, $listing);
        $this->saveVariants($request, $listing);
        $this->applyTitlePostfix($request, $listing);

        return redirect('/admin/listings')->with('success', 'Listing posted successfully by Super Admin.');
    }

    public function editListing(Listing $listing)
    {
        $listing->load(['images', 'values.field']);
        $users = User::orderBy('name')->get();
        $stores = Store::orderBy('name')->get();
        $categories = Category::with('parent')->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $locations = Location::with('parent')->where('is_active', true)->orderBy('parent_id')->orderBy('name')->get();

        return view('admin.listings.edit', compact('listing', 'users', 'stores', 'categories', 'locations'));
    }

    public function updateListing(Request $request, Listing $listing)
    {
        $data = $this->validateListing($request, $listing->id);
        $this->validateListingImages($request);
        $listing->update($this->listingPayload($request, $data));
        $this->deleteListingImages($request, $listing);
        $this->storeListingImages($request, $listing);
        $this->saveCustomFields($request, $listing);
        $this->saveVariants($request, $listing);
        $this->applyTitlePostfix($request, $listing);

        return redirect('/admin/listings')->with('success', 'Listing updated successfully.');
    }

    private function validateListingImages(Request $request): void
    {
        $request->validate([
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }

    private function storeListingImages(Request $request, Listing $listing): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $cols = Schema::getColumnListing('listing_images');
        $nextSort = ((int) ($listing->images()->max('sort_order') ?? -1)) + 1;
        $hasExisting = $listing->images()->exists();

        foreach ($request->file('images') as $index => $image) {
            if (! $image || ! $image->isValid()) {
                continue;
            }

            $ext = strtolower($image->getClientOriginalExtension()) ?: 'jpg';
            $seoName = \App\Helpers\ImageHelper::seoFilename($listing->slug, $nextSort + $index, $ext);
            $path = $image->storeAs('listings', $seoName, 'public');
            $path = \App\Helpers\ImageHelper::finalize($path);

            $row = ['listing_id' => $listing->id, 'path' => $path];
            if (in_array('sort_order', $cols)) {
                $row['sort_order'] = $nextSort++;
            }
            if (in_array('is_primary', $cols)) {
                $row['is_primary'] = ! $hasExisting && $index === 0;
            }
            ListingImage::create($row);
            $hasExisting = true;
        }
    }

    private function deleteListingImages(Request $request, Listing $listing): void
    {
        $ids = $request->input('delete_images', []);
        if (! is_array($ids) || empty($ids)) {
            return;
        }

        $images = ListingImage::where('listing_id', $listing->id)->whereIn('id', $ids)->get();
        foreach ($images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
    }

    private function validateListing(Request $request, $ignoreId = null): array
    {
        $slugRule = $ignoreId
            ? ['nullable', 'string', 'max:220', Rule::unique('listings', 'slug')->ignore($ignoreId)]
            : 'nullable|string|max:220|unique:listings,slug';

        return $request->validate([
            'user_id' => 'required|exists:users,id',
            'store_id' => 'nullable|exists:stores,id',
            'category_id' => 'nullable|exists:categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'title' => 'required|string|max:180',
            'slug' => $slugRule,
            'price' => 'nullable|numeric|min:0',
            'type' => 'required|string|max:50',
            'status' => 'required|string|max:50',
            'location' => 'nullable|string|max:180',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'is_top' => 'nullable|boolean',
        ]);
    }

    private function listingPayload(Request $request, array $data): array
    {
        $payload = [
            'user_id' => $data['user_id'],
            'store_id' => $data['store_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'title' => $data['title'],
            'slug' => ! empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['title'].'-'.uniqid()),
            'price' => $data['price'] ?? 0,
            'type' => $data['type'],
            'status' => $data['status'],
            'location' => $data['location'] ?? null,
            'description' => $data['description'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_top' => $request->boolean('is_top'),
        ];
        if (Schema::hasColumn('listings', 'location_id')) {
            $payload['location_id'] = $data['location_id'] ?? null;
        }

        return $payload;
    }

    /**
     * Sync size/price variants (clothing) from variant_sizes[] + variant_price[size].
     */
    private function saveVariants(Request $request, Listing $listing): void
    {
        $sizes = $request->input('variant_sizes', []);
        if (! is_array($sizes)) return;

        \App\Models\ListingVariant::where('listing_id', $listing->id)->delete();

        $prices = $request->input('variant_price', []);
        $order = ['XS' => 0, 'S' => 1, 'M' => 2, 'L' => 3, 'XL' => 4, 'XXL' => 5];
        foreach ($sizes as $size) {
            $size = strtoupper(substr(trim($size), 0, 30));
            if ($size === '') continue;
            $price = isset($prices[$size]) && $prices[$size] !== '' ? (float) $prices[$size] : null;
            \App\Models\ListingVariant::create([
                'listing_id' => $listing->id,
                'name' => $size,
                'price' => $price,
                'sort_order' => $order[$size] ?? 9,
            ]);
        }
    }

    /**
     * Append brand + model to the listing title (skipping words already present).
     */
    private function applyTitlePostfix(Request $request, Listing $listing): void
    {
        $parts = [];

        if ($request->filled('cf_brand_id')) {
            $brand = \App\Models\Brand::find($request->input('cf_brand_id'));
            if ($brand) $parts[] = $brand->name;
        }
        if ($request->filled('cf_model_id')) {
            $model = \App\Models\BrandModel::find($request->input('cf_model_id'));
            if ($model) $parts[] = $model->name;
        }

        $missing = array_filter($parts, fn ($p) => stripos($listing->title, $p) === false);
        if (empty($missing)) {
            return;
        }

        $newTitle = $listing->title . ' — ' . implode(' ', $missing);
        if (mb_strlen($newTitle) <= 180) {
            $listing->update(['title' => $newTitle]);
        }
    }

    private function saveCustomFields(Request $request, Listing $listing): void
    {
        // Condition maps to the listings.condition column
        if ($request->filled('cf_condition')) {
            $listing->update(['condition' => $request->input('cf_condition')]);
        }

        foreach ($request->all() as $key => $value) {
            if (!str_starts_with($key, 'cf_')) continue;
            $fieldName = substr($key, 3);
            $field = \App\Models\CustomField::where('name', $fieldName)->first();
            if (!$field) continue;
            $storeValue = is_array($value) ? implode(',', $value) : $value;
            if ($storeValue === '' || $storeValue === null) {
                \App\Models\ListingFieldValue::where('listing_id', $listing->id)->where('custom_field_id', $field->id)->delete();
                continue;
            }
            \App\Models\ListingFieldValue::updateOrCreate(
                ['listing_id' => $listing->id, 'custom_field_id' => $field->id],
                ['value' => $storeValue]
            );
        }
    }

    public function createStore()
    {
        $users = User::orderBy('name')->get();
        $locations = \App\Models\Location::where('is_active', 1)->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.stores.create', compact('users', 'locations'));
    }

    public function storeStore(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:190',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:190',
            'whatsapp' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:120',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'nullable|string|max:30',
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        unset($data['logo'], $data['banner']);
        $data = \App\Http\Controllers\Dashboard\StoreController::normalizePhones($data);
        $data['slug'] = \Illuminate\Support\Str::slug($data['name'] . '-' . uniqid());
        $data['is_featured'] = $request->boolean('is_featured');

        $storeSlug = \Illuminate\Support\Str::slug($data['name']);
        if ($request->hasFile('logo')) {
            $ext = strtolower($request->file('logo')->getClientOriginalExtension()) ?: 'jpg';
            $data['logo'] = $request->file('logo')->storeAs('stores', $storeSlug . '-logo-in-kegalle.' . $ext, 'public');
            $data['logo'] = \App\Helpers\ImageHelper::finalize($data['logo'], false, 500);
        }
        if ($request->hasFile('banner')) {
            $ext = strtolower($request->file('banner')->getClientOriginalExtension()) ?: 'jpg';
            $data['banner'] = $request->file('banner')->storeAs('stores', $storeSlug . '-banner-in-kegalle.' . $ext, 'public');
            $data['banner'] = \App\Helpers\ImageHelper::finalize($data['banner'], false, 1600);
        }

        Store::create($data);

        return redirect('/admin/stores')->with('success', 'Store created successfully.');
    }

    public function stores(Request $request)
    {
        // Only approved stores here — pending live in Approvals, suspended/rejected in Danger Zone
        $stores = Store::with('user')->withCount('listings')
            ->whereIn('status', ['approved', 'active', 'published'])
            ->when($request->featured === '1', fn ($q) => $q->where('is_featured', 1))
            ->when($request->featured === '0', fn ($q) => $q->where('is_featured', 0))
            ->when($request->q, fn ($q) => $q->where('name', 'like', '%'.$request->q.'%'))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.stores.index', compact('stores'));
    }

    public function approveStore(Store $store)
    {
        $store->update(['status' => 'approved']);

        return back()->with('success', 'Store approved.');
    }

    public function suspendStore(Store $store)
    {
        $store->update(['status' => 'suspended']);

        return back()->with('success', 'Store suspended.');
    }

    public function featureStore(Store $store)
    {
        $store->is_featured = ! (bool) $store->is_featured;
        $store->save();

        return back()->with('success', 'Store featured status updated.');
    }

    public function verifyStore(Store $store)
    {
        $store->is_verified = ! (bool) $store->is_verified;
        $store->save();

        $label = $store->is_verified ? 'verified' : 'unverified';
        return back()->with('success', "Store marked as {$label}.");
    }

    public function editStore(Store $store)
    {
        $users = User::orderBy('name')->get();
        $locations = \App\Models\Location::where('is_active', 1)->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.stores.edit', compact('store', 'users', 'locations'));
    }

    public function updateStore(Request $request, Store $store)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:190',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:190',
            'whatsapp' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:120',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_logo' => 'nullable|boolean',
            'remove_banner' => 'nullable|boolean',
        ]);

        unset($data['logo'], $data['banner'], $data['remove_logo'], $data['remove_banner']);
        $data = \App\Http\Controllers\Dashboard\StoreController::normalizePhones($data);

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

        return redirect('/admin/stores')->with('success', 'Store updated successfully.');
    }

    public function transferStore(Request $request, Store $store)
    {
        $data = $request->validate([
            'new_owner_id' => 'required|exists:users,id|different:current_owner_id',
        ], [
            'new_owner_id.different' => 'The new owner must be different from the current owner.',
        ]);

        $oldOwner = $store->user;
        $newOwner = User::findOrFail($data['new_owner_id']);

        $request->merge(['current_owner_id' => $store->user_id]);

        $store->user_id = $newOwner->id;
        $store->save();

        $listingsTransferred = Listing::where('store_id', $store->id)->update(['user_id' => $newOwner->id]);

        $msg = "Store \"{$store->name}\" transferred to {$newOwner->name} ({$newOwner->email}).";
        if ($listingsTransferred > 0) {
            $msg .= " {$listingsTransferred} listing(s) also transferred.";
        }

        return redirect('/admin/stores/' . $store->id . '/edit')->with('success', $msg);
    }

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

    public function destroyListing(Listing $listing)
    {
        foreach ($listing->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
        \App\Models\ListingFieldValue::where('listing_id', $listing->id)->delete();
        $title = $listing->title;
        $listing->delete();

        return back()->with('success', "Listing \"{$title}\" permanently deleted.");
    }

    public function destroyStore(Store $store)
    {
        $listingCount = $store->listings()->count();
        if ($listingCount > 0) {
            return back()->with('success', "Store \"{$store->name}\" still has {$listingCount} listing(s). Delete or transfer them first.");
        }

        if ($store->logo) Storage::disk('public')->delete($store->logo);
        if ($store->banner) Storage::disk('public')->delete($store->banner);
        $name = $store->name;
        $store->delete();

        return back()->with('success', "Store \"{$name}\" permanently deleted.");
    }

    public function approveListing(Listing $listing)
    {
        $listing->update(['status' => 'approved']);

        return back()->with('success', 'Listing approved.');
    }

    public function rejectListing(Listing $listing)
    {
        $listing->update(['status' => 'rejected']);

        return back()->with('success', 'Listing rejected.');
    }

    public function featureListing(Listing $listing)
    {
        $listing->is_featured = ! (bool) $listing->is_featured;
        $listing->save();

        return back()->with('success', 'Featured status updated.');
    }

    public function topListing(Listing $listing)
    {
        $listing->is_top = ! (bool) $listing->is_top;
        $listing->save();

        return back()->with('success','Top ad status updated.');
    }
}
