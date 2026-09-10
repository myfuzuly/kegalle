<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class GuestEnquiryController extends Controller
{
    public function store(Request $request, Listing $listing)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:80'],
            'contact' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:500'],
        ]);

        $seller = $listing->user;
        if (!$seller) {
            return response()->json(['ok' => false, 'error' => 'Seller not found.'], 404);
        }

        // In-app notification to seller
        try {
            UserNotification::send($seller->id, 'enquiry_received', [
                'title'   => 'New Enquiry: ' . \Illuminate\Support\Str::limit($listing->title, 50),
                'message' => $data['name'] . ' enquired about your listing. Contact: ' . $data['contact'],
                'url'     => '/dashboard/listings',
            ]);
        } catch (\Throwable) {}

        // Email to seller — dispatched as a queued closure so it doesn't block the response
        try {
            $listingTitle = $listing->title;
            $listingUrl   = url('/listings/' . $listing->slug);
            $name         = $data['name'];
            $contact      = $data['contact'];
            $message      = $data['message'];
            $sellerEmail  = $seller->email;

            dispatch(function () use ($sellerEmail, $listingTitle, $listingUrl, $name, $contact, $message) {
                $body = "New enquiry on Kegalle Marketplace\n\n"
                    . "Listing: {$listingTitle}\n"
                    . "{$listingUrl}\n\n"
                    . "From: {$name}\n"
                    . "Contact: {$contact}\n\n"
                    . "Message:\n{$message}\n\n"
                    . "---\nReply directly to the contact above. Do not reply to this email.";

                \Illuminate\Support\Facades\Mail::raw($body, function ($mail) use ($sellerEmail, $listingTitle) {
                    $mail->to($sellerEmail)
                        ->subject("Enquiry about your listing: {$listingTitle}");
                });
            })->onQueue('default');
        } catch (\Throwable) {}

        return response()->json(['ok' => true]);
    }
}
