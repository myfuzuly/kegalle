<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Http\Request;

class DealManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Deal::with(['listing.images', 'listing.category', 'store', 'user'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('listing', fn($qb) => $qb->where('title', 'like', "%$q%"));
        }

        $deals = $query->paginate(20)->withQueryString();
        $pendingCount = Deal::where('status', 'pending')->count();

        return view('admin.deals.index', compact('deals', 'pendingCount'));
    }

    public function edit(Deal $deal)
    {
        $deal->load(['listing', 'store', 'user']);

        return view('admin.deals.edit', compact('deal'));
    }

    public function update(Request $request, Deal $deal)
    {
        $data = $request->validate([
            'deal_price' => 'required|numeric|min:1',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'stock_qty' => 'nullable|integer|min:1',
            'status' => 'required|in:pending,approved,rejected,expired',
            'admin_note' => 'nullable|string|max:500',
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
            'stock_qty' => $data['stock_qty'] ?? null,
            'status' => $data['status'],
            'admin_note' => $data['admin_note'] ?? $deal->admin_note,
            'is_flash' => $request->boolean('is_flash'),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        return redirect('/admin/deals')->with('success', "Deal #{$deal->id} updated.");
    }

    public function approve(Deal $deal)
    {
        $deal->update(['status' => 'approved']);

        return back()->with('success', "Deal #{$deal->id} approved.");
    }

    public function reject(Deal $deal, Request $request)
    {
        $deal->update([
            'status' => 'rejected',
            'admin_note' => $request->input('admin_note', 'Rejected by admin.'),
        ]);

        return back()->with('success', "Deal #{$deal->id} rejected.");
    }

    public function feature(Deal $deal)
    {
        $deal->update(['is_featured' => !$deal->is_featured]);

        $label = $deal->is_featured ? 'featured' : 'unfeatured';
        return back()->with('success', "Deal #{$deal->id} $label.");
    }

    public function flash(Deal $deal)
    {
        $deal->update(['is_flash' => !$deal->is_flash]);

        $label = $deal->is_flash ? 'marked as flash deal' : 'removed from flash deals';
        return back()->with('success', "Deal #{$deal->id} $label.");
    }

    public function destroy(Deal $deal)
    {
        $deal->delete();

        return back()->with('success', 'Deal deleted.');
    }
}
