<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\Offer;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OfferFlowTest extends TestCase
{
    use RefreshDatabase;

    private function seller(): User
    {
        return User::factory()->create(['role' => 'user', 'status' => 'active']);
    }

    private function buyer(): User
    {
        return User::factory()->create(['role' => 'user', 'status' => 'active']);
    }

    private function listing(User $seller): Listing
    {
        $store = Store::factory()->create(['user_id' => $seller->id, 'status' => 'approved']);
        return Listing::factory()->create([
            'user_id'  => $seller->id,
            'store_id' => $store->id,
            'status'   => 'approved',
            'price'    => 10000,
            'slug'     => 'test-listing-' . uniqid(),
        ]);
    }

    public function test_buyer_can_submit_offer(): void
    {
        Mail::fake();
        $seller  = $this->seller();
        $buyer   = $this->buyer();
        $listing = $this->listing($seller);

        $this->actingAs($buyer)
            ->post("/listings/{$listing->id}/offer", [
                'payment_method' => 'cod',
                'offered_price'  => 9000,
                'message'        => 'Can you do 9000?',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('offers', [
            'listing_id'     => $listing->id,
            'buyer_id'       => $buyer->id,
            'offered_price'  => 9000,
            'payment_method' => 'cod',
            'status'         => 'pending',
        ]);
    }

    public function test_offer_queues_notification_email_to_seller(): void
    {
        Mail::fake();
        $seller  = $this->seller();
        $buyer   = $this->buyer();
        $listing = $this->listing($seller);

        $this->actingAs($buyer)
            ->post("/listings/{$listing->id}/offer", [
                'payment_method' => 'cod',
                'offered_price'  => 9500,
            ]);

        Mail::assertQueued(\App\Mail\NewChatMessageMail::class, fn($m) => $m->hasTo($seller->email));
    }

    public function test_seller_cannot_offer_on_own_listing(): void
    {
        Mail::fake();
        $seller  = $this->seller();
        $listing = $this->listing($seller);

        $this->actingAs($seller)
            ->post("/listings/{$listing->id}/offer", ['payment_method' => 'cod'])
            ->assertRedirect();

        $this->assertDatabaseMissing('offers', ['listing_id' => $listing->id]);
    }

    public function test_duplicate_pending_offer_blocked(): void
    {
        Mail::fake();
        $seller  = $this->seller();
        $buyer   = $this->buyer();
        $listing = $this->listing($seller);

        $this->actingAs($buyer)->post("/listings/{$listing->id}/offer", ['payment_method' => 'cod']);
        $this->actingAs($buyer)->post("/listings/{$listing->id}/offer", ['payment_method' => 'bank_transfer']);

        $this->assertSame(1, Offer::where('buyer_id', $buyer->id)->count());
    }

    public function test_seller_can_accept_offer(): void
    {
        Mail::fake();
        $seller  = $this->seller();
        $buyer   = $this->buyer();
        $listing = $this->listing($seller);

        $offer = Offer::create([
            'listing_id'     => $listing->id,
            'buyer_id'       => $buyer->id,
            'offered_price'  => 8500,
            'payment_method' => 'cash_on_pickup',
            'status'         => 'pending',
        ]);

        $this->actingAs($seller)
            ->patch("/offers/{$offer->id}", [
                'status'      => 'accepted',
                'seller_note' => 'See you tomorrow.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('offers', ['id' => $offer->id, 'status' => 'accepted']);
    }

    public function test_accepting_offer_emails_buyer(): void
    {
        Mail::fake();
        $seller  = $this->seller();
        $buyer   = $this->buyer();
        $listing = $this->listing($seller);

        $offer = Offer::create([
            'listing_id'     => $listing->id,
            'buyer_id'       => $buyer->id,
            'offered_price'  => 8500,
            'payment_method' => 'cash_on_pickup',
            'status'         => 'pending',
        ]);

        $this->actingAs($seller)->patch("/offers/{$offer->id}", ['status' => 'accepted']);

        Mail::assertQueued(\App\Mail\OfferAcceptedMail::class, fn($m) => $m->hasTo($buyer->email));
    }

    public function test_non_seller_cannot_accept_offer(): void
    {
        Mail::fake();
        $seller  = $this->seller();
        $buyer   = $this->buyer();
        $listing = $this->listing($seller);

        $offer = Offer::create([
            'listing_id'     => $listing->id,
            'buyer_id'       => $buyer->id,
            'offered_price'  => 8500,
            'payment_method' => 'cod',
            'status'         => 'pending',
        ]);

        $this->actingAs($this->buyer())
            ->patch("/offers/{$offer->id}", ['status' => 'accepted'])
            ->assertStatus(403);
    }

    public function test_offer_requires_valid_payment_method(): void
    {
        $seller  = $this->seller();
        $buyer   = $this->buyer();
        $listing = $this->listing($seller);

        $this->actingAs($buyer)
            ->post("/listings/{$listing->id}/offer", ['payment_method' => 'bitcoin'])
            ->assertSessionHasErrors('payment_method');
    }

    public function test_guest_cannot_submit_offer(): void
    {
        $listing = $this->listing($this->seller());

        $this->post("/listings/{$listing->id}/offer", ['payment_method' => 'cod'])
            ->assertRedirect('/login');
    }

    public function test_received_offers_only_shows_own_listings(): void
    {
        Mail::fake();
        $seller = $this->seller();
        $other  = $this->seller();
        $buyer  = $this->buyer();

        $myListing    = $this->listing($seller);
        $otherListing = $this->listing($other);

        Offer::create(['listing_id' => $myListing->id,    'buyer_id' => $buyer->id, 'offered_price' => 100, 'payment_method' => 'cod', 'status' => 'pending']);
        Offer::create(['listing_id' => $otherListing->id, 'buyer_id' => $buyer->id, 'offered_price' => 200, 'payment_method' => 'cod', 'status' => 'pending']);

        $response = $this->actingAs($seller)->get('/dashboard/offers/received');
        $response->assertOk();
        $response->assertViewHas('offers', function ($offers) use ($myListing, $otherListing) {
            $ids = $offers->pluck('listing_id')->all();
            return in_array($myListing->id, $ids) && ! in_array($otherListing->id, $ids);
        });
    }
}
