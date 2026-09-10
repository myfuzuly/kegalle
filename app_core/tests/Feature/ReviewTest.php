<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_review_listing(): void
    {
        $seller = User::factory()->create(['status' => 'active']);
        $buyer = User::factory()->create(['status' => 'active']);
        $store = Store::factory()->create(['user_id' => $seller->id]);
        $listing = Listing::factory()->create([
            'user_id' => $seller->id,
            'store_id' => $store->id,
            'slug' => 'review-test',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($buyer)->post("/listings/{$listing->id}/review", [
            'rating' => 5,
            'comment' => 'Great product, highly recommend!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'listing_id' => $listing->id,
            'user_id' => $buyer->id,
            'rating' => 5,
        ]);
    }

    public function test_guest_cannot_review_listing(): void
    {
        $seller = User::factory()->create(['status' => 'active']);
        $store = Store::factory()->create(['user_id' => $seller->id]);
        $listing = Listing::factory()->create([
            'user_id' => $seller->id,
            'store_id' => $store->id,
            'slug' => 'guest-review',
        ]);

        $response = $this->post("/listings/{$listing->id}/review", [
            'rating' => 5,
            'comment' => 'Should not work',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_review_store(): void
    {
        $seller = User::factory()->create(['status' => 'active']);
        $buyer = User::factory()->create(['status' => 'active']);
        $store = Store::factory()->create(['user_id' => $seller->id, 'status' => 'approved']);

        $response = $this->actingAs($buyer)->post("/store/{$store->id}/review", [
            'rating' => 4,
            'comment' => 'Good store with quality products',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'store_id' => $store->id,
            'user_id' => $buyer->id,
            'rating' => 4,
        ]);
    }

    public function test_review_rating_must_be_between_1_and_5(): void
    {
        $seller = User::factory()->create(['status' => 'active']);
        $buyer = User::factory()->create(['status' => 'active']);
        $store = Store::factory()->create(['user_id' => $seller->id]);
        $listing = Listing::factory()->create([
            'user_id' => $seller->id,
            'store_id' => $store->id,
            'slug' => 'rating-test',
        ]);

        $response = $this->actingAs($buyer)->post("/listings/{$listing->id}/review", [
            'rating' => 6,
            'comment' => 'Invalid rating',
        ]);

        $response->assertSessionHasErrors('rating');
    }
}
