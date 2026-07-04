<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class StaticPageController extends Controller
{
    public function contactSubmit(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:180',
            'message' => 'required|string|max:2000',
        ]);

        try {
            Mail::to('support@kurulla.com')
                ->send(new ContactMessageMail($data['name'], $data['email'], $data['message']));
        } catch (\Throwable $e) {
            return back()->withInput()->with('success', 'Sorry, we could not send your message right now. Please try again later or contact us via WhatsApp.');
        }

        return back()->with('success', 'Thanks! Your message has been sent — we will get back to you soon.');
    }
}
