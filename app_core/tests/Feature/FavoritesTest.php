<?php

namespace Tests\Feature;

use App\Models\Favorite;
use App\Models\Listing;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * End-to-end tests for the DB-backed favorites/wishlist feature.
 *
 * Guest behavior (localStorage) cannot be tested in PHPUnit —
 * those flows are covered by the JS unit tests and manual browser checks.
 */
class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    private function listingFixture(): array
    {
        $owner = User::factory()->create(['status' => 'active']);
        $store = Store::factory()->create(['user_id' => $owner->id, 'status' => 'approved']);
        $listing = Listing::factory()->create([
            'user_id'  => $owner->id,
            'store_id' => $store->id,
            'status'   => 'approved',
            'price'    => 15000,
        ]);
        return [$listing, $owner];
    }

    // ── Guest access ─────────────────────────────────────────────────────────

    public function test_guest_toggle_redirects_to_login(): void
    {
        [$listing] = $this->listingFixture();

        $this->post("/dashboard/favorites/{$listing->id}/toggle")
            ->assertRedirect('/login');
    }

    public function test_guest_cannot_view_favorites_page(): void
    {
        $this->get('/dashboard/favorites')->assertRedirect('/login');
    }

    // ── Auth user: adding a favorite ─────────────────────────────────────────

    public function test_auth_user_can_add_favorite(): void
    {
        [$listing] = $this->listingFixture();
        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($user)
            ->postJson("/dashboard/favorites/{$listing->id}/toggle");

        $response->assertStatus(200)
            ->assertJson(['favorited' => true]);

        $this->assertDatabaseHas('favorites', [
            'user_id'    => $user->id,
            'listing_id' => $listing->id,
        ]);
    }

    public function test_favorite_records_price_at_save(): void
    {
        [$listing] = $this->listingFixture();
        $user = User::factory()->create(['status' => 'active']);

        $this->actingAs($user)
            ->postJson("/dashboard/favorites/{$listing->id}/toggle");

        $this->assertDatabaseHas('favorites', [
            'user_id'       => $user->id,
            'listing_id'    => $listing->id,
            'price_at_save' => 15000,
        ]);
    }

    // ── Toggle off ───────────────────────────────────────────────────────────

    public function test_auth_user_can_remove_favorite(): void
    {
        [$listing] = $this->listingFixture();
        $user = User::factory()->create(['status' => 'active']);

        // Add it
        Favorite::create(['user_id' => $user->id, 'listing_id' => $listing->id]);

        // Toggle off
        $response = $this->actingAs($user)
            ->postJson("/dashboard/favorites/{$listing->id}/toggle");

        $response->assertStatus(200)
            ->assertJson(['favorited' => false]);

        $this->assertDatabaseMissing('favorites', [
            'user_id'    => $user->id,
            'listing_id' => $listing->id,
        ]);
    }

    public function test_toggle_is_idempotent_add_remove_add(): void
    {
        [$listing] = $this->listingFixture();
        $user = User::factory()->create(['status' => 'active']);

        $this->actingAs($user)->postJson("/dashboard/favorites/{$listing->id}/toggle")
            ->assertJson(['favorited' => true]);

        $this->actingAs($user)->postJson("/dashboard/favorites/{$listing->id}/toggle")
            ->assertJson(['favorited' => false]);

        $this->actingAs($user)->postJson("/dashboard/favorites/{$listing->id}/toggle")
            ->assertJson(['favorited' => true]);

        $this->assertDatabaseHas('favorites', ['user_id' => $user->id, 'listing_id' => $listing->id]);
    }

    // ── Isolation between users ──────────────────────────────────────────────

    public function test_favorites_are_user_scoped(): void
    {
        [$listing] = $this->listingFixture();
        $userA = User::factory()->create(['status' => 'active']);
        $userB = User::factory()->create(['status' => 'active']);

        $this->actingAs($userA)->postJson("/dashboard/favorites/{$listing->id}/toggle");

        // userB has no favorites
        $this->assertDatabaseMissing('favorites', [
            'user_id'    => $userB->id,
            'listing_id' => $listing->id,
        ]);
    }

    // ── Favorites dashboard page ──────────────────────────────────────────────

    public function test_favorites_page_loads_for_auth_user(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $this->actingAs($user)->get('/dashboard/favorites')->assertStatus(200);
    }

    public function test_favorites_page_shows_favorited_listings(): void
    {
        [$listing] = $this->listingFixture();
        $user = User::factory()->create(['status' => 'active']);
        Favorite::create(['user_id' => $user->id, 'listing_id' => $listing->id]);

        $response = $this->actingAs($user)->get('/dashboard/favorites');
        $response->assertStatus(200);
        $response->assertSee($listing->title);
    }

    public function test_favorites_page_empty_state_for_new_user(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $response = $this->actingAs($user)->get('/dashboard/favorites');
        $response->assertStatus(200);
        // Page loads even with zero favorites
        $this->assertEquals(0, Favorite::where('user_id', $user->id)->count());
    }

    // ── Nonexistent listing ───────────────────────────────────────────────────

    public function test_toggle_returns_404_for_missing_listing(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $this->actingAs($user)
            ->postJson('/dashboard/favorites/999999/toggle')
            ->assertStatus(404);
    }
}
