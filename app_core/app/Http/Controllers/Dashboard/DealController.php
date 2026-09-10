<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Deal;
use App\Models\Listing;
use App\Models\Store;
use App\Services\ListingService;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = in_array($user->role ?? '', ['super_admin', 'admin']);

        $query = Deal::with(['listing.images', 'listing.category'])->latest();
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }
        $query->when($request->status && $request->status !== '', fn ($q) => $q->where('status', $request->status))
              ->when($request->q, fn ($q) => $q->whereHas('listing', fn($qb) => $qb->where('title', 'like', '%'.$request->q.'%')));

        $deals = $query->paginate(20)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'total'      => $deals->total(),
                'rows'       => view('dashboard.deals._rows', compact('deals'))->render(),
                'pagination' => (string) $deals->links('vendor.pagination.dashboard'),
            ]);
        }

        return view('dashboard.deals.index', compact('deals'));
    }

    public function create()
    {
        $user = auth()->user();
        $isAdmin = in_array($user->role ?? '', ['super_admin', 'admin']);

        $listings = Listing::where('status', 'approved')
            ->where(function ($q) use ($user, $isAdmin) {
                if ($isAdmin) {
                    // Admin can create deals for any listing
                } else {
                    $q->where('user_id', $user->id)
                      ->orWhereHas('store', fn($sq) => $sq->where('user_id', $user->id));
                }
            })
            ->with(['images', 'store'])
            ->latest()
            ->get();

        return view('dashboard.deals.create', compact('listings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'deal_price' => 'required|numeric|min:1',
            'starts_at' => 'required|date|after_or_equal:today',
            'ends_at' => 'required|date|after:starts_at',
            'is_flash' => 'boolean',
            'stock_qty' => 'nullable|integer|min:1',
        ]);

        $listing = Listing::findOrFail($request->listing_id);

        $user = auth()->user();
        $isAdmin = in_array($user->role ?? '', ['super_admin', 'admin']);
        if (!$isAdmin && $listing->user_id !== $user->id && optional($listing->store)->user_id !== $user->id) {
            return back()->with('error', 'You can only create deals for your own listings.');
        }

        $originalPrice = $listing->price;
        $dealPrice = $request->deal_price;

        if (is_null($originalPrice) || $originalPrice <= 0) {
            return back()->with('error', 'Cannot create a deal for a listing with no price set.');
        }

        if ($dealPrice >= $originalPrice) {
            return back()->with('error', 'Deal price must be lower than the original price (LKR ' . number_format($originalPrice) . ').');
        }

        $discountPercent = round((($originalPrice - $dealPrice) / $originalPrice) * 100, 2);

        Deal::create([
            'listing_id' => $listing->id,
            'store_id' => $listing->store_id,
            'user_id' => auth()->id(),
            'deal_price' => $dealPrice,
            'original_price' => $originalPrice,
            'discount_percent' => $discountPercent,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'is_flash' => $request->boolean('is_flash'),
            'stock_qty' => $request->stock_qty,
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard.deals.index')
            ->with('success', 'Deal submitted for admin approval!');
    }

    public function createWithProduct()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $store = Store::where('user_id', auth()->id())->first();

        return view('dashboard.deals.create-with-product', compact('categories', 'store'));
    }

    public function storeWithProduct(Request $request, ListingService $listingService)
    {
        $request->validate([
            'title'       => 'required|string|min:5|max:191',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required|numeric|min:1',
            'description' => 'required|string|min:20',
            'deal_price'  => 'required|numeric|min:1',
            'starts_at'   => 'required|date|after_or_equal:today',
            'ends_at'     => 'required|date|after:starts_at',
            'stock_qty'   => 'nullable|integer|min:1',
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'image|max:4096',
        ]);

        if ($request->deal_price >= $request->price) {
            return back()->withInput()->with('error', 'Deal price must be lower than the original price (LKR ' . number_format($request->price) . ').');
        }

        $user = auth()->user();

        $validated = $request->only(['title', 'category_id', 'price', 'description']);
        $validated['type']    = 'product';
        $validated['ad_type'] = 'sale';
        if ($store = Store::where('user_id', $user->id)->first()) {
            $validated['store_id'] = $store->id;
        }

        $listing = $listingService->create($request, $validated, $user);

        $originalPrice   = (float) $request->price;
        $dealPrice       = (float) $request->deal_price;
        $discountPercent = round((($originalPrice - $dealPrice) / $originalPrice) * 100, 2);

        Deal::create([
            'listing_id'       => $listing->id,
            'store_id'         => $listing->store_id,
            'user_id'          => $user->id,
            'deal_price'       => $dealPrice,
            'original_price'   => $originalPrice,
            'discount_percent' => $discountPercent,
            'starts_at'        => $request->starts_at,
            'ends_at'          => $request->ends_at,
            'is_flash'         => $request->boolean('is_flash'),
            'stock_qty'        => $request->stock_qty,
            'status'           => 'pending',
        ]);

        return redirect()->route('dashboard.deals.index')
            ->with('success', 'Product and deal submitted for admin approval!');
    }

    public function edit(Deal $deal)
    {
        $user = auth()->user();
        $isAdmin = in_array($user->role ?? '', ['super_admin', 'admin']);
        if (!$isAdmin && $deal->user_id !== $user->id) {
            abort(403);
        }

        $deal->load('listing.images');

        return view('dashboard.deals.edit', compact('deal'));
    }

    public function update(Request $request, Deal $deal)
    {
        $user = auth()->user();
        $isAdmin = in_array($user->role ?? '', ['super_admin', 'admin']);
        if (!$isAdmin && $deal->user_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'deal_price' => 'required|numeric|min:1',
            'starts_at' => ['required', 'date', $isAdmin ? 'nullable' : 'after_or_equal:today'],
            'ends_at' => 'required|date|after:starts_at',
            'is_flash' => 'boolean',
            'stock_qty' => 'nullable|integer|min:1',
        ]);

        $originalPrice = $deal->original_price ?: optional($deal->listing)->price ?: 0;
        if ($originalPrice > 0 && $data['deal_price'] >= $originalPrice) {
            return back()->withInput()->with('error', 'Deal price must be lower than the original price (LKR ' . number_format($originalPrice) . ').');
        }

        $deal->update([
            'deal_price' => $data['deal_price'],
            'discount_percent' => $originalPrice > 0 ? round((($originalPrice - $data['deal_price']) / $originalPrice) * 100, 2) : 0,
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'is_flash' => $request->boolean('is_flash'),
            'stock_qty' => $data['stock_qty'] ?? null,
            // Edited deals go back for admin review (unless an admin is editing)
            'status' => $isAdmin ? $deal->status : 'pending',
        ]);

        return redirect()->route('dashboard.deals.index')
            ->with('success', $isAdmin ? 'Deal updated.' : 'Deal updated and sent for admin re-approval.');
    }

    public function destroy(Deal $deal)
    {
        $user = auth()->user();
        $isAdmin = in_array($user->role ?? '', ['super_admin', 'admin']);

        if (!$isAdmin && $deal->user_id !== $user->id) {
            return back()->with('error', 'Unauthorized.');
        }

        if (!$isAdmin && $deal->status === 'approved') {
            return back()->with('error', 'Cannot delete an approved deal. Contact admin.');
        }

        $deal->delete();

        return back()->with('success', 'Deal deleted.');
    }
}
