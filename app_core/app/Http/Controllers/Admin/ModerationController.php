<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Location;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ModerationController extends Controller
{
    public function listings(Request $request)
    {
        $listings = Listing::with(['user', 'store', 'category', 'locationModel'])
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->q, fn ($q) => $q->where('title', 'like', '%'.$request->q.'%'))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.listings.index', compact('listings'));
    }

    public function createListing()
    {
        $users = User::orderBy('name')->get();
        $stores = Store::orderBy('name')->get();
        $categories = Category::with('parent')->orderBy('parent_id')->orderBy('name')->get();
        $locations = Location::with('parent')->where('is_active', true)->orderBy('parent_id')->orderBy('name')->get();

        return view('admin.listings.create', compact('users', 'stores', 'categories', 'locations'));
    }

    public function storeListing(Request $request)
    {
        $data = $this->validateListing($request);
        $payload = $this->listingPayload($request, $data);
        if (Schema::hasColumn('listings', 'created_by_admin_id')) {
            $payload['created_by_admin_id'] = auth()->id();
        }
        Listing::create($payload);

        return redirect('/admin/listings')->with('success', 'Listing posted successfully by Super Admin.');
    }

    public function editListing(Listing $listing)
    {
        $users = User::orderBy('name')->get();
        $stores = Store::orderBy('name')->get();
        $categories = Category::with('parent')->orderBy('parent_id')->orderBy('name')->get();
        $locations = Location::with('parent')->where('is_active', true)->orderBy('parent_id')->orderBy('name')->get();

        return view('admin.listings.edit', compact('listing', 'users', 'stores', 'categories', 'locations'));
    }

    public function updateListing(Request $request, Listing $listing)
    {
        $data = $this->validateListing($request, $listing->id);
        $listing->update($this->listingPayload($request, $data));

        return redirect('/admin/listings')->with('success', 'Listing updated successfully.');
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

    public function stores(Request $request)
    {
        $stores = Store::with('user')->withCount('listings')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
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
