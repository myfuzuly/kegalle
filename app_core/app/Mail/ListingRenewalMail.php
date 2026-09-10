<?php

namespace App\Mail;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ListingRenewalMail extends Mailable
{
    use Queueable, SerializesModels;
    public int $tries = 3;
    public array $backoff = [10, 30, 60];


    public Listing $listing;

    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
    }

    public function build()
    {
        return $this
            ->subject('Your listing expires in 7 days — renew now on Kegalle Marketplace')
            ->view('emails.listing-renewal')
        ->withSymfonyMessage(function ($message) {
            $message->getHeaders()->addTextHeader('List-Unsubscribe', '<mailto:unsubscribe@kegalle.com>');
            $message->getHeaders()->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        });
    }
}
