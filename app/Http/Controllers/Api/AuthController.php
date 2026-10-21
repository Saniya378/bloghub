<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a new user and generate a Sanctum API token.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $username = $this->generateUniqueUsername($request->name);

        $user = User::create([
            'name' => $request->name,
            'username' => $username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'reader',
            'is_active' => true,
        ]);

        return $this->respondWithRegistrationToken($user);
    }

    /**
     * Register a new author account and generate a Sanctum API token.
     */
    public function registerAuthor(Request $request): JsonResponse
    {
        if ($request->has('email') && is_string($request->input('email'))) {
            $request->merge([
                'email' => strtolower(trim($request->input('email'))),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $username = $this->generateUniqueUsername($request->name);

        $user = User::create([
            'name' => $request->name,
            'username' => $username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'author',
            'is_active' => true,
        ]);

        return $this->respondWithRegistrationToken($user);
    }

    /**
     * Generate a clean unique username from the display name.
     */
    private function generateUniqueUsername(?string $name): string
    {
        $baseUsername = Str::slug($name ?? '', '');
        if (empty($baseUsername)) {
            $baseUsername = 'user';
        }
        $username = $baseUsername;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Build standard registration response with Sanctum bearer token.
     */
    private function respondWithRegistrationToken(User $user): JsonResponse
    {
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    /**
     * Authenticate user and return Sanctum API token.
     */
    public function login(Request $request): JsonResponse
    {
        $key = 'api_login_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($key);
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $identifier = $request->input('email');
        $user = User::where('email', $identifier)
            ->orWhere('username', $identifier)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key);
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials provided.',
            ], 401);
        }

        if (isset($user->is_active) && !$user->is_active) {
            RateLimiter::hit($key);
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact support.',
            ], 403);
        }

        RateLimiter::clear($key);

        // Revoke old tokens if device name is sent, or generate a new token
        $deviceName = $request->input('device_name', 'mobile_app');
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Get authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'unread_notifications_count' => $user->unreadNotificationsCount ?? 0,
            ],
        ]);
    }

    /**
     * Revoke current token / logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Request OTP for password reset.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $key = 'api_otp_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many OTP requests. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key),
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->input('email');
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put("otp_{$email}", $otp, 600); // 10 minutes

        RateLimiter::hit($key);

        return response()->json([
            'success' => true,
            'message' => 'OTP has been generated.',
            'otp' => $otp, // Useful for development testing
        ]);
    }

    /**
     * Verify OTP.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->input('email');
        $cachedOtp = Cache::get("otp_{$email}");

        if (!$cachedOtp || $cachedOtp !== $request->input('otp')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        // Generate temporary reset token
        $resetToken = Str::random(64);
        Cache::put("password_reset_token_{$email}", $resetToken, 900); // 15 min

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'reset_token' => $resetToken,
        ]);
    }

    /**
     * Reset Password using verified token.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'reset_token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->input('email');
        $cachedToken = Cache::get("password_reset_token_{$email}");

        if (!$cachedToken || $cachedToken !== $request->input('reset_token')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token.',
            ], 422);
        }

        $user = User::where('email', $email)->first();
        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        // Invalidate tokens and OTP
        Cache::forget("otp_{$email}");
        Cache::forget("password_reset_token_{$email}");

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. You can now log in.',
        ]);
    }
}
