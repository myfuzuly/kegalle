<?php

namespace App\Services;

use App\Helpers\ImageHelper;
use App\Models\Brand;
use App\Models\BrandModel;
use App\Models\CustomField;
use App\Models\Listing;
use App\Models\ListingFieldValue;
use App\Models\ListingImage;
use App\Models\ListingVariant;
use App\Models\Location;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListingService
{
    public function generateSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 2;
        while (Listing::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    public function create(Request $request, array $validated, User $user): Listing
    {
        $store = $this->resolveStore($validated, $user);

        // Enforce: store owners always post as product; no-store users always as classified
        $type = $store ? 'product' : 'classified';

        $slug  = $this->generateSlug($validated['title']);
        $city  = $store?->city ?? 'Kegalle';
        $locationId = Location::where('name', $city)->where('is_active', 1)->value('id');

        $listing = Listing::create([
            'user_id'         => $user->id,
            'store_id'        => $store?->id,
            'category_id'     => $validated['category_id'] ?? null,
            'title'           => $validated['title'],
            'slug'            => $slug,
            'description'     => $validated['description'],
            'price'           => $validated['price'] ?? 0,
            'location'        => $city,
            'location_id'     => $locationId,
            'type'            => $type,
            'ad_type'         => $validated['ad_type'] ?? 'sale',
            'status'          => 'pending',
            'is_featured'     => false,
            'is_top'          => false,
            'payment_methods' => $request->input('payment_methods') ?: ['cash_on_pickup'],
        ]);

        $this->handleImages($request, $listing, $slug);
        $this->saveCustomFields($request, $listing);
        $this->saveVariants($request, $listing);
        $this->applyTitlePostfix($request, $listing);

        return $listing;
    }

    public function update(Request $request, array $validated, Listing $listing): Listing
    {
        $user = $request->user();

        if (! empty($validated['store_id'])) {
            $ownStore = Store::where('user_id', $user->id)
                ->where('id', $validated['store_id'])
                ->exists();
            if (! $ownStore) {
                unset($validated['store_id']);
            }
        }

        $resolvedStoreId = $validated['store_id'] ?? $listing->store_id;
        $type = $resolvedStoreId ? 'product' : 'classified';

        $listing->update([
            'title'           => $validated['title'],
            'category_id'     => $validated['category_id'] ?? $listing->category_id,
            'store_id'        => $resolvedStoreId,
            'type'            => $type,
            'price'           => $validated['price'] ?? 0,
            'description'     => $validated['description'],
            'payment_methods' => $request->input('payment_methods') ?: $listing->payment_methods,
            'status'          => 'pending',
        ]);

        $this->deleteImages($request, $listing);
        $this->handleImages($request, $listing, $listing->slug, append: true);
        $this->setPrimaryImage($request, $listing);
        $this->saveCustomFields($request, $listing);
        $this->saveVariants($request, $listing);
        $this->applyTitlePostfix($request, $listing);

        return $listing;
    }

    public function delete(Listing $listing): void
    {
        foreach ($listing->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
        $listing->delete();
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function resolveStore(array $validated, User $user): ?Store
    {
        if (! empty($validated['store_id'])) {
            return Store::where('user_id', $user->id)
                ->where('id', $validated['store_id'])
                ->first();
        }
        return Store::where('user_id', $user->id)->first();
    }

    private function handleImages(Request $request, Listing $listing, string $slug, bool $append = false): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $nextSort = $append
            ? ((int) ($listing->images()->max('sort_order') ?? -1)) + 1
            : 0;

        foreach ($request->file('images') as $index => $image) {
            $ext     = strtolower($image->getClientOriginalExtension()) ?: 'jpg';
            $seoName = ImageHelper::seoFilename($slug, $nextSort + $index, $ext);
            $path    = $image->storeAs('listings', $seoName, 'public');
            $path    = ImageHelper::finalize($path);

            ListingImage::create([
                'listing_id' => $listing->id,
                'path'       => $path,
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
        $deleteIds = $request->input('delete_images', []);
        if (! is_array($deleteIds) || empty($deleteIds)) {
            return;
        }

        $images = ListingImage::where('listing_id', $listing->id)
            ->whereIn('id', $deleteIds)
            ->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
    }

    public function saveCustomFields(Request $request, Listing $listing): void
    {
        if ($request->filled('cf_condition')) {
            $listing->update(['condition' => $request->input('cf_condition')]);
        }

        foreach ($request->all() as $key => $value) {
            if (! str_starts_with($key, 'cf_')) {
                continue;
            }
            $fieldName = substr($key, 3);
            $field = CustomField::where('name', $fieldName)->first();
            if (! $field) {
                continue;
            }
            $storeValue = is_array($value) ? implode(',', $value) : $value;
            if ($storeValue === '' || $storeValue === null) {
                continue;
            }
            ListingFieldValue::updateOrCreate(
                ['listing_id' => $listing->id, 'custom_field_id' => $field->id],
                ['value' => $storeValue]
            );
        }
    }

    public function saveVariants(Request $request, Listing $listing): void
    {
        $sizes = $request->input('variant_sizes', []);
        if (! is_array($sizes)) {
            return;
        }

        ListingVariant::where('listing_id', $listing->id)->delete();

        $prices = $request->input('variant_price', []);
        $order  = ['XS' => 0, 'S' => 1, 'M' => 2, 'L' => 3, 'XL' => 4, 'XXL' => 5];

        foreach ($sizes as $size) {
            $size = strtoupper(substr(trim($size), 0, 30));
            if ($size === '') {
                continue;
            }
            $price = isset($prices[$size]) && $prices[$size] !== '' ? (float) $prices[$size] : null;
            ListingVariant::create([
                'listing_id' => $listing->id,
                'name'       => $size,
                'price'      => $price,
                'sort_order' => $order[$size] ?? 9,
            ]);
        }
    }

    public function applyTitlePostfix(Request $request, Listing $listing): void
    {
        $parts = [];

        if ($request->filled('cf_brand_id')) {
            $brand = Brand::find($request->input('cf_brand_id'));
            if ($brand) {
                $parts[] = $brand->name;
            }
        }
        if ($request->filled('cf_model_id')) {
            $model = BrandModel::find($request->input('cf_model_id'));
            if ($model) {
                $parts[] = $model->name;
            }
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
}
