<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    /**
     * Request OTP for password reset.
     */
    public function requestOtp(Request $request): JsonResponse
    {
        // Rate limiting: max 3 OTP requests per minute
        $key = 'otp_request_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'error' => 'Too many OTP requests. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key),
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $email = $request->input('email');

        // Generate 6-digit OTP
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in cache for 10 minutes
        Cache::put("otp_{$email}", $otp, 600);

        RateLimiter::hit($key);

        // In production, send OTP via email
        // For testing, we return it in response
        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email.',
            'otp' => $otp, // Remove in production!
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
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $email = $request->input('email');
        $otp = $request->input('otp');

        $storedOtp = Cache::get("otp_{$email}");

        if (!$storedOtp) {
            return response()->json(['success' => false, 'message' => 'OTP expired or not found'], 400);
        }

        if (!hash_equals($storedOtp, $otp)) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP'], 400);
        }

        // Mark OTP as verified
        Cache::put("otp_verified_{$email}", true, 600);

        // Delete OTP (single use)
        Cache::forget("otp_{$email}");

        return response()->json(['success' => true, 'message' => 'OTP verified successfully']);
    }

    /**
     * Reset password after OTP verification.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $email = $request->input('email');

        // Check if OTP was verified
        if (!Cache::get("otp_verified_{$email}")) {
            return response()->json(['success' => false, 'message' => 'OTP verification required'], 403);
        }

        $user = User::where('email', $email)->first();
        $user->update(['password' => $request->input('password')]);

        // Clear OTP verification
        Cache::forget("otp_verified_{$email}");

        return response()->json(['success' => true, 'message' => 'Password reset successfully']);
    }
}
