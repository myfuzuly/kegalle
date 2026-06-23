<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
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
        return view('dashboard.stores.form', [
            'store' => new Store,
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:190'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
        ]);

        $data['user_id'] = auth()->id();
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['status'] = 'pending';

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

        return view('dashboard.stores.form', [
            'store' => $store,
            'mode' => 'edit',
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
        ]);

        if ($store->name !== $data['name']) {
            $data['slug'] = $this->uniqueSlug($data['name'], $store->id);
        }

        $store->update($data);

        return redirect()
            ->route('dashboard.stores.show', $store)
            ->with('success', 'Store profile updated.');
    }

    public function analytics(Store $store)
    {
        $this->authorizeStore($store);

        return view('dashboard.stores.analytics', compact('store'));
    }

    public function reviews(Store $store)
    {
        $this->authorizeStore($store);

        return view('dashboard.stores.reviews', compact('store'));
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
