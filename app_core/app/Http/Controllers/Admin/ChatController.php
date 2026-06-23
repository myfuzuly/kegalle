<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatThread;

class ChatController extends Controller
{
    public function index()
    {
        $threads = ChatThread::with(['buyer', 'seller', 'listing'])->latest()->paginate(30);

        return view('admin.chats.index', compact('threads'));
    }

    public function show(ChatThread $chat)
    {
        $chat->load(['buyer', 'seller', 'listing', 'messages.sender']);

        return view('admin.chats.show', ['thread' => $chat]);
    }

    public function close(ChatThread $chat)
    {
        $chat->update(['status' => 'closed']);

        return back()->with('success', 'Conversation closed.');
    }

    public function destroy(ChatThread $chat)
    {
        $chat->delete();

        return redirect('/admin/chats')->with('success', 'Conversation deleted.');
    }
}
