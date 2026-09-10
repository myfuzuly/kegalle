<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SavedSearchAlertMail extends Mailable
{
    use Queueable, SerializesModels;
    public int $tries = 3;
    public array $backoff = [10, 30, 60];


    public Collection $listings;
    public array $searchParams;

    public function __construct(Collection $listings, array $searchParams)
    {
        $this->listings     = $listings;
        $this->searchParams = $searchParams;
    }

    public function build()
    {
        return $this
            ->subject(count($this->listings).' new '.\Illuminate\Support\Str::plural('listing', count($this->listings)).' match your saved search — Kegalle Marketplace')
            ->view('emails.saved-search-alert')
        ->withSymfonyMessage(function ($message) {
            $message->getHeaders()->addTextHeader('List-Unsubscribe', '<mailto:unsubscribe@kegalle.com>');
            $message->getHeaders()->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        });
    }
}
