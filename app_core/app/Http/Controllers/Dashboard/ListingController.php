<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\Location;
use App\Models\ListingFieldValue;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Helpers\ImageHelper;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $listings = Listing::query()
            ->with(['category', 'store'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        $stats = [
            'total' => Listing::where('user_id', auth()->id())->count(),
            'approved' => Listing::where('user_id', auth()->id())->where('status', 'approved')->count(),
            'pending' => Listing::where('user_id', auth()->id())->whereIn('status', ['pending', 'draft'])->count(),
            'featured' => Listing::where('user_id', auth()->id())->where('is_featured', 1)->count(),
        ];

        return view('dashboard.listings.index', compact('listings', 'stats'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $stores = Store::where('user_id', auth()->id())->orderBy('name')->get();
        $store = $stores->first();

        return view('dashboard.listings.create', compact('categories', 'store', 'stores'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'store_id' => ['nullable', 'exists:stores,id'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['required', 'string', 'min:5'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        // Only allow posting to the user's own stores
        if (! empty($data['store_id'])) {
            $store = Store::where('user_id', auth()->id())->where('id', $data['store_id'])->first();
        } else {
            $store = Store::where('user_id', auth()->id())->first();
        }

        $slugBase = Str::slug($data['title']);
        $slug = $slugBase;
        $i = 2;
        while (Listing::where('slug', $slug)->exists()) {
            $slug = $slugBase.'-'.$i++;
        }

        $listing = Listing::create([
            'user_id' => auth()->id(),
            'store_id' => $store?->id,
            'category_id' => $data['category_id'] ?? null,
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'],
            'price' => $data['price'] ?? 0,
            'location' => $store?->city ?? 'Kegalle',
            'type' => 'product',
            'status' => 'pending',
            'is_featured' => false,
            'is_top' => false,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $ext = strtolower($image->getClientOriginalExtension()) ?: 'jpg';
                $seoName = ImageHelper::seoFilename($slug, $index, $ext);
                $path = $image->storeAs('listings', $seoName, 'public');
                $path = ImageHelper::finalize($path);

                if (class_exists('App\\Models\\ListingImage')) {
                    ListingImage::create([
                        'listing_id' => $listing->id,
                        'path' => $path,
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        // Save custom field values
        $this->saveCustomFields($request, $listing);
        $this->saveVariants($request, $listing);
        $this->applyTitlePostfix($request, $listing);

        return redirect('/dashboard/listings')
            ->with('success', 'Listing submitted for admin approval.')
            ->with('listing_submitted', $listing->title);
    }

    public function edit(Listing $listing)
    {
        $this->authorizeListing($listing);
        $listing->load(['images', 'values.field']);
        $categories = Category::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $stores = Store::where('user_id', auth()->id())->orderBy('name')->get();

        return view('dashboard.listings.edit', compact('listing', 'categories', 'stores'));
    }

    public function update(Request $request, Listing $listing)
    {
        $this->authorizeListing($listing);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'store_id' => ['nullable', 'exists:stores,id'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['required', 'string', 'min:5'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if (! empty($data['store_id'])) {
            $ownStore = Store::where('user_id', auth()->id())->where('id', $data['store_id'])->exists();
            if (! $ownStore) {
                unset($data['store_id']);
            }
        }

        $listing->update([
            'title' => $data['title'],
            'category_id' => $data['category_id'] ?? $listing->category_id,
            'store_id' => $data['store_id'] ?? $listing->store_id,
            'price' => $data['price'] ?? 0,
            'description' => $data['description'],
            'status' => 'pending',
        ]);

        // Remove selected images
        $deleteIds = $request->input('delete_images', []);
        if (is_array($deleteIds) && ! empty($deleteIds)) {
            $images = ListingImage::where('listing_id', $listing->id)->whereIn('id', $deleteIds)->get();
            foreach ($images as $image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($image->path);
                $image->delete();
            }
        }

        // Add new images
        if ($request->hasFile('images')) {
            $nextSort = ((int) ($listing->images()->max('sort_order') ?? -1)) + 1;
            foreach ($request->file('images') as $index => $image) {
                $ext = strtolower($image->getClientOriginalExtension()) ?: 'jpg';
                $seoName = ImageHelper::seoFilename($listing->slug, $nextSort + $index, $ext);
                $path = $image->storeAs('listings', $seoName, 'public');
                $path = ImageHelper::finalize($path);

                ListingImage::create([
                    'listing_id' => $listing->id,
                    'path' => $path,
                    'sort_order' => $nextSort + $index,
                ]);
            }
        }

        $this->saveCustomFields($request, $listing);
        $this->saveVariants($request, $listing);
        $this->applyTitlePostfix($request, $listing);

        return redirect('/dashboard/listings')->with('success', 'Listing updated and sent for admin re-approval.');
    }

    public function destroy(Listing $listing)
    {
        $this->authorizeListing($listing);

        foreach ($listing->images as $image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($image->path);
            $image->delete();
        }
        $listing->delete();

        return redirect('/dashboard/listings')->with('success', 'Listing deleted.');
    }

    private function authorizeListing(Listing $listing): void
    {
        abort_if((int) $listing->user_id !== (int) auth()->id(), 403);
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
            if ($storeValue === '' || $storeValue === null) continue;
            ListingFieldValue::updateOrCreate(
                ['listing_id' => $listing->id, 'custom_field_id' => $field->id],
                ['value' => $storeValue]
            );
        }
    }
}
