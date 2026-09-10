<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PushSubscriptionController extends Controller
{
    // POST /api/push/subscribe
    public function subscribe(Request $request)
    {
        if (!DB::getSchemaBuilder()->hasTable('push_subscriptions')) {
            return response()->json(['error' => 'Push not configured yet'], 503);
        }

        $request->validate([
            'endpoint' => 'required|url|max:500',
            'p256dh'   => 'required|string|max:200',
            'auth'     => 'required|string|max:50',
        ]);

        $userId = auth()->id();

        DB::table('push_subscriptions')->updateOrInsert(
            ['user_id' => $userId, 'endpoint' => $request->endpoint],
            [
                'p256dh'     => $request->p256dh,
                'auth_token' => $request->auth,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json(['subscribed' => true]);
    }

    // POST /api/push/unsubscribe
    public function unsubscribe(Request $request)
    {
        DB::table('push_subscriptions')
            ->where('user_id', auth()->id())
            ->where('endpoint', $request->endpoint)
            ->delete();

        return response()->json(['unsubscribed' => true]);
    }

    // GET /api/push/latest  — called by SW after receiving a push signal
    public function latest()
    {
        if (!DB::getSchemaBuilder()->hasTable('push_notifications')) {
            return response()->json([
                'title' => 'kegalle Marketplace',
                'body'  => 'You have a new update.',
                'url'   => '/',
            ]);
        }

        $note = DB::table('push_notifications')
            ->where('user_id', auth()->id())
            ->whereNull('shown_at')
            ->latest()
            ->first();

        if (!$note) {
            return response()->json([
                'title' => 'kegalle Marketplace',
                'body'  => 'You have a new update.',
                'url'   => '/',
            ]);
        }

        // Mark shown
        DB::table('push_notifications')
            ->where('id', $note->id)
            ->update(['shown_at' => now()]);

        return response()->json([
            'title' => $note->title,
            'body'  => $note->body,
            'url'   => $note->url ?? '/',
        ]);
    }
}
