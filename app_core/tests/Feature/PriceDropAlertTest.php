<?php

namespace Tests\Feature;

use App\Models\Favorite;
use App\Models\Listing;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceDropAlertTest extends TestCase
{
    use RefreshDatabase;

    private function listing(float $price): Listing
    {
        $user  = User::factory()->create(['status' => 'active']);
        $store = Store::factory()->create(['user_id' => $user->id, 'status' => 'approved']);
        return Listing::factory()->create([
            'user_id'  => $user->id,
            'store_id' => $store->id,
            'status'   => 'approved',
            'price'    => $price,
            'slug'     => 'price-drop-' . uniqid(),
        ]);
    }

    public function test_favorite_records_price_at_save_on_toggle(): void
    {
        $user    = User::factory()->create(['status' => 'active']);
        $listing = $this->listing(5000.00);

        $this->actingAs($user)
            ->post("/dashboard/favorites/{$listing->id}/toggle");

        $this->assertDatabaseHas('favorites', [
            'user_id'       => $user->id,
            'listing_id'    => $listing->id,
            'price_at_save' => '5000.00',
        ]);
    }

    public function test_toggle_removes_existing_favorite(): void
    {
        $user    = User::factory()->create(['status' => 'active']);
        $listing = $this->listing(5000.00);

        $this->actingAs($user)->post("/dashboard/favorites/{$listing->id}/toggle");
        $this->actingAs($user)->post("/dashboard/favorites/{$listing->id}/toggle");

        $this->assertDatabaseMissing('favorites', [
            'user_id'    => $user->id,
            'listing_id' => $listing->id,
        ]);
    }
}
