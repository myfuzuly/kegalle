<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\NewChatMessageMail;
use App\Models\ChatThread;
use App\Models\ChatMessage;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // If query params present, handle "start conversation" flow
        if ($request->filled('listing') && $request->filled('seller')) {
            $listing = Listing::findOrFail($request->listing);
            $sellerId = (int) $request->seller;

            // Can't message yourself
            if ($sellerId === $userId) {
                return redirect()->route('dashboard.chat')->with('error', 'You cannot message yourself.');
            }

            // Check if thread already exists
            $existing = ChatThread::where('listing_id', $listing->id)
                ->where('buyer_id', $userId)
                ->where('seller_id', $sellerId)
                ->first();

            if ($existing) {
                return redirect()->route('dashboard.chat.show', $existing);
            }

            // Show new conversation form
            return view('dashboard.chats.index', [
                'threads' => $this->getThreads($userId),
                'newListing' => $listing,
                'newSellerId' => $sellerId,
            ]);
        }

        return view('dashboard.chats.index', [
            'threads' => $this->getThreads($userId),
            'newListing' => null,
            'newSellerId' => null,
        ]);
    }

    public function show(ChatThread $thread)
    {
        $userId = Auth::id();
        abort_if($thread->buyer_id !== $userId && $thread->seller_id !== $userId, 403);

        $thread->load(['messages.sender', 'buyer', 'seller', 'listing']);

        // Mark unread messages as read (messages not sent by current user)
        ChatMessage::where('thread_id', $thread->id)
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $thread->messages()->orderBy('created_at', 'asc')->get();

        $otherUser = $thread->buyer_id === $userId ? $thread->seller : $thread->buyer;

        return view('dashboard.chats.show', compact('thread', 'messages', 'otherUser'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'seller_id' => 'required|exists:users,id',
            'message' => 'required|string|max:2000',
        ]);

        $userId = Auth::id();
        $sellerId = (int) $request->seller_id;

        abort_if($sellerId === $userId, 403, 'You cannot message yourself.');

        $listing = Listing::findOrFail($request->listing_id);
        abort_if((int) $listing->user_id !== $sellerId, 422, 'Listing does not belong to that seller.');

        // Check for existing thread
        $thread = ChatThread::where('listing_id', $request->listing_id)
            ->where('buyer_id', $userId)
            ->where('seller_id', $sellerId)
            ->first();

        if (!$thread) {
            $thread = ChatThread::create([
                'listing_id' => $request->listing_id,
                'buyer_id' => $userId,
                'seller_id' => $sellerId,
                'status' => 'open',
            ]);
        }

        $msg = ChatMessage::create([
            'thread_id' => $thread->id,
            'sender_id' => $userId,
            'message' => $request->message,
        ]);

        $thread->load(['buyer', 'seller', 'listing']);
        $sender = $thread->buyer_id === $userId ? $thread->buyer : $thread->seller;
        $recipient = $thread->buyer_id === $userId ? $thread->seller : $thread->buyer;
        if ($recipient && $recipient->email) {
            try {
                Mail::to($recipient->email)->queue(new NewChatMessageMail($thread, $msg, $sender->name ?? 'Someone'));
            } catch (\Throwable $e) {
                Log::warning('Chat mail failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('dashboard.chat.show', $thread)->with('success', 'Message sent.');
    }

    public function reply(Request $request, ChatThread $thread)
    {
        $userId = Auth::id();
        abort_if($thread->buyer_id !== $userId && $thread->seller_id !== $userId, 403);

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $msg = ChatMessage::create([
            'thread_id' => $thread->id,
            'sender_id' => $userId,
            'message' => $request->message,
        ]);

        $thread->load(['buyer', 'seller', 'listing']);
        $sender = $thread->buyer_id === $userId ? $thread->buyer : $thread->seller;
        $recipient = $thread->buyer_id === $userId ? $thread->seller : $thread->buyer;
        if ($recipient && $recipient->email) {
            try {
                Mail::to($recipient->email)->queue(new NewChatMessageMail($thread, $msg, $sender->name ?? 'Someone'));
            } catch (\Throwable $e) {
                Log::warning('Chat mail failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('dashboard.chat.show', $thread)->with('success', 'Reply sent.');
    }

    private function getThreads(int $userId)
    {
        return ChatThread::where('buyer_id', $userId)
            ->orWhere('seller_id', $userId)
            ->with(['buyer', 'seller', 'listing', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->withCount(['messages as unread_count' => function ($q) use ($userId) {
                $q->where('sender_id', '!=', $userId)->whereNull('read_at');
            }])
            ->get()
            ->sortByDesc(function ($thread) {
                return optional($thread->messages->first())->created_at ?? $thread->created_at;
            });
    }
}
