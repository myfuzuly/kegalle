<?php

namespace App\Http\Controllers;

use App\Mail\NewChatMessageMail;
use App\Models\ChatThread;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OfferController extends Controller
{
    public function store(Request $request, Listing $listing)
    {
        $request->validate([
            'payment_method' => 'required|in:cod,cash_on_pickup,bank_transfer,installment',
            'offered_price'  => 'nullable|numeric|min:1',
            'message'        => 'nullable|string|max:500',
        ]);

        $buyer = Auth::user();

        // Prevent seller offering on own listing
        if ($listing->user_id === $buyer->id) {
            return back()->with('error', 'You cannot make an offer on your own listing.');
        }

        // Only one pending offer per buyer per listing
        $existing = Offer::where('listing_id', $listing->id)
            ->where('buyer_id', $buyer->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('error', 'You already have a pending offer on this listing.');
        }

        $offer = Offer::create([
            'listing_id'     => $listing->id,
            'buyer_id'       => $buyer->id,
            'offered_price'  => $request->offered_price ?: $listing->price,
            'payment_method' => $request->payment_method,
            'message'        => $request->message,
            'status'         => 'pending',
        ]);

        // Open (or reuse) a chat thread so buyer+seller can discuss
        $thread = ChatThread::firstOrCreate([
            'listing_id' => $listing->id,
            'buyer_id'   => $buyer->id,
            'seller_id'  => $listing->user_id,
        ]);

        $payLabel = Offer::PAYMENT_METHODS[$offer->payment_method]['label'] ?? $offer->payment_method;
        $price    = $offer->offered_price ? 'LKR '.number_format($offer->offered_price) : 'asking price';
        $autoMsg  = "Hi! I'm interested in \"{$listing->title}\". I'd like to pay {$price} via {$payLabel}.";
        if ($request->message) $autoMsg .= "\n\n" . $request->message;

        $chatMsg = $thread->messages()->create([
            'sender_id' => $buyer->id,
            'message'   => $autoMsg,
        ]);

        // Notify seller — email + in-app
        try {
            $sellerEmail = optional($listing->user)->email;
            if ($sellerEmail) {
                $thread->load('listing');
                Mail::to($sellerEmail)->queue(new NewChatMessageMail($thread, $chatMsg, $buyer->name ?? 'A buyer'));
            }
        } catch (\Throwable) {}

        $offerTitle   = 'New offer on "' . \Illuminate\Support\Str::limit($listing->title, 40) . '"';
        $offerBody    = ($buyer->name ?? 'A buyer') . ' offered ' . ($offer->offered_price ? 'LKR ' . number_format($offer->offered_price) : 'asking price') . ' via ' . (Offer::PAYMENT_METHODS[$offer->payment_method]['label'] ?? $offer->payment_method) . '.';
        UserNotification::send($listing->user_id, 'offer_received', $offerTitle, $offerBody, '/dashboard/offers/received', $offer->id);
        try { app(\App\Services\WebPushService::class)->notifyUser($listing->user_id, $offerTitle, $offerBody, '/dashboard/offers/received'); } catch (\Throwable) {}

        return redirect("/dashboard/chat/{$thread->id}")
            ->with('success', 'Offer sent! The seller has been notified.');
    }

    public function update(Request $request, Offer $offer)
    {
        $request->validate([
            'status'      => 'required|in:accepted,rejected',
            'seller_note' => 'nullable|string|max:500',
        ]);

        // Only the listing seller can accept/reject
        abort_if(!$offer->listing, 404, 'Listing no longer exists.');
        if ($offer->listing->user_id !== Auth::id()) {
            abort(403);
        }

        $offer->update([
            'status'      => $request->status,
            'seller_note' => $request->seller_note,
            'responded_at' => now(),
        ]);

        // Notify buyer — email + in-app
        if ($request->status === 'accepted') {
            try {
                $buyerEmail = optional($offer->buyer)->email;
                if ($buyerEmail) {
                    Mail::to($buyerEmail)->queue(new \App\Mail\OfferAcceptedMail($offer));
                }
            } catch (\Throwable) {}

            $accTitle = 'Your offer was accepted!';
            $accBody  = 'The seller accepted your offer on "' . \Illuminate\Support\Str::limit($offer->listing->title ?? '', 50) . '". Check the chat to arrange next steps.';
            UserNotification::send($offer->buyer_id, 'offer_accepted', $accTitle, $accBody, '/dashboard/chat', $offer->id);
            try { app(\App\Services\WebPushService::class)->notifyUser($offer->buyer_id, $accTitle, $accBody, '/dashboard/chat'); } catch (\Throwable) {}
        } else {
            $rejTitle = 'Offer update on "' . \Illuminate\Support\Str::limit($offer->listing->title ?? '', 40) . '"';
            $rejBody  = 'The seller has declined your offer' . ($request->seller_note ? ': ' . \Illuminate\Support\Str::limit($request->seller_note, 100) : '.');
            UserNotification::send($offer->buyer_id, 'offer_rejected', $rejTitle, $rejBody, '/dashboard/offers', $offer->id);
            try { app(\App\Services\WebPushService::class)->notifyUser($offer->buyer_id, $rejTitle, $rejBody, '/dashboard/offers'); } catch (\Throwable) {}
        }

        $msg = $request->status === 'accepted'
            ? 'Offer accepted! The buyer has been notified with next steps.'
            : 'Offer rejected.';

        return back()->with('success', $msg);
    }

    public function myOffers()
    {
        $offers = Offer::where('buyer_id', Auth::id())
            ->with(['listing.images', 'listing.store'])
            ->latest()
            ->paginate(15);

        return view('dashboard.offers.index', compact('offers'));
    }

    public function receivedOffers()
    {
        $listingIds = Listing::where('user_id', Auth::id())->pluck('id');
        $offers = Offer::whereIn('listing_id', $listingIds)
            ->with(['listing', 'buyer'])
            ->latest()
            ->paginate(15);

        return view('dashboard.offers.received', compact('offers'));
    }
}
