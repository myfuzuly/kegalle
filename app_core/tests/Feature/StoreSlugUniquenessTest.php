<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreSlugUniquenessTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_slug_is_unique_in_database(): void
    {
        $user1 = User::factory()->create(['status' => 'active']);
        $user2 = User::factory()->create(['status' => 'active']);

        $store1 = Store::factory()->create(['user_id' => $user1->id, 'slug' => 'my-store']);

        // Attempt to create another store with same slug should fail at DB level
        $this->expectException(\Illuminate\Database\QueryException::class);
        Store::factory()->create(['user_id' => $user2->id, 'slug' => 'my-store']);
    }

    public function test_two_stores_can_have_different_slugs(): void
    {
        $user1 = User::factory()->create(['status' => 'active']);
        $user2 = User::factory()->create(['status' => 'active']);

        Store::factory()->create(['user_id' => $user1->id, 'slug' => 'store-alpha']);
        Store::factory()->create(['user_id' => $user2->id, 'slug' => 'store-beta']);

        $this->assertDatabaseHas('stores', ['slug' => 'store-alpha']);
        $this->assertDatabaseHas('stores', ['slug' => 'store-beta']);
    }

    public function test_creating_store_via_dashboard_deduplicates_slug(): void
    {
        $user1 = User::factory()->create(['status' => 'active', 'role' => 'user']);
        $user2 = User::factory()->create(['status' => 'active', 'role' => 'user']);

        // First store takes the base slug
        Store::factory()->create(['user_id' => $user1->id, 'name' => 'Kegalle Electronics', 'slug' => 'kegalle-electronics']);

        // Second user creates a store with the same name — slug is auto-deduplicated
        $response = $this->actingAs($user2)->post('/dashboard/stores', [
            'name'        => 'Kegalle Electronics',
            'description' => 'My electronics shop',
            'phone'       => '0711234567',
        ]);

        // Should redirect to the stores index (success)
        $response->assertRedirect();

        // Both stores exist; second one gets a deduplicated slug
        $this->assertDatabaseHas('stores', ['slug' => 'kegalle-electronics']);
        $this->assertDatabaseHas('stores', ['slug' => 'kegalle-electronics-1']);
    }
}
