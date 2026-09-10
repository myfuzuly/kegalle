<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\Location;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\ListingStatusMail;
use Illuminate\Support\Facades\Mail;
use App\Services\MobitelSmsService;
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
            ->when(
                $request->status && $request->status !== '',
                fn ($q) => $q->where('status', $request->status),
                fn ($q) => $q->where('status', 'approved')
            )
            ->when($request->type && $request->type !== '', fn ($q) => $q->where('type', $request->type))
            ->when($request->q, fn ($q) => $q->where('title', 'like', '%'.$request->q.'%'))
            ->latest()->paginate(20)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'total'      => $listings->total(),
                'rows'       => view('admin.listings._rows', compact('listings'))->render(),
                'pagination' => (string) $listings->links('vendor.pagination.ka-admin'),
            ]);
        }

        return view('admin.listings.index', compact('listings'));
    }

    public function classifieds(Request $request)
    {
        $base = Listing::with(['user', 'store', 'category', 'locationModel', 'images'])
            ->where('type', 'classified')
            ->when($request->q, fn ($q) => $q->where('title', 'like', '%'.$request->q.'%'));

        $listings = (clone $base)
            ->when(
                $request->status && $request->status !== '',
                fn ($q) => $q->where('status', $request->status),
                fn ($q) => $q->whereIn('status', ['approved', 'pending'])
            )
            ->latest()->paginate(20)->withQueryString();

        $dangerListings = (clone $base)
            ->whereIn('status', ['rejected', 'suspended', 'expired'])
            ->latest()->limit(100)->get();

        if ($request->ajax()) {
            return response()->json([
                'total'      => $listings->total(),
                'rows'       => view('admin.classifieds._rows', compact('listings'))->render(),
                'pagination' => (string) $listings->links('vendor.pagination.ka-admin'),
            ]);
        }

        return view('admin.classifieds.index', compact('listings', 'dangerListings'));
    }

    public function createClassified()
    {
        $categories = Category::with('parent')->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $locations  = Location::with('parent')->where('is_active', true)->orderBy('parent_id')->orderBy('name')->get();
        $brands     = Brand::active()->orderBy('name')->get(['id', 'name']);

        return view('admin.classifieds.create', compact('categories', 'locations', 'brands'));
    }

    public function storeClassified(Request $request)
    {
        $data = $request->validate([
            'poster_name'      => 'required|string|max:120',
            'poster_phone'     => 'required|string|max:30',
            'poster_whatsapp'  => 'nullable|string|max:30',
            'category_id'      => 'nullable|exists:categories,id',
            'location_id'      => 'nullable|exists:locations,id',
            'brand_id'         => 'nullable|exists:brands,id',
            'title'            => 'required|string|max:180',
            'price'            => 'nullable|numeric|min:0',
            'location'         => 'nullable|string|max:180',
            'description'      => 'nullable|string',
            'description_si'   => 'nullable|string',
            'description_ta'   => 'nullable|string',
            'is_featured'      => 'nullable|boolean',
            'is_top'           => 'nullable|boolean',
            'images.*'         => 'nullable|image|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        $payload = [
            'user_id'         => null,
            'poster_name'     => $data['poster_name'],
            'poster_phone'    => $data['poster_phone'],
            'poster_whatsapp' => $data['poster_whatsapp'] ?? null,
            'category_id'     => $data['category_id'] ?? null,
            'title'           => $data['title'],
            'slug'            => $this->uniqueSlug($data['title']),
            'price'           => $data['price'] ?? 0,
            'type'            => 'classified',
            'status'          => 'approved',
            'location'        => $data['location'] ?? null,
            'description'     => $data['description'] ?? null,
            'description_si'  => $data['description_si'] ?? null,
            'description_ta'  => $data['description_ta'] ?? null,
            'is_featured'     => $request->boolean('is_featured'),
            'is_top'          => $request->boolean('is_top'),
        ];

        if (Schema::hasColumn('listings', 'location_id')) {
            $payload['location_id'] = $data['location_id'] ?? null;
        }
        if (Schema::hasColumn('listings', 'created_by_admin_id')) {
            $payload['created_by_admin_id'] = auth()->id();
        }

        $listing = Listing::create($payload);
        $this->storeListingImages($request, $listing);
        $this->saveClassifiedBrand($listing, $data['brand_id'] ?? null);
        $this->saveCustomFields($request, $listing);

        return redirect('/admin/classifieds')->with('success', 'Classified ad posted successfully.');
    }

    public function editClassified(Listing $listing)
    {
        $listing->load(['images', 'values.field']);
        $categories = Category::with('parent')->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $locations  = Location::with('parent')->where('is_active', true)->orderBy('parent_id')->orderBy('name')->get();
        $currentBrandId = $listing->values->first(fn($v) => optional($v->field)->name === 'brand_id')?->value;

        return view('admin.classifieds.edit', compact('listing', 'categories', 'locations', 'currentBrandId'));
    }

    public function updateClassified(Request $request, Listing $listing)
    {
        $data = $request->validate([
            'poster_name'      => 'required|string|max:120',
            'poster_phone'     => 'required|string|max:30',
            'poster_whatsapp'  => 'nullable|string|max:30',
            'category_id'      => 'nullable|exists:categories,id',
            'location_id'      => 'nullable|exists:locations,id',
            'title'            => 'required|string|max:180',
            'price'            => 'nullable|numeric|min:0',
            'location'         => 'nullable|string|max:180',
            'description'      => 'nullable|string',
            'description_si'   => 'nullable|string',
            'description_ta'   => 'nullable|string',
            'status'           => 'required|string|max:50',
            'brand_id'         => 'nullable|exists:brands,id',
            'is_featured'      => 'nullable|boolean',
            'is_top'           => 'nullable|boolean',
            'images.*'         => 'nullable|image|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        $payload = [
            'poster_name'     => $data['poster_name'],
            'poster_phone'    => $data['poster_phone'],
            'poster_whatsapp' => $data['poster_whatsapp'] ?? null,
            'category_id'     => $data['category_id'] ?? null,
            'title'           => $data['title'],
            'price'           => $data['price'] ?? 0,
            'status'          => $data['status'],
            'location'        => $data['location'] ?? null,
            'description'     => $data['description'] ?? null,
            'description_si'  => $data['description_si'] ?? null,
            'description_ta'  => $data['description_ta'] ?? null,
            'is_featured'     => $request->boolean('is_featured'),
            'is_top'          => $request->boolean('is_top'),
        ];

        if (Schema::hasColumn('listings', 'location_id')) {
            $payload['location_id'] = $data['location_id'] ?? null;
        }

        $listing->update($payload);
        $this->deleteListingImages($request, $listing);
        $this->storeListingImages($request, $listing);
        $this->setPrimaryImage($request, $listing);
        $this->saveClassifiedBrand($listing, $data['brand_id'] ?? null);
        $this->saveCustomFields($request, $listing);

        return redirect('/admin/classifieds')->with('success', 'Classified ad updated successfully.');
    }

    public function create()
    {
        $users = cache()->remember('admin_users_dropdown', 120, fn () => User::orderBy('name')->limit(500)->get(['id', 'name', 'email']));
        $stores = cache()->remember('admin_stores_dropdown', 120, fn () => Store::orderBy('name')->limit(500)->get(['id', 'name']));
        $categories = Category::with('parent')->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $locations = Location::with('parent')->where('is_active', true)->orderBy('parent_id')->orderBy('name')->get();
        $brands = Brand::active()->orderBy('name')->get(['id', 'name']);

        return view('admin.listings.create', compact('users', 'stores', 'categories', 'locations', 'brands'));
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
        $users = cache()->remember('admin_users_dropdown', 120, fn () => User::orderBy('name')->limit(500)->get(['id', 'name', 'email']));
        $stores = cache()->remember('admin_stores_dropdown', 120, fn () => Store::orderBy('name')->limit(500)->get(['id', 'name']));
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
        $this->setPrimaryImage($request, $listing);
        $this->saveCustomFields($request, $listing);
        $this->saveVariants($request, $listing);
        $this->applyTitlePostfix($request, $listing);

        return redirect('/admin/listings')->with('success', 'Listing updated successfully.');
    }

    public function approve(Listing $listing)
    {
        $listing->update(['status' => 'approved']);
        $listing->load('user');

        $email = optional($listing->user)->email;
        if ($email) {
            try { Mail::to($email)->queue(new ListingStatusMail($listing, true)); } catch (\Throwable $e) { \Log::warning('Approve mail failed: ' . $e->getMessage()); }
        }

        if ($listing->user_id) {
            try {
                app(\App\Services\WebPushService::class)->notifyUser(
                    $listing->user_id,
                    '✅ Listing approved!',
                    '"' . \Str::limit($listing->title, 50) . '" is now live on kegalle.',
                    url('/listings/' . $listing->slug)
                );
            } catch (\Throwable $e) {}
        }

        $phone = optional($listing->user)->phone;
        if ($phone) {
            try {
                $title = \Str::limit($listing->title, 60);
                app(MobitelSmsService::class)->send(
                    $phone,
                    "kegalle.com: Your ad \"{$title}\" has been approved and is now live. View: kegalle.com/listings/{$listing->slug}"
                );
            } catch (\Throwable $e) {}
        }

        return back()->with('success', 'Listing approved.');
    }

    public function reject(Listing $listing)
    {
        $listing->update(['status' => 'rejected']);
        $listing->load('user');
        $email = optional($listing->user)->email;
        if ($email) {
            try { Mail::to($email)->queue(new ListingStatusMail($listing, false)); } catch (\Throwable $e) { \Log::warning('Reject mail failed: ' . $e->getMessage()); }
        }
        if ($listing->user_id) {
            try {
                app(\App\Services\WebPushService::class)->notifyUser(
                    $listing->user_id,
                    'Listing not approved',
                    '"' . \Str::limit($listing->title, 50) . '" needs changes before it can go live.',
                    url('/dashboard/listings')
                );
            } catch (\Throwable $e) {}
        }
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
            'images.*' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
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
                $newPrimaryIdx = (int) $request->input('new_primary_index', 0);
                $row['is_primary'] = ! $hasExisting && $index === $newPrimaryIdx;
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

        $cfKeys = array_filter(array_keys($request->all()), fn($k) => str_starts_with($k, 'cf_'));
        abort_if(count($cfKeys) > 30, 422, 'Too many custom fields submitted.');

        $cfFieldNames = array_map(fn($k) => substr($k, 3), $cfKeys);
        $fieldMap = \App\Models\CustomField::whereIn('name', $cfFieldNames)
            ->get()->keyBy('name');

        foreach ($request->all() as $key => $value) {
            if (!str_starts_with($key, 'cf_')) continue;
            $fieldName = substr($key, 3);
            $field = $fieldMap->get($fieldName);
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

    private function saveClassifiedBrand(Listing $listing, ?string $brandId): void
    {
        $field = \App\Models\CustomField::where('name', 'brand_id')->first();
        if (!$field) return;
        if ($brandId) {
            \App\Models\ListingFieldValue::updateOrCreate(
                ['listing_id' => $listing->id, 'custom_field_id' => $field->id],
                ['value' => $brandId]
            );
        } else {
            \App\Models\ListingFieldValue::where('listing_id', $listing->id)
                ->where('custom_field_id', $field->id)->delete();
        }
    }

    private function setPrimaryImage(Request $request, Listing $listing): void
    {
        if (! Schema::hasColumn('listing_images', 'is_primary')) return;
        $primaryId = (int) $request->input('primary_image_id');
        if (! $primaryId) return;

        ListingImage::where('listing_id', $listing->id)->update(['is_primary' => false]);
        ListingImage::where('id', $primaryId)->where('listing_id', $listing->id)->update(['is_primary' => true]);
    }

    public function bulkApprove(Request $request)
    {
        $ids = $request->validate(['ids' => 'required|array', 'ids.*' => 'integer'])['ids'];
        $count = Listing::whereIn('id', $ids)->whereNotIn('status', ['approved'])->update(['status' => 'approved']);
        return response()->json(['approved' => $count]);
    }

    public function exportCsv(Request $request)
    {
        $filename = 'listings-' . now()->format('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($request) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Title', 'Type', 'Status', 'Price', 'Category', 'Location', 'Seller', 'Store', 'Created']);
            Listing::with(['category:id,name', 'locationModel:id,name', 'user:id,name,email', 'store:id,name'])
                ->when($request->status, fn ($q) => $q->where('status', $request->status))
                ->when($request->type,   fn ($q) => $q->where('type',   $request->type))
                ->when($request->q,      fn ($q) => $q->where('title',  'like', '%'.$request->q.'%'))
                ->orderByDesc('id')
                ->chunk(500, function ($listings) use ($handle) {
                    foreach ($listings as $l) {
                        fputcsv($handle, [
                            $l->id,
                            $l->title,
                            $l->type,
                            $l->status,
                            $l->price ?? 0,
                            optional($l->category)->name,
                            optional($l->locationModel)->name ?? $l->location,
                            optional($l->user)->name,
                            optional($l->store)->name,
                            $l->created_at?->format('Y-m-d'),
                        ]);
                    }
                });
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i    = 1;
        while (Listing::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
