<?php

namespace App\Mail;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OfferAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;
    public int $tries = 3;
    public array $backoff = [10, 30, 60];


    public Offer $offer;

    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }

    public function build()
    {
        return $this
            ->subject('Your offer was accepted! — Kegalle Marketplace')
            ->view('emails.offer-accepted');
    }
}
