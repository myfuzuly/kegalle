<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    private function seller(): User
    {
        return User::factory()->create(['status' => 'active', 'role' => 'user']);
    }

    public function test_guest_cannot_create_store(): void
    {
        $this->get('/dashboard/stores/create')->assertRedirect('/login');
    }

    public function test_seller_can_view_create_store_form(): void
    {
        $this->actingAs($this->seller())
            ->get('/dashboard/stores/create')
            ->assertStatus(200);
    }

    public function test_seller_can_create_store(): void
    {
        $user = $this->seller();

        $response = $this->actingAs($user)->post('/dashboard/stores', [
            'name' => 'My Test Store',
            'phone' => '0712345678',
            'email' => 'store@test.com',
            'city' => 'Kegalle',
        ]);

        $response->assertRedirect(route('dashboard.stores.index'));
        $this->assertDatabaseHas('stores', [
            'name' => 'My Test Store',
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_store_name_is_required(): void
    {
        $user = $this->seller();

        $response = $this->actingAs($user)->post('/dashboard/stores', [
            'phone' => '0712345678',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_seller_cannot_exceed_store_limit(): void
    {
        $user = $this->seller();
        $user->update(['store_limit' => 1]);
        Store::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post('/dashboard/stores', [
            'name' => 'Second Store',
        ]);

        $response->assertRedirect(route('dashboard.stores.index'));
    }

    public function test_seller_can_update_own_store(): void
    {
        $user = $this->seller();
        $store = Store::factory()->create(['user_id' => $user->id, 'name' => 'Old Name']);

        $response = $this->actingAs($user)->put("/dashboard/stores/{$store->id}", [
            'name' => 'New Name',
            'city' => 'Mawanella',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('stores', ['id' => $store->id, 'name' => 'New Name']);
    }

    public function test_seller_cannot_update_others_store(): void
    {
        $owner = $this->seller();
        $other = $this->seller();
        $store = Store::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($other)->put("/dashboard/stores/{$store->id}", [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(403);
    }

    public function test_phone_normalized_to_sri_lankan_format(): void
    {
        $user = $this->seller();

        $this->actingAs($user)->post('/dashboard/stores', [
            'name' => 'Phone Test Store',
            'phone' => '0712345678',
        ]);

        $this->assertDatabaseHas('stores', [
            'user_id' => $user->id,
            'phone' => '+94712345678',
        ]);
    }
}
