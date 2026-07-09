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

class AdminListingController extends Controller
{
    public function index(Request $request)
    {
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
        $listings = Listing::with(['user', 'store', 'category', 'locationModel', 'images'])
            ->where('type', 'classified')
            ->where('status', 'approved')
            ->when($request->q, fn ($q) => $q->where('title', 'like', '%'.$request->q.'%'))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.classifieds.index', compact('listings'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        $stores = Store::orderBy('name')->get();
        $categories = Category::with('parent')->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $locations = Location::with('parent')->where('is_active', true)->orderBy('parent_id')->orderBy('name')->get();

        return view('admin.listings.create', compact('users', 'stores', 'categories', 'locations'));
    }

    public function store(Request $request)
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

    public function edit(Listing $listing)
    {
        $listing->load(['images', 'values.field']);
        $users = User::orderBy('name')->get();
        $stores = Store::orderBy('name')->get();
        $categories = Category::with('parent')->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $locations = Location::with('parent')->where('is_active', true)->orderBy('parent_id')->orderBy('name')->get();

        return view('admin.listings.edit', compact('listing', 'users', 'stores', 'categories', 'locations'));
    }

    public function update(Request $request, Listing $listing)
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

    public function approve(Listing $listing)
    {
        $listing->update(['status' => 'approved']);
        return back()->with('success', 'Listing approved.');
    }

    public function reject(Listing $listing)
    {
        $listing->update(['status' => 'rejected']);
        return back()->with('success', 'Listing rejected.');
    }

    public function feature(Listing $listing)
    {
        $listing->is_featured = ! (bool) $listing->is_featured;
        $listing->save();
        return back()->with('success', 'Featured status updated.');
    }

    public function top(Listing $listing)
    {
        $listing->is_top = ! (bool) $listing->is_top;
        $listing->save();
        return back()->with('success', 'Top ad status updated.');
    }

    public function destroy(Listing $listing)
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
}
