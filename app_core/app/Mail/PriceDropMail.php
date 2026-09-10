<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class PriceDropMail extends Mailable
{
    use Queueable, SerializesModels;
    public int $tries = 3;
    public array $backoff = [10, 30, 60];


    public Collection $drops;

    public function __construct(Collection $drops)
    {
        $this->drops = $drops;
    }

    public function build()
    {
        $count = $this->drops->count();
        return $this
            ->subject("{$count} price ".($count === 1 ? 'drop' : 'drops')." on your saved items — kegalle")
            ->view('emails.price-drop-alert')
        ->withSymfonyMessage(function ($message) {
            $message->getHeaders()->addTextHeader('List-Unsubscribe', '<mailto:unsubscribe@kegalle.com>');
            $message->getHeaders()->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        });
    }
}
