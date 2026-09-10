<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingTest extends TestCase
{
    use RefreshDatabase;

    private function sellerWithStore(): array
    {
        $user = User::factory()->create(['status' => 'active', 'role' => 'user']);
        $store = Store::factory()->create(['user_id' => $user->id, 'status' => 'approved']);
        return [$user, $store];
    }

    public function test_guest_cannot_create_listing(): void
    {
        $this->get('/dashboard/listings/create')->assertRedirect('/login');
    }

    public function test_seller_can_view_create_listing_form(): void
    {
        [$user] = $this->sellerWithStore();
        $this->actingAs($user)
            ->get('/dashboard/listings/create')
            ->assertStatus(200);
    }

    public function test_seller_can_create_listing(): void
    {
        [$user, $store] = $this->sellerWithStore();

        $response = $this->actingAs($user)->post('/dashboard/listings', [
            'title' => 'Samsung Galaxy S24',
            'description' => 'Brand new phone for sale',
            'price' => 150000,
            'store_id' => $store->id,
        ]);

        $response->assertRedirect('/dashboard/listings');
        $this->assertDatabaseHas('listings', [
            'title' => 'Samsung Galaxy S24',
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_listing_title_is_required(): void
    {
        [$user] = $this->sellerWithStore();

        $response = $this->actingAs($user)->post('/dashboard/listings', [
            'description' => 'No title here',
            'price' => 1000,
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_listing_description_min_length(): void
    {
        [$user] = $this->sellerWithStore();

        $response = $this->actingAs($user)->post('/dashboard/listings', [
            'title' => 'Test',
            'description' => 'Hi',
            'price' => 1000,
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_seller_can_update_own_listing(): void
    {
        [$user, $store] = $this->sellerWithStore();
        $listing = Listing::factory()->create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'title' => 'Old Title',
            'description' => 'Old description text',
            'slug' => 'old-title',
        ]);

        $response = $this->actingAs($user)->put("/dashboard/listings/{$listing->id}", [
            'title' => 'Updated Title',
            'description' => 'Updated description text',
            'price' => 2000,
        ]);

        $response->assertRedirect('/dashboard/listings');
        $this->assertDatabaseHas('listings', ['id' => $listing->id, 'title' => 'Updated Title']);
    }

    public function test_seller_cannot_update_others_listing(): void
    {
        [$owner, $store] = $this->sellerWithStore();
        $other = User::factory()->create(['status' => 'active']);
        $listing = Listing::factory()->create([
            'user_id' => $owner->id,
            'store_id' => $store->id,
            'slug' => 'test-listing',
        ]);

        $response = $this->actingAs($other)->put("/dashboard/listings/{$listing->id}", [
            'title' => 'Hacked',
            'description' => 'Hacked description',
        ]);

        $response->assertStatus(403);
    }

    public function test_seller_can_delete_own_listing(): void
    {
        [$user, $store] = $this->sellerWithStore();
        $listing = Listing::factory()->create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'slug' => 'delete-me',
        ]);

        $response = $this->actingAs($user)->delete("/dashboard/listings/{$listing->id}");

        $response->assertRedirect('/dashboard/listings');
        $this->assertDatabaseMissing('listings', ['id' => $listing->id]);
    }
}
