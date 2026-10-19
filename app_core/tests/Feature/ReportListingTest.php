<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * End-to-end tests for the Report Listing feature.
 */
class ReportListingTest extends TestCase
{
    use RefreshDatabase;

    private function publishedListing(): Listing
    {
        $user  = User::factory()->create(['status' => 'active']);
        $store = Store::factory()->create(['user_id' => $user->id, 'status' => 'approved']);
        return Listing::factory()->create([
            'user_id'  => $user->id,
            'store_id' => $store->id,
            'status'   => 'approved',
            'title'    => 'Reportable Item',
        ]);
    }

    public function test_guest_report_redirects_to_login(): void
    {
        $listing = $this->publishedListing();

        $this->post("/listings/{$listing->id}/report", ['reason' => 'spam'])
            ->assertRedirect('/login');
    }

    public function test_authenticated_user_can_report(): void
    {
        $user    = User::factory()->create(['status' => 'active']);
        $listing = $this->publishedListing();

        $this->actingAs($user)
            ->postJson("/listings/{$listing->id}/report", ['reason' => 'counterfeit', 'details' => 'Looks fake.'])
            ->assertOk();

        $this->assertDatabaseHas('reports', ['user_id' => $user->id, 'listing_id' => $listing->id, 'reason' => 'counterfeit']);
    }

    public function test_duplicate_report_is_rejected(): void
    {
        $user    = User::factory()->create(['status' => 'active']);
        $listing = $this->publishedListing();

        $this->actingAs($user)->postJson("/listings/{$listing->id}/report", ['reason' => 'spam'])->assertOk();
        $this->actingAs($user)->postJson("/listings/{$listing->id}/report", ['reason' => 'spam'])->assertStatus(422);
    }

    public function test_report_requires_reason(): void
    {
        $user    = User::factory()->create(['status' => 'active']);
        $listing = $this->publishedListing();

        $this->actingAs($user)
            ->postJson("/listings/{$listing->id}/report", ['details' => 'No reason'])
            ->assertJsonValidationErrors('reason');
    }

    public function test_report_for_nonexistent_listing_returns_404(): void
    {
        $user = User::factory()->create(['status' => 'active']);

        $this->actingAs($user)->postJson('/listings/999999/report', ['reason' => 'spam'])->assertStatus(404);
    }

    public function test_report_is_throttled(): void
    {
        $user    = User::factory()->create(['status' => 'active']);
        $listing = $this->publishedListing();

        // throttle:5,10 — the 6th request in 10 minutes is refused
        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($user)->postJson("/listings/{$listing->id}/report", ['reason' => 'spam']);
        }

        $this->actingAs($user)->postJson("/listings/{$listing->id}/report", ['reason' => 'spam'])->assertStatus(429);
    }
}
