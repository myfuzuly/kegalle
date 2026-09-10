<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewChatMessageMail;
use App\Models\ChatThread;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ChatPollController extends Controller
{
    // GET /api/chat/{thread}/messages?after={id}
    public function messages(ChatThread $thread, Request $request)
    {
        $userId = Auth::id();
        abort_if($thread->buyer_id !== $userId && $thread->seller_id !== $userId, 403);

        $after = (int) $request->query('after', 0);

        $messages = $thread->messages()
            ->with('sender:id,name')
            ->when($after > 0, fn($q) => $q->where('id', '>', $after))
            ->orderBy('id', 'asc')
            ->take(50)
            ->get();

        // Mark as read
        if ($messages->isNotEmpty()) {
            ChatMessage::where('thread_id', $thread->id)
                ->where('sender_id', '!=', $userId)
                ->whereNull('read_at')
                ->whereIn('id', $messages->pluck('id'))
                ->update(['read_at' => now()]);
        }

        $readIds = ChatMessage::where('thread_id', $thread->id)
            ->where('sender_id', $userId)
            ->whereNotNull('read_at')
            ->latest('id')
            ->limit(100)
            ->pluck('id');

        return response()->json([
            'messages' => $messages->map(fn($m) => $this->formatMessage($m, $userId)),
            'read_ids' => $readIds,
        ]);
    }

    // POST /api/chat/{thread}/send
    public function send(ChatThread $thread, Request $request)
    {
        $userId = Auth::id();
        abort_if($thread->buyer_id !== $userId && $thread->seller_id !== $userId, 403);

        $request->validate(['message' => 'required|string|max:2000']);

        $msg = ChatMessage::create([
            'thread_id' => $thread->id,
            'sender_id' => $userId,
            'message'   => $request->message,
        ]);

        $thread->load(['buyer', 'seller', 'listing']);
        $sender    = $thread->buyer_id === $userId ? $thread->buyer : $thread->seller;
        $recipient = $thread->buyer_id === $userId ? $thread->seller : $thread->buyer;

        // Email notification
        if ($recipient?->email) {
            try {
                Mail::to($recipient->email)->queue(new NewChatMessageMail($thread, $msg, $sender->name ?? 'Someone'));
            } catch (\Throwable $e) {
                Log::warning('Chat mail failed: ' . $e->getMessage());
            }
        }

        // Web push notification
        if ($recipient) {
            app(\App\Services\WebPushService::class)->notifyUser(
                $recipient->id,
                'New message from ' . ($sender->name ?? 'Someone'),
                \Str::limit(optional($thread->listing)->title ?? 'Kegalle Marketplace', 50),
                url('/dashboard/chat/' . $thread->id)
            );
        }

        return response()->json($this->formatMessage($msg->load('sender:id,name'), $userId));
    }

    // GET /api/chat/unread  — total unread count for nav badge
    public function unreadCount()
    {
        $userId = Auth::id();
        $count = ChatMessage::whereHas('thread', fn($q) =>
                $q->where('buyer_id', $userId)->orWhere('seller_id', $userId)
            )
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();

        return response()->json(['unread' => $count]);
    }

    private function formatMessage(ChatMessage $m, int $userId): array
    {
        return [
            'id'        => $m->id,
            'message'   => $m->message,
            'is_mine'   => $m->sender_id === $userId,
            'sender'    => $m->sender->name ?? 'User',
            'time'      => $m->created_at->diffForHumans(),
            'time_iso'  => $m->created_at->toIso8601String(),
            'read_at'   => $m->read_at?->diffForHumans(),
        ];
    }
}
