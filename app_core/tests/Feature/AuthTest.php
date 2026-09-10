<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_register_page_loads(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_user_can_register(): void
    {
        Mail::fake();
        $location = $this->createLocation();

        $this->post('/register', [
            'account_type'          => 'user',
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'phone'                 => '0712345678',
            'location_id'           => $location->id,
            'password'              => 'SecurePass1!',
            'password_confirmation' => 'SecurePass1!',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_register_sends_verification_email(): void
    {
        Mail::fake();
        $location = $this->createLocation();

        $this->post('/register', [
            'account_type'          => 'user',
            'name'                  => 'Mail Test',
            'email'                 => 'mailtest@example.com',
            'phone'                 => '0712345679',
            'location_id'           => $location->id,
            'password'              => 'SecurePass1!',
            'password_confirmation' => 'SecurePass1!',
        ]);

        Mail::assertQueued(\App\Mail\VerifyAccountMail::class, function ($mail) {
            return $mail->hasTo('mailtest@example.com');
        });
    }

    public function test_honeypot_blocks_bot_registration(): void
    {
        $location = $this->createLocation();

        $this->post('/register', [
            'account_type' => 'user',
            'name'         => 'Bot',
            'email'        => 'bot@example.com',
            'phone'        => '0712345678',
            'location_id'  => $location->id,
            'password'     => 'SecurePass1!',
            'password_confirmation' => 'SecurePass1!',
            'website'      => 'http://spam.com', // honeypot
        ])->assertRedirect();

        $this->assertDatabaseMissing('users', ['email' => 'bot@example.com']);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123'), 'status' => 'active']);

        $this->post('/login', ['email' => $user->email, 'password' => 'password123'])
            ->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $this->post('/login', ['email' => $user->email, 'password' => 'wrongpass']);

        $this->assertGuest();
    }

    public function test_suspended_user_is_redirected_after_login(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123'), 'status' => 'suspended']);

        $this->post('/login', ['email' => $user->email, 'password' => 'password123']);
        $this->actingAs($user)->get('/dashboard')->assertRedirect('/account-suspended');
    }

    public function test_password_reset_returns_generic_message_for_unknown_email(): void
    {
        $this->post('/forgot-password', ['email' => 'nobody@example.com'])
            ->assertSessionHas('success');

        // Must not reveal that the email doesn't exist
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'nobody@example.com']);
    }

    public function test_password_reset_email_sent_for_known_email(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Mail::assertQueued(\App\Mail\PasswordResetMail::class, fn($m) => $m->hasTo($user->email));
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $this->actingAs($user)->get('/dashboard')->assertStatus(200);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_non_admin_cannot_access_make_admin_route(): void
    {
        $regularUser = User::factory()->create(['role' => 'user', 'status' => 'active']);
        $target      = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // A plain user is blocked by the is_admin middleware (403) before reaching the controller
        $this->actingAs($regularUser)
            ->post("/admin/users/{$target->id}/make-admin")
            ->assertStatus(403);

        // The target's role must remain unchanged
        $this->assertDatabaseHas('users', ['id' => $target->id, 'role' => 'user']);
    }

    public function test_super_admin_can_promote_to_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin', 'status' => 'active']);
        $target     = User::factory()->create(['role' => 'user', 'status' => 'active']);

        $this->actingAs($superAdmin)
            ->post("/admin/users/{$target->id}/make-admin")
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $target->id, 'role' => 'admin']);
    }
}
