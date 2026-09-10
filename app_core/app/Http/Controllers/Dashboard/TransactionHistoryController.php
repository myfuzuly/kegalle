<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class TransactionHistoryController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $offers = Offer::with(['listing'])
            ->where(function ($q) use ($userId) {
                $q->where('buyer_id', $userId)
                  ->orWhereHas('listing', fn($q2) => $q2->where('user_id', $userId));
            })
            ->whereIn('status', ['accepted', 'rejected', 'completed'])
            ->latest('updated_at')
            ->take(50)
            ->get()
            ->map(function ($offer) use ($userId) {
                $offer->role = $offer->buyer_id === $userId ? 'buyer' : 'seller';
                return $offer;
            });

        $payments = Payment::with('membershipPlan')
            ->where('user_id', $userId)
            ->latest()
            ->take(30)
            ->get();

        return view('dashboard.transactions.index', compact('offers', 'payments'));
    }
}
