<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class OtpFlowTest extends TestCase
{
    use RefreshDatabase;

    // ==================== OTP REQUEST TESTS ====================

    public function test_user_can_request_otp(): void
    {
        $user = User::factory()->create(['email' => 'otp@example.com']);

        $response = $this->post('/forgot-password', [
            'email' => 'otp@example.com',
        ]);

        $response->assertStatus(200);
        // OTP should be stored in cache
        $this->assertTrue(Cache::has('otp_otp@example.com'));
    }

    public function test_otp_request_requires_valid_email(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_otp_request_rate_limiting(): void
    {
        $user = User::factory()->create(['email' => 'otp@example.com']);

        // Make 3 OTP requests (the limit)
        for ($i = 0; $i < 3; $i++) {
            $this->post('/forgot-password', [
                'email' => 'otp@example.com',
            ]);
        }

        // 4th attempt should be rate limited
        $response = $this->post('/forgot-password', [
            'email' => 'otp@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ==================== OTP VERIFICATION TESTS ====================

    public function test_user_can_verify_otp(): void
    {
        $user = User::factory()->create(['email' => 'otp@example.com']);
        $otp = '123456';
        Cache::put('otp_otp@example.com', $otp, 600);

        $response = $this->post('/verify-otp', [
            'email' => 'otp@example.com',
            'otp' => $otp,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_otp_verification_fails_with_invalid_otp(): void
    {
        $user = User::factory()->create(['email' => 'otp@example.com']);
        Cache::put('otp_otp@example.com', '123456', 600);

        $response = $this->post('/verify-otp', [
            'email' => 'otp@example.com',
            'otp' => '999999',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    public function test_otp_verification_fails_with_expired_otp(): void
    {
        $user = User::factory()->create(['email' => 'otp@example.com']);
        // OTP not in cache (expired)

        $response = $this->post('/verify-otp', [
            'email' => 'otp@example.com',
            'otp' => '123456',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    public function test_otp_verification_requires_email(): void
    {
        $response = $this->post('/verify-otp', [
            'email' => '',
            'otp' => '123456',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_otp_verification_requires_6_digit_otp(): void
    {
        $user = User::factory()->create(['email' => 'otp@example.com']);

        $response = $this->post('/verify-otp', [
            'email' => 'otp@example.com',
            'otp' => '123',
        ]);

        $response->assertSessionHasErrors('otp');
    }

    // ==================== PASSWORD RESET TESTS ====================

    public function test_user_can_reset_password_after_otp_verification(): void
    {
        $user = User::factory()->create([
            'email' => 'otp@example.com',
            'password' => 'OldPassword1',
        ]);
        $otp = '123456';
        Cache::put('otp_otp@example.com', $otp, 600);
        Cache::put('otp_verified_otp@example.com', true, 600);

        $response = $this->post('/reset-password', [
            'email' => 'otp@example.com',
            'password' => 'NewPassword1',
            'password_confirmation' => 'NewPassword1',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify password was changed
        $user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('NewPassword1', $user->password));
    }

    public function test_password_reset_requires_otp_verification(): void
    {
        $user = User::factory()->create([
            'email' => 'otp@example.com',
            'password' => 'OldPassword1',
        ]);
        // OTP not verified

        $response = $this->post('/reset-password', [
            'email' => 'otp@example.com',
            'password' => 'NewPassword1',
            'password_confirmation' => 'NewPassword1',
        ]);

        $response->assertStatus(403);
        $response->assertJson(['success' => false, 'message' => 'OTP verification required']);
    }

    public function test_password_reset_requires_valid_email(): void
    {
        $response = $this->post('/reset-password', [
            'email' => 'nonexistent@example.com',
            'password' => 'NewPassword1',
            'password_confirmation' => 'NewPassword1',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_password_reset_requires_password_confirmation(): void
    {
        $user = User::factory()->create(['email' => 'otp@example.com']);
        Cache::put('otp_verified_otp@example.com', true, 600);

        $response = $this->post('/reset-password', [
            'email' => 'otp@example.com',
            'password' => 'NewPassword1',
            'password_confirmation' => 'Different1',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // ==================== OTP EXPIRATION TESTS ====================

    public function test_otp_expires_after_10_minutes(): void
    {
        $user = User::factory()->create(['email' => 'otp@example.com']);
        $otp = '123456';
        // Simulate OTP created 11 minutes ago
        Cache::put('otp_otp@example.com', $otp, -60);

        $response = $this->post('/verify-otp', [
            'email' => 'otp@example.com',
            'otp' => $otp,
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    public function test_otp_is_single_use(): void
    {
        $user = User::factory()->create(['email' => 'otp@example.com']);
        $otp = '123456';
        Cache::put('otp_otp@example.com', $otp, 600);

        // First use
        $this->post('/verify-otp', [
            'email' => 'otp@example.com',
            'otp' => $otp,
        ]);

        // Second use should fail
        $response = $this->post('/verify-otp', [
            'email' => 'otp@example.com',
            'otp' => $otp,
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }
}
