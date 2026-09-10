<?php

namespace App\Mail;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ListingStatusMail extends Mailable
{
    use Queueable, SerializesModels;
    public int $tries = 3;
    public array $backoff = [10, 30, 60];


    public Listing $listing;
    public string $statusLabel;
    public bool $approved;

    public function __construct(Listing $listing, bool $approved)
    {
        $this->listing = $listing;
        $this->approved = $approved;
        $this->statusLabel = $approved ? 'Approved' : 'Rejected';
    }

    public function build()
    {
        return $this
            ->subject('Your listing has been '.$this->statusLabel.' — Kegalle Marketplace')
            ->view('emails.listing-status')
        ->withSymfonyMessage(function ($message) {
            $message->getHeaders()->addTextHeader('List-Unsubscribe', '<mailto:unsubscribe@kegalle.com>');
            $message->getHeaders()->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        });
    }
}
