<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatThread;

class ChatController extends Controller
{
    public function index()
    {
        return view('admin.chats.index', ['items' => ChatThread::latest()->take(100)->get()]);
    }
}
