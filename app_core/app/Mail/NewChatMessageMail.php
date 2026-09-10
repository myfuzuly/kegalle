<?php

namespace App\Mail;

use App\Models\ChatMessage;
use App\Models\ChatThread;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewChatMessageMail extends Mailable
{
    use Queueable, SerializesModels;
    public int $tries = 3;
    public array $backoff = [10, 30, 60];


    public ChatThread $thread;
    public ChatMessage $chatMessage;
    public string $senderName;

    public function __construct(ChatThread $thread, ChatMessage $chatMessage, string $senderName)
    {
        $this->thread = $thread;
        $this->chatMessage = $chatMessage;
        $this->senderName = $senderName;
    }

    public function build()
    {
        return $this
            ->subject($this->senderName.' sent you a message — Kegalle Marketplace')
            ->view('emails.new-chat-message')
        ->withSymfonyMessage(function ($message) {
            $message->getHeaders()->addTextHeader('List-Unsubscribe', '<mailto:unsubscribe@kegalle.com>');
            $message->getHeaders()->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        });
    }
}
