<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Event;
use App\Models\Listing;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke tests for all public-facing pages.
 * Confirms each route returns 200 and doesn't throw an unhandled exception.
 * Does not assert exact content beyond basic sanity.
 */
class FrontendPagesTest extends TestCase
{
    use RefreshDatabase;

    // ── Static public pages ───────────────────────────────────────────────────

    public function test_homepage_loads(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_about_page_loads(): void
    {
        $this->get('/about-us')->assertStatus(200);
    }

    public function test_contact_page_loads(): void
    {
        $this->get('/contact-us')->assertStatus(200);
    }

    public function test_privacy_page_loads(): void
    {
        $this->get('/privacy-policy')->assertStatus(200);
    }

    public function test_terms_page_loads(): void
    {
        $this->get('/terms-and-conditions')->assertStatus(200);
    }

    // ── Auth pages ────────────────────────────────────────────────────────────

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_register_page_loads(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_forgot_password_page_loads(): void
    {
        $this->get('/forgot-password')->assertStatus(200);
    }

    // ── Listings ──────────────────────────────────────────────────────────────

    public function test_listings_explore_page_loads(): void
    {
        $this->get('/explore')->assertStatus(200);
    }

    public function test_listing_show_page_loads(): void
    {
        $seller  = User::factory()->create(['status' => 'active']);
        $store   = Store::factory()->create(['user_id' => $seller->id, 'status' => 'approved']);
        $listing = Listing::factory()->create([
            'user_id'  => $seller->id,
            'store_id' => $store->id,
            'status'   => 'approved',
            'slug'     => 'test-item-smoke',
        ]);

        $this->get("/listings/{$listing->slug}")->assertStatus(200);
    }

    public function test_404_for_nonexistent_listing(): void
    {
        $this->get('/listings/no-such-listing-xyz')->assertStatus(404);
    }

    // ── Stores ────────────────────────────────────────────────────────────────

    public function test_stores_page_loads(): void
    {
        $this->get('/stores')->assertStatus(200);
    }

    public function test_store_show_page_loads(): void
    {
        $seller = User::factory()->create(['status' => 'active']);
        $store  = Store::factory()->create([
            'user_id' => $seller->id,
            'status'  => 'approved',
            'slug'    => 'smoke-test-store',
        ]);

        $this->get("/store/{$store->slug}")->assertStatus(200);
    }

    // ── Blog ──────────────────────────────────────────────────────────────────

    public function test_blog_index_loads(): void
    {
        $this->get('/blog')->assertStatus(200);
    }

    public function test_blog_post_loads(): void
    {
        $post = Post::factory()->create(['slug' => 'smoke-test-post', 'is_published' => true]);
        $this->get("/blog/{$post->slug}")->assertStatus(200);
    }

    // ── Events ────────────────────────────────────────────────────────────────

    public function test_events_page_loads(): void
    {
        $this->get('/events')->assertStatus(200);
    }

    public function test_event_show_loads(): void
    {
        $event = Event::factory()->create([
            'slug'       => 'smoke-test-event-' . uniqid(),
            'status'     => 'published',
            'event_date' => now()->addWeek()->toDateString(),
        ]);
        $this->get("/events/{$event->slug}")->assertStatus(200);
    }

    // ── Search ────────────────────────────────────────────────────────────────

    public function test_search_returns_results_page(): void
    {
        // Search is handled by the listings index with a query parameter
        $this->get('/listings?q=test')->assertStatus(200);
    }

    public function test_search_with_no_query_loads(): void
    {
        $this->get('/listings')->assertStatus(200);
    }

    // ── Categories ────────────────────────────────────────────────────────────

    public function test_categories_page_loads(): void
    {
        $this->get('/categories')->assertStatus(200);
    }

    // ── Dashboard (auth-gated) ────────────────────────────────────────────────

    public function test_dashboard_redirects_guest(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_dashboard_loads_for_authenticated_user(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $this->actingAs($user)->get('/dashboard')->assertStatus(200);
    }

    public function test_dashboard_listings_loads(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $this->actingAs($user)->get('/dashboard/listings')->assertStatus(200);
    }

    public function test_dashboard_offers_loads(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $this->actingAs($user)->get('/dashboard/offers')->assertStatus(200);
    }

    public function test_dashboard_chat_loads(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $this->actingAs($user)->get('/dashboard/chat')->assertStatus(200);
    }

    public function test_profile_page_loads(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $this->actingAs($user)->get('/dashboard/profile')->assertStatus(200);
    }

    // ── Admin (gated) ─────────────────────────────────────────────────────────

    public function test_admin_dashboard_redirects_regular_user(): void
    {
        $user = User::factory()->create(['status' => 'active', 'role' => 'user']);
        $this->actingAs($user)->get('/admin')->assertStatus(403);
    }

    public function test_admin_dashboard_loads_for_admin(): void
    {
        $admin = User::factory()->create(['status' => 'active', 'role' => 'admin']);
        $this->actingAs($admin)->get('/admin')->assertStatus(200);
    }

    public function test_admin_listings_page_loads(): void
    {
        $admin = User::factory()->create(['status' => 'active', 'role' => 'admin']);
        $this->actingAs($admin)->get('/admin/listings')->assertStatus(200);
    }

    public function test_admin_users_page_loads(): void
    {
        $admin = User::factory()->create(['status' => 'active', 'role' => 'admin']);
        $this->actingAs($admin)->get('/admin/users')->assertStatus(200);
    }

    public function test_admin_stores_page_loads(): void
    {
        $admin = User::factory()->create(['status' => 'active', 'role' => 'admin']);
        $this->actingAs($admin)->get('/admin/stores')->assertStatus(200);
    }

    // ── Error pages ───────────────────────────────────────────────────────────

    public function test_404_page_returns_404_status(): void
    {
        $this->get('/this-route-does-not-exist-abc123')->assertStatus(404);
    }
}
