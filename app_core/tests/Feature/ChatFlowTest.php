<?php

namespace Tests\Feature;

use App\Models\ChatThread;
use App\Models\Listing;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ChatFlowTest extends TestCase
{
    use RefreshDatabase;

    private function twoUsersWithThread(): array
    {
        $buyer  = User::factory()->create(['status' => 'active']);
        $seller = User::factory()->create(['status' => 'active']);
        $store  = Store::factory()->create(['user_id' => $seller->id, 'status' => 'approved']);
        $listing = Listing::factory()->create([
            'user_id'  => $seller->id,
            'store_id' => $store->id,
            'status'   => 'approved',
            'slug'     => 'chat-listing-' . uniqid(),
        ]);
        $thread = ChatThread::create([
            'listing_id' => $listing->id,
            'buyer_id'   => $buyer->id,
            'seller_id'  => $seller->id,
            'status'     => 'open',
        ]);
        return [$buyer, $seller, $thread, $listing];
    }

    public function test_buyer_can_send_message(): void
    {
        Mail::fake();
        [$buyer, , $thread] = $this->twoUsersWithThread();

        $this->actingAs($buyer)
            ->postJson("/api/chat/{$thread->id}/send", ['message' => 'Hello, is this available?'])
            ->assertOk()
            ->assertJsonStructure(['id', 'message', 'is_mine']);

        $this->assertDatabaseHas('chat_messages', [
            'thread_id' => $thread->id,
            'sender_id' => $buyer->id,
            'message'   => 'Hello, is this available?',
        ]);
    }

    public function test_seller_can_reply(): void
    {
        Mail::fake();
        [$buyer, $seller, $thread] = $this->twoUsersWithThread();

        $this->actingAs($seller)
            ->postJson("/api/chat/{$thread->id}/send", ['message' => 'Yes, still available!'])
            ->assertOk();

        $this->assertDatabaseHas('chat_messages', [
            'thread_id' => $thread->id,
            'sender_id' => $seller->id,
            'message'   => 'Yes, still available!',
        ]);
    }

    public function test_sending_message_queues_email_to_recipient(): void
    {
        Mail::fake();
        [$buyer, $seller, $thread] = $this->twoUsersWithThread();

        $this->actingAs($buyer)
            ->postJson("/api/chat/{$thread->id}/send", ['message' => 'Interested!'])
            ->assertOk();

        Mail::assertQueued(\App\Mail\NewChatMessageMail::class, fn($m) => $m->hasTo($seller->email));
    }

    public function test_outsider_cannot_send_message(): void
    {
        $outsider = User::factory()->create(['status' => 'active']);
        [, , $thread] = $this->twoUsersWithThread();

        $this->actingAs($outsider)
            ->postJson("/api/chat/{$thread->id}/send", ['message' => 'Intruding'])
            ->assertStatus(403);
    }

    public function test_messages_poll_returns_new_messages(): void
    {
        Mail::fake();
        [$buyer, $seller, $thread] = $this->twoUsersWithThread();

        $this->actingAs($seller)
            ->postJson("/api/chat/{$thread->id}/send", ['message' => 'Ping']);

        $response = $this->actingAs($buyer)
            ->getJson("/api/chat/{$thread->id}/messages?after=0");

        $response->assertOk()->assertJsonCount(1, 'messages');
        $this->assertEquals('Ping', $response->json('messages.0.message'));
    }

    public function test_empty_message_is_rejected(): void
    {
        [$buyer, , $thread] = $this->twoUsersWithThread();

        $this->actingAs($buyer)
            ->postJson("/api/chat/{$thread->id}/send", ['message' => ''])
            ->assertStatus(422);
    }

    public function test_unread_count_reflects_unread_messages(): void
    {
        Mail::fake();
        [$buyer, $seller, $thread] = $this->twoUsersWithThread();

        $this->actingAs($seller)
            ->postJson("/api/chat/{$thread->id}/send", ['message' => 'Hey']);

        $response = $this->actingAs($buyer)->getJson('/api/chat/unread');
        $response->assertOk();
        $this->assertGreaterThanOrEqual(1, $response->json('unread'));
    }

    public function test_buyer_can_start_conversation_via_dashboard(): void
    {
        Mail::fake();
        [$buyer, $seller, , $listing] = $this->twoUsersWithThread();

        // Delete thread so we can test creation
        \App\Models\ChatThread::truncate();
        \App\Models\ChatMessage::truncate();

        $this->actingAs($buyer)
            ->post('/dashboard/chat', [
                'listing_id' => $listing->id,
                'seller_id'  => $seller->id,
                'message'    => 'Hi, I want to buy this.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('chat_threads', [
            'listing_id' => $listing->id,
            'buyer_id'   => $buyer->id,
            'seller_id'  => $seller->id,
        ]);
    }

    public function test_user_cannot_message_themselves(): void
    {
        [, $seller, , $listing] = $this->twoUsersWithThread();

        $this->actingAs($seller)
            ->post('/dashboard/chat', [
                'listing_id' => $listing->id,
                'seller_id'  => $seller->id,
                'message'    => 'Talking to myself',
            ])
            ->assertStatus(403);
    }
}
