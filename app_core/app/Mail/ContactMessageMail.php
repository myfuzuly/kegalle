<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $fromName;
    public string $fromEmail;
    public string $messageBody;

    public function __construct(string $fromName, string $fromEmail, string $messageBody)
    {
        $this->fromName = $fromName;
        $this->fromEmail = $fromEmail;
        $this->messageBody = $messageBody;
    }

    public function build()
    {
        return $this
            ->replyTo($this->fromEmail, $this->fromName)
            ->subject('New contact message from '.$this->fromName)
            ->view('emails.contact-message');
    }
}
