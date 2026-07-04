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
