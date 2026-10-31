<?php

namespace Tests\Feature;

use App\Helpers\HtmlSanitizer;
use App\Models\Listing;
use App\Models\Store;
use App\Models\User;
use App\Services\ListingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** Regression tests for the October 2026 audit fixes. */
class AuditFixesTest extends TestCase
{
    use RefreshDatabase;

    public function test_sanitizer_strips_obfuscated_javascript_links_but_keeps_normal_ones(): void
    {
        $this->assertStringNotContainsString('href', HtmlSanitizer::clean('<a href="jav&#x09;ascript:alert(1)">x</a>'));
        $this->assertStringNotContainsString('href', HtmlSanitizer::clean('<a href=" javascript:alert(1)">x</a>'));
        $this->assertStringContainsString('href="https://kegalle.com"', HtmlSanitizer::clean('<a href="https://kegalle.com">x</a>'));
        $this->assertStringContainsString('href="/listings"', HtmlSanitizer::clean('<a href="/listings">x</a>'));
        $this->assertStringNotContainsString('src', HtmlSanitizer::clean('<img src="data:text/html,x">'));
    }

    public function test_login_otp_is_burned_after_five_wrong_guesses(): void
    {
        $user = User::factory()->create(['status' => 'active', 'phone' => '0771234567']);
        $id = DB::table('phone_otps')->insertGetId([
            'user_id' => $user->id, 'phone' => '0771234567', 'code' => Hash::make('123456'),
            'used' => false, 'expires_at' => now()->addMinutes(10), 'created_at' => now(), 'updated_at' => now(),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/phone/login-otp/verify', ['phone' => '0771234567', 'code' => '000000']);
        }

        $this->assertEquals(1, DB::table('phone_otps')->where('id', $id)->value('used'));
        $this->post('/phone/login-otp/verify', ['phone' => '0771234567', 'code' => '123456']);
        $this->assertGuest();
    }

    public function test_changing_phone_clears_phone_verification(): void
    {
        $this->createLocation();
        $user = User::factory()->create(['status' => 'active', 'phone' => '0771234567', 'phone_verified_at' => now()]);

        $this->actingAs($user)->put('/dashboard/profile', [
            'name' => $user->name, 'email' => $user->email, 'phone' => '0779999999',
            'location_id' => \App\Models\Location::first()->id,
        ]);

        $this->assertNull($user->fresh()->phone_verified_at);
    }

    public function test_early_membership_renewal_keeps_remaining_days(): void
    {
        $admin = User::factory()->create(['status' => 'active', 'role' => 'super_admin']);
        $owner = User::factory()->create(['status' => 'active']);
        $store = Store::factory()->create(['user_id' => $owner->id, 'membership_expires_at' => now()->addDays(20)]);
        $planId = DB::table('membership_plans')->insertGetId([
            'name' => 'Gold', 'slug' => 'gold', 'price' => 1000, 'duration_days' => 30, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $paymentId = DB::table('payments')->insertGetId([
            'user_id' => $owner->id, 'membership_plan_id' => $planId, 'amount' => 1000, 'currency' => 'LKR',
            'status' => 'pending', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->actingAs($admin)->post("/admin/payments/{$paymentId}/approve");

        $this->assertEqualsWithDelta(50, now()->diffInDays($store->fresh()->membership_expires_at), 1);
        $this->assertEquals('paid', DB::table('payments')->where('id', $paymentId)->value('status'));
    }

    public function test_expired_classified_is_hidden_and_sinhala_title_gets_a_slug(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $store = Store::factory()->create(['user_id' => $user->id, 'status' => 'approved']);
        Listing::factory()->create(['user_id' => $user->id, 'store_id' => $store->id, 'status' => 'approved', 'expires_at' => now()->subDay()]);
        $live = Listing::factory()->create(['user_id' => $user->id, 'store_id' => $store->id, 'status' => 'approved', 'expires_at' => null]);

        $this->assertEquals([$live->id], Listing::published()->pluck('id')->all());
        $this->assertStringStartsWith('ad', app(ListingService::class)->generateSlug('කෑගල්ල ඉඩම'));
    }

    public function test_cached_page_gets_this_requests_csrf_token(): void
    {
        $this->get('/listings')->assertOk(); // fills the page cache

        $html = $this->get('/listings')->getContent();
        preg_match('/<meta name="csrf-token" content="([^"]*)"/', $html, $m);

        $this->assertSame(csrf_token(), $m[1] ?? null);
    }

    public function test_cached_page_nonces_match_the_csp_header(): void
    {
        $this->get('/listings')->assertOk();

        $res = $this->get('/listings');
        preg_match("/'nonce-([^']+)'/", $res->headers->get('Content-Security-Policy'), $h);
        preg_match_all('/nonce="([^"]+)"/', $res->getContent(), $tags);

        $this->assertNotEmpty($tags[1]);
        $this->assertSame([$h[1]], array_values(array_unique($tags[1])));
    }

    public function test_listing_pages_do_not_query_per_card(): void
    {
        $owner = User::factory()->create(['status' => 'active']);
        foreach (Store::factory()->count(5)->create(['user_id' => $owner->id, 'status' => 'approved']) as $store) {
            Listing::factory()->count(4)->create(['user_id' => $owner->id, 'store_id' => $store->id, 'status' => 'approved']);
        }

        DB::enableQueryLog();
        $this->get('/listings?fresh=1')->assertOk();
        $queries = count(DB::getQueryLog());

        // 20 cards across 5 stores used to cost ~3 store-stat queries per card
        $this->assertLessThan(40, $queries, "listings page ran {$queries} queries");
    }

    public function test_home_has_no_splash_and_pins_featured_ads_first(): void
    {
        $owner = User::factory()->create(['status' => 'active']);
        $store = Store::factory()->create(['user_id' => $owner->id, 'status' => 'approved']);
        $featured = Listing::factory()->create(['user_id' => $owner->id, 'store_id' => $store->id, 'status' => 'approved', 'is_featured' => 1, 'created_at' => now()->subDays(5)]);
        Listing::factory()->count(3)->create(['user_id' => $owner->id, 'store_id' => $store->id, 'status' => 'approved']);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('id="k-preloader"', $html);
        $grid = substr($html, strpos($html, 'id="k-latest-grid"'));
        $this->assertStringStartsWith('/listings/' . $featured->slug, substr($grid, strpos($grid, '/listings/')));
    }

    public function test_home_shows_sell_band_and_ad_page_has_phone_contact_bar(): void
    {
        $this->get('/')->assertOk()->assertSee('Got something to sell?')->assertSee('Search ads in Kegalle');

        $owner = User::factory()->create(['status' => 'active', 'phone' => '0771234567']);
        $store = Store::factory()->create(['user_id' => $owner->id, 'status' => 'approved', 'phone' => '0771234567']);
        $ad = Listing::factory()->create(['user_id' => $owner->id, 'store_id' => $store->id, 'status' => 'approved']);

        $this->get('/listings/' . $ad->slug)->assertOk()->assertSee('class="k-contact-bar"', false);
        $this->actingAs($owner)->get('/listings/' . $ad->slug)->assertOk()->assertDontSee('class="k-contact-bar"', false);
    }

    public function test_dashboard_offers_store_after_three_approved_ads(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $this->actingAs($user)->get('/dashboard')->assertOk()->assertDontSee('dbiStoreNudge');

        Listing::factory()->count(3)->create(['user_id' => $user->id, 'store_id' => null, 'status' => 'approved']);
        \Illuminate\Support\Facades\Cache::flush();
        $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('dbiStoreNudge');
    }

    private function moderator(): User
    {
        DB::table('roles')->insert(['key' => 'moderator', 'name' => 'Moderator', 'is_admin_level' => true, 'is_super' => false,
            'permissions' => json_encode(['dashboard', 'users']), 'created_at' => now(), 'updated_at' => now()]);
        return User::factory()->create(['status' => 'active', 'role' => 'moderator']);
    }

    public function test_moderator_cannot_promote_self_or_take_over_super_admin(): void
    {
        $mod   = $this->moderator();
        $super = User::factory()->create(['status' => 'active', 'role' => 'super_admin', 'email' => 'boss@kegalle.com']);

        $this->actingAs($mod)->patch("/admin/users/{$mod->id}/role", ['role' => 'super_admin'])->assertForbidden();
        $this->actingAs($mod)->put("/admin/users/{$super->id}", [
            'name' => 'x', 'email' => 'mine@evil.com', 'role' => 'super_admin', 'status' => 'active',
            'password' => 'newpassword1', 'password_confirmation' => 'newpassword1',
        ])->assertForbidden();
        $this->actingAs($mod)->post("/admin/users/{$super->id}/suspend")->assertForbidden();

        $this->assertSame('moderator', $mod->fresh()->role);
        $this->assertSame('boss@kegalle.com', $super->fresh()->email);
        $this->assertSame('active', $super->fresh()->status);
    }

    public function test_admin_edit_saves_role_and_status(): void
    {
        $super = User::factory()->create(['status' => 'active', 'role' => 'super_admin']);
        $user  = User::factory()->create(['status' => 'active', 'role' => 'user']);

        $this->actingAs($super)->put("/admin/users/{$user->id}", [
            'name' => $user->name, 'email' => $user->email, 'role' => 'seller', 'status' => 'suspended',
        ]);

        $this->assertSame('seller', $user->fresh()->role);
        $this->assertSame('suspended', $user->fresh()->status);
    }

    public function test_csv_cells_cannot_start_formulas(): void
    {
        $h = fopen('php://memory', 'w+');
        \App\Helpers\Csv::put($h, ['=HYPERLINK("x")', 'plain', '@SUM(1)']);
        rewind($h);
        $this->assertSame("\"'=HYPERLINK(\"\"x\"\")\",plain,'@SUM(1)\n", stream_get_contents($h));
    }

    public function test_blocked_user_cannot_send_offer_and_offers_need_a_live_ad(): void
    {
        $seller = User::factory()->create(['status' => 'active']);
        $buyer  = User::factory()->create(['status' => 'active']);
        $store  = Store::factory()->create(['user_id' => $seller->id, 'status' => 'approved']);
        $live   = Listing::factory()->create(['user_id' => $seller->id, 'store_id' => $store->id, 'status' => 'approved']);
        $sold   = Listing::factory()->create(['user_id' => $seller->id, 'store_id' => $store->id, 'status' => 'sold']);
        $offer  = ['payment_method' => 'cod', 'offered_price' => 100];

        $this->actingAs($buyer)->post("/listings/{$sold->id}/offer", $offer)->assertNotFound();

        \App\Models\UserBlock::create(['blocker_id' => $seller->id, 'blocked_id' => $buyer->id]);
        $this->actingAs($buyer)->post("/listings/{$live->id}/offer", $offer)->assertForbidden();
    }
}
