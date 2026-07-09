<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Location;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminStoreController extends Controller
{
    public function index(Request $request)
    {
        $stores = Store::with('user')->withCount('listings')
            ->whereIn('status', ['approved', 'active', 'published'])
            ->when($request->featured === '1', fn ($q) => $q->where('is_featured', 1))
            ->when($request->featured === '0', fn ($q) => $q->where('is_featured', 0))
            ->when($request->q, fn ($q) => $q->where('name', 'like', '%'.$request->q.'%'))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.stores.index', compact('stores'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        $locations = Location::where('is_active', 1)->orderBy('sort_order')->orderBy('name')->get();
        $categories = Category::whereNull('parent_id')->where('is_active', 1)->orderBy('sort_order')->orderBy('name')->with('children')->get();
        return view('admin.stores.create', compact('users', 'locations', 'categories'));
    }

    public function store(StoreStoreRequest $request)
    {
        $data = $request->validated();

        if ($request->has('whatsapp_same') && ! empty($data['phone'])) {
            $data['whatsapp'] = $data['phone'];
        }
        unset($data['logo'], $data['banner'], $data['whatsapp_same'], $data['categories']);
        $data = \App\Http\Controllers\Dashboard\StoreController::normalizePhones($data);
        $data['slug'] = Str::slug($data['name'] . '-' . uniqid());
        $data['is_featured'] = $request->boolean('is_featured');

        $storeSlug = Str::slug($data['name']);
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

        $store = Store::create($data);
        if ($request->has('categories')) {
            $store->categories()->sync($request->input('categories', []));
        }

        return redirect('/admin/stores')->with('success', 'Store created successfully.');
    }

    public function edit(Store $store)
    {
        $users = User::orderBy('name')->get();
        $locations = Location::where('is_active', 1)->orderBy('sort_order')->orderBy('name')->get();
        $categories = Category::whereNull('parent_id')->where('is_active', 1)->orderBy('sort_order')->orderBy('name')->with('children')->get();
        return view('admin.stores.edit', compact('store', 'users', 'locations', 'categories'));
    }

    public function update(UpdateStoreRequest $request, Store $store)
    {
        $data = $request->validated();

        if ($request->has('whatsapp_same') && ! empty($data['phone'])) {
            $data['whatsapp'] = $data['phone'];
        }
        unset($data['logo'], $data['banner'], $data['remove_logo'], $data['remove_banner'], $data['whatsapp_same'], $data['categories']);
        $data = \App\Http\Controllers\Dashboard\StoreController::normalizePhones($data);

        $storeSlug = Str::slug($data['name'] ?? $store->name);
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
        $store->categories()->sync($request->input('categories', []));

        return redirect('/admin/stores')->with('success', 'Store updated successfully.');
    }

    public function approve(Store $store)
    {
        $store->update(['status' => 'approved']);
        return back()->with('success', 'Store approved.');
    }

    public function suspend(Store $store)
    {
        $store->update(['status' => 'suspended']);
        return back()->with('success', 'Store suspended.');
    }

    public function feature(Store $store)
    {
        $store->is_featured = ! (bool) $store->is_featured;
        $store->save();
        return back()->with('success', 'Store featured status updated.');
    }

    public function verify(Store $store)
    {
        $store->is_verified = ! (bool) $store->is_verified;
        $store->save();
        $label = $store->is_verified ? 'verified' : 'unverified';
        return back()->with('success', "Store marked as {$label}.");
    }

    public function transfer(Request $request, Store $store)
    {
        $data = $request->validate([
            'new_owner_id' => 'required|exists:users,id|different:current_owner_id',
        ], [
            'new_owner_id.different' => 'The new owner must be different from the current owner.',
        ]);

        $request->merge(['current_owner_id' => $store->user_id]);
        $newOwner = User::findOrFail($data['new_owner_id']);

        $store->user_id = $newOwner->id;
        $store->save();

        $listingsTransferred = Listing::where('store_id', $store->id)->update(['user_id' => $newOwner->id]);

        $msg = "Store \"{$store->name}\" transferred to {$newOwner->name} ({$newOwner->email}).";
        if ($listingsTransferred > 0) {
            $msg .= " {$listingsTransferred} listing(s) also transferred.";
        }

        return redirect('/admin/stores/' . $store->id . '/edit')->with('success', $msg);
    }

    public function destroy(Store $store)
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
}
