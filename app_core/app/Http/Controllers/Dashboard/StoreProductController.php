<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StoreProductController extends Controller
{
    private function ownedStore($store)
    {
        return Store::where('id', $store)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    public function index($store)
    {
        $store = $this->ownedStore($store);

        $products = Listing::where('store_id', $store->id)
            ->latest()
            ->paginate(20);

        return view('dashboard.stores.products.index', compact('store', 'products'));
    }

    public function create($store)
    {
        $store = $this->ownedStore($store);
        $categories = Category::where('is_active', 1)->orderBy('name')->get();

        return view('dashboard.stores.products.create', compact('store', 'categories'));
    }

    public function store(Request $request, $store)
    {
        $store = $this->ownedStore($store);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'category_id' => ['nullable', 'integer'],
            'price' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:190'],
        ]);

        $data['user_id'] = Auth::id();
        $data['store_id'] = $store->id;
        $data['type'] = 'product';
        $data['status'] = 'pending';
        $data['slug'] = Str::slug($data['title']).'-'.time();

        Listing::create($data);

        return redirect()
            ->route('dashboard.stores.show', $store)
            ->with('success', 'Product submitted for approval.');
    }

    public function edit($store, Listing $listing)
    {
        $store = $this->ownedStore($store);
        abort_if($listing->store_id !== $store->id, 403);

        $categories = Category::where('is_active', 1)->orderBy('name')->get();

        return view('dashboard.stores.products.edit', compact('store', 'listing', 'categories'));
    }

    public function update(Request $request, $store, Listing $listing)
    {
        $store = $this->ownedStore($store);
        abort_if($listing->store_id !== $store->id, 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'category_id' => ['nullable', 'integer'],
            'price' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:190'],
        ]);

        $data['status'] = 'pending';
        $listing->update($data);

        return redirect()
            ->route('dashboard.stores.products.index', $store)
            ->with('success', 'Product updated and submitted for review.');
    }
}
