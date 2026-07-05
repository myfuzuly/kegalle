<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Listing;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = in_array($user->role ?? '', ['super_admin', 'admin']);

        $query = Deal::with(['listing.images', 'listing.category'])->latest();
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }
        $deals = $query->paginate(20);

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
            'starts_at' => 'required|date',
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
        if ($deal->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized.');
        }

        if ($deal->status === 'approved') {
            return back()->with('error', 'Cannot delete an approved deal. Contact admin.');
        }

        $deal->delete();

        return back()->with('success', 'Deal deleted.');
    }
}
