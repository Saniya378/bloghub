<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ==================== REGISTRATION TESTS ====================

    public function test_user_can_register_with_valid_credentials(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'role' => 'user',
            'is_active' => true,
        ]);
        $this->assertAuthenticated();
    }

    public function test_registration_requires_name(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'john@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertGuest();
    }

    public function test_registration_requires_valid_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'not-an-email',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'john@example.com']);

        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_requires_password_minimum_8_characters(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Different1',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_registration_requires_password_with_uppercase_lowercase_and_number(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'alllowercase1',
            'password_confirmation' => 'alllowercase1',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_registration_name_only_allows_letters_spaces_and_hyphens(): void
    {
        $response = $this->post('/register', [
            'name' => 'John123',
            'email' => 'john@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertGuest();
    }

    public function test_registration_rate_limiting(): void
    {
        // Make 3 registration attempts (the limit)
        for ($i = 0; $i < 3; $i++) {
            $this->post('/register', [
                'name' => "User $i",
                'email' => "user$i@example.com",
                'password' => 'Password1',
                'password_confirmation' => 'Password1',
            ]);
        }

        // 4th attempt should be rate limited
        $response = $this->post('/register', [
            'name' => 'Rate Limited',
            'email' => 'limited@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ==================== LOGIN TESTS ====================

    public function test_user_can_login_with_email_and_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'Password1',
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'Password1',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_with_username_and_password(): void
    {
        $user = User::factory()->create([
            'name' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'Password1',
        ]);

        $response = $this->post('/login', [
            'email' => 'testuser',
            'password' => 'Password1',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_requires_email(): void
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'Password1',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_requires_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'Password1',
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'WrongPassword1',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_fails_for_nonexistent_user(): void
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'Password1',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => 'Password1',
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'inactive@example.com',
            'password' => 'Password1',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_rate_limiting(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'Password1',
        ]);

        // Make 5 failed login attempts (the limit)
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'WrongPassword1',
            ]);
        }

        // 6th attempt should be rate limited
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'Password1',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ==================== LOGOUT TESTS ====================

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_guest_cannot_logout(): void
    {
        $response = $this->post('/logout');

        $response->assertRedirect('/login');
    }

    // ==================== SESSION SECURITY TESTS ====================

    public function test_session_is_invalidated_on_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $this->assertAuthenticated();

        $this->post('/logout');

        $this->assertGuest();
        $this->assertSessionMissing('_token');
    }

    public function test_remember_me_functionality(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'Password1',
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'Password1',
            'remember' => true,
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
        // Remember cookie should be set
        $this->assertNotNull($response->getCookie('remember_web_' . sha1(get_class($user))));
    }
}
