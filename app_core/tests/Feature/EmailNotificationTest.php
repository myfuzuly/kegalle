<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use App\Mail\ListingStatusMail;
use App\Mail\NewChatMessageMail;
use App\Mail\OfferAcceptedMail;
use App\Mail\PasswordResetMail;
use App\Mail\VerifyAccountMail;
use App\Models\ChatThread;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Verifies that every email notification in the system is queued with the
 * correct mailable class and addressed to the right recipient.
 *
 * Uses Mail::fake() so no real email is sent; assertions confirm the app
 * called Mail::queue() with the expected parameters.
 */
class EmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function seller(): User
    {
        return User::factory()->create(['status' => 'active', 'role' => 'user']);
    }

    private function buyer(): User
    {
        return User::factory()->create(['status' => 'active', 'role' => 'user']);
    }

    private function admin(): User
    {
        return User::factory()->create(['status' => 'active', 'role' => 'super_admin']);
    }

    private function pendingListing(User $seller): Listing
    {
        $store = Store::factory()->create(['user_id' => $seller->id, 'status' => 'approved']);
        return Listing::factory()->create([
            'user_id'  => $seller->id,
            'store_id' => $store->id,
            'status'   => 'pending',
            'slug'     => 'listing-' . uniqid(),
        ]);
    }

    private function threadBetween(User $buyer, User $seller, Listing $listing): ChatThread
    {
        return ChatThread::create([
            'listing_id' => $listing->id,
            'buyer_id'   => $buyer->id,
            'seller_id'  => $seller->id,
            'status'     => 'open',
        ]);
    }

    // ── 1. Registration ───────────────────────────────────────────────────────

    public function test_registration_queues_verification_email(): void
    {
        Mail::fake();
        $location = $this->createLocation();

        $this->post('/register', [
            'account_type'          => 'user',
            'name'                  => 'New User',
            'email'                 => 'newuser@example.com',
            'phone'                 => '0712000001',
            'location_id'           => $location->id,
            'password'              => 'SecurePass1!',
            'password_confirmation' => 'SecurePass1!',
        ]);

        Mail::assertQueued(VerifyAccountMail::class, function ($mail) {
            return $mail->hasTo('newuser@example.com');
        });
    }

    // ── 2. Password reset ─────────────────────────────────────────────────────

    public function test_password_reset_request_queues_reset_email(): void
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'reset@example.com']);

        $this->post('/forgot-password', ['email' => 'reset@example.com']);

        Mail::assertQueued(PasswordResetMail::class, fn($m) => $m->hasTo('reset@example.com'));
    }

    public function test_no_email_queued_for_unknown_reset_address(): void
    {
        Mail::fake();

        $this->post('/forgot-password', ['email' => 'nobody@example.com']);

        Mail::assertNothingQueued();
    }

    // ── 3. Listing approval / rejection ──────────────────────────────────────

    public function test_listing_approval_queues_status_email_to_seller(): void
    {
        Mail::fake();
        $seller  = $this->seller();
        $admin   = $this->admin();
        $listing = $this->pendingListing($seller);

        $this->actingAs($admin)->post("/admin/listings/{$listing->id}/approve");

        Mail::assertQueued(ListingStatusMail::class, function ($mail) use ($seller, $listing) {
            return $mail->hasTo($seller->email) && $mail->listing->id === $listing->id;
        });
    }

    public function test_listing_rejection_queues_status_email_to_seller(): void
    {
        Mail::fake();
        $seller  = $this->seller();
        $admin   = $this->admin();
        $listing = $this->pendingListing($seller);

        $this->actingAs($admin)->post("/admin/listings/{$listing->id}/reject", ['reason' => 'Policy violation']);

        Mail::assertQueued(ListingStatusMail::class, fn($m) => $m->hasTo($seller->email));
    }

    // ── 4. Chat messages ──────────────────────────────────────────────────────

    public function test_chat_message_via_api_queues_email_to_recipient(): void
    {
        Mail::fake();
        $seller  = $this->seller();
        $buyer   = $this->buyer();
        $listing = $this->pendingListing($seller);
        $thread  = $this->threadBetween($buyer, $seller, $listing);

        $this->actingAs($buyer)
            ->postJson("/api/chat/{$thread->id}/send", ['message' => 'Is this still available?']);

        Mail::assertQueued(NewChatMessageMail::class, fn($m) => $m->hasTo($seller->email));
    }

    public function test_chat_reply_via_dashboard_queues_email_to_recipient(): void
    {
        Mail::fake();
        $seller  = $this->seller();
        $buyer   = $this->buyer();
        $listing = $this->pendingListing($seller);
        $thread  = $this->threadBetween($buyer, $seller, $listing);

        $this->actingAs($seller)
            ->post("/dashboard/chat/{$thread->id}/reply", ['message' => 'Yes, pick up from Kegalle town.']);

        Mail::assertQueued(NewChatMessageMail::class, fn($m) => $m->hasTo($buyer->email));
    }

    public function test_new_chat_thread_queues_email_to_seller(): void
    {
        Mail::fake();
        $seller  = $this->seller();
        $buyer   = $this->buyer();
        $listing = $this->pendingListing($seller);

        $this->actingAs($buyer)->post('/dashboard/chat', [
            'listing_id' => $listing->id,
            'seller_id'  => $seller->id,
            'message'    => 'Hi, interested in buying.',
        ]);

        Mail::assertQueued(NewChatMessageMail::class, fn($m) => $m->hasTo($seller->email));
    }

    // ── 5. Offers ─────────────────────────────────────────────────────────────

    public function test_offer_submission_queues_notification_to_seller(): void
    {
        Mail::fake();
        $seller  = $this->seller();
        $buyer   = $this->buyer();
        $listing = $this->pendingListing($seller);
        $listing->update(['status' => 'approved']);

        $this->actingAs($buyer)->post("/listings/{$listing->id}/offer", [
            'payment_method' => 'cash_on_pickup',
            'offered_price'  => 9500,
        ]);

        // Offer creates a chat thread and sends the first message as an email
        Mail::assertQueued(NewChatMessageMail::class, fn($m) => $m->hasTo($seller->email));
    }

    public function test_offer_acceptance_queues_email_to_buyer(): void
    {
        Mail::fake();
        $seller  = $this->seller();
        $buyer   = $this->buyer();
        $listing = $this->pendingListing($seller);

        $offer = Offer::create([
            'listing_id'     => $listing->id,
            'buyer_id'       => $buyer->id,
            'offered_price'  => 8000,
            'payment_method' => 'cod',
            'status'         => 'pending',
        ]);

        $this->actingAs($seller)->patch("/offers/{$offer->id}", ['status' => 'accepted']);

        Mail::assertQueued(OfferAcceptedMail::class, fn($m) => $m->hasTo($buyer->email));
    }

    public function test_offer_rejection_does_not_queue_offer_accepted_mail(): void
    {
        Mail::fake();
        $seller  = $this->seller();
        $buyer   = $this->buyer();
        $listing = $this->pendingListing($seller);

        $offer = Offer::create([
            'listing_id'     => $listing->id,
            'buyer_id'       => $buyer->id,
            'offered_price'  => 8000,
            'payment_method' => 'cod',
            'status'         => 'pending',
        ]);

        $this->actingAs($seller)->patch("/offers/{$offer->id}", ['status' => 'rejected']);

        Mail::assertNotQueued(OfferAcceptedMail::class);
    }

    // ── 6. Contact form ───────────────────────────────────────────────────────

    public function test_contact_form_queues_email_to_site_address(): void
    {
        Mail::fake();

        $this->post('/contact-us', [
            'name'    => 'A Visitor',
            'email'   => 'visitor@example.com',
            'subject' => 'Inquiry',
            'message' => 'I have a question about the site.',
        ]);

        // ContactMessageMail uses ->send() not ->queue(), so use assertSent
        Mail::assertSent(ContactMessageMail::class);
    }

    // ── 7. Store approval ─────────────────────────────────────────────────────

    public function test_store_approval_sends_in_app_notification(): void
    {
        $admin  = $this->admin();
        $seller = $this->seller();
        $store  = Store::factory()->create(['user_id' => $seller->id, 'status' => 'pending']);

        $this->actingAs($admin)->post("/admin/stores/{$store->id}/approve");

        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $seller->id,
            'type'    => 'store_approved',
        ]);
    }

    // ── 8. Mail::fake() summary log ───────────────────────────────────────────

    public function test_all_mailable_classes_exist(): void
    {
        $mailables = [
            VerifyAccountMail::class,
            PasswordResetMail::class,
            ListingStatusMail::class,
            NewChatMessageMail::class,
            OfferAcceptedMail::class,
            ContactMessageMail::class,
        ];

        foreach ($mailables as $class) {
            $this->assertTrue(class_exists($class), "Mailable {$class} does not exist");
        }
    }
}
