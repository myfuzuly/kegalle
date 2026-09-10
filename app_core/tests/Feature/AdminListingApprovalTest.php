<?php

namespace Tests\Feature;

use App\Mail\ListingStatusMail;
use App\Models\Listing;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminListingApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    private function pendingListing(): Listing
    {
        $user  = User::factory()->create(['status' => 'active', 'role' => 'user']);
        $store = Store::factory()->create(['user_id' => $user->id, 'status' => 'approved']);
        return Listing::factory()->create([
            'user_id'  => $user->id,
            'store_id' => $store->id,
            'status'   => 'pending',
            'slug'     => 'test-listing-'.uniqid(),
        ]);
    }

    public function test_admin_can_approve_listing(): void
    {
        Mail::fake();
        $admin   = $this->admin();
        $listing = $this->pendingListing();

        $this->actingAs($admin)
            ->post("/admin/listings/{$listing->id}/approve")
            ->assertRedirect();

        $this->assertDatabaseHas('listings', [
            'id'     => $listing->id,
            'status' => 'approved',
        ]);
    }

    public function test_approval_sends_status_email(): void
    {
        Mail::fake();
        $admin   = $this->admin();
        $listing = $this->pendingListing();

        $this->actingAs($admin)
            ->post("/admin/listings/{$listing->id}/approve");

        Mail::assertQueued(ListingStatusMail::class, function ($mail) use ($listing) {
            return $mail->listing->id === $listing->id;
        });
    }

    public function test_admin_can_reject_listing(): void
    {
        Mail::fake();
        $admin   = $this->admin();
        $listing = $this->pendingListing();

        $this->actingAs($admin)
            ->post("/admin/listings/{$listing->id}/reject", ['reason' => 'Does not meet guidelines'])
            ->assertRedirect();

        $this->assertDatabaseHas('listings', [
            'id'     => $listing->id,
            'status' => 'rejected',
        ]);
    }

    public function test_rejection_sends_status_email(): void
    {
        Mail::fake();
        $admin   = $this->admin();
        $listing = $this->pendingListing();

        $this->actingAs($admin)
            ->post("/admin/listings/{$listing->id}/reject", ['reason' => 'Spam']);

        Mail::assertQueued(ListingStatusMail::class, function ($mail) use ($listing) {
            return $mail->listing->id === $listing->id;
        });
    }

    public function test_non_admin_cannot_approve_listing(): void
    {
        $user    = User::factory()->create(['role' => 'user', 'status' => 'active']);
        $listing = $this->pendingListing();

        $this->actingAs($user)
            ->post("/admin/listings/{$listing->id}/approve")
            ->assertStatus(403);
    }
}
