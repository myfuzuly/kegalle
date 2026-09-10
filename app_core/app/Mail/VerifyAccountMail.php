<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $verifyUrl;

    public function __construct(User $user, string $plainToken)
    {
        $this->user = $user;
        $this->verifyUrl = url('/email/verify/' . $plainToken);
    }

    public function build()
    {
        return $this
            ->subject('Verify your Kegalle account')
            ->view('emails.verify-account');
    }
}
