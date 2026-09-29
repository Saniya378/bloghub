<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Handle contact form submission.
     */
    public function store(Request $request): JsonResponse
    {
        // Rate limiting: max 3 messages per minute per IP
        $key = 'contact_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many messages. Please try again later.',
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        ContactMessage::create($validator->validated());

        RateLimiter::hit($key);

        return response()->json([
            'success' => true,
            'message' => 'Thank you! Your message has been sent.',
        ]);
    }
}
