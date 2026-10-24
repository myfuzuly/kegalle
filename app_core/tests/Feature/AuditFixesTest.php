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
}
