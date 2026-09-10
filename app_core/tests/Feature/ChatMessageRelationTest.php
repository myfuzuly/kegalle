<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\ChatThread;
use App\Models\Listing;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression test for the missing thread() relation on ChatMessage
 * that caused a silent production 500 for weeks (caught Jul 2026).
 */
class ChatMessageRelationTest extends TestCase
{
    use RefreshDatabase;

    private function makeThread(): ChatThread
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create();
        $store  = Store::factory()->create(['user_id' => $seller->id, 'status' => 'approved']);
        $listing = Listing::factory()->create(['user_id' => $seller->id, 'store_id' => $store->id, 'status' => 'approved']);

        return ChatThread::create([
            'listing_id' => $listing->id,
            'buyer_id'   => $buyer->id,
            'seller_id'  => $seller->id,
        ]);
    }

    public function test_chat_message_has_thread_relation(): void
    {
        $thread  = $this->makeThread();
        $message = $thread->messages()->create([
            'sender_id' => $thread->buyer_id,
            'message'   => 'Hello!',
        ]);

        $loaded = ChatMessage::find($message->id);
        $this->assertNotNull($loaded->thread, 'ChatMessage::thread() must return a ChatThread (was null — missing belongsTo relation)');
        $this->assertSame($thread->id, $loaded->thread->id);
    }

    public function test_wherehas_thread_does_not_throw(): void
    {
        $thread  = $this->makeThread();
        $thread->messages()->create(['sender_id' => $thread->buyer_id, 'message' => 'Hi']);

        // This is the query that threw in production via ChatPollController::unreadCount()
        $count = ChatMessage::whereHas('thread', function ($q) use ($thread) {
            $q->where('id', $thread->id);
        })->count();

        $this->assertGreaterThan(0, $count);
    }

    public function test_chat_poll_unread_count_endpoint_returns_200(): void
    {
        $thread = $this->makeThread();
        $thread->messages()->create(['sender_id' => $thread->buyer_id, 'message' => 'Test']);

        $this->actingAs(User::find($thread->seller_id))
            ->getJson('/api/chat/unread')
            ->assertOk()
            ->assertJsonStructure(['unread']);
    }
}
