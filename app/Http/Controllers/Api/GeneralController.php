<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class GeneralController extends Controller
{
    /**
     * Get platform statistics and general info for app landing.
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_articles' => Article::published()->count(),
            'total_authors' => User::whereIn('role', ['author', 'admin'])->where('is_active', true)->count(),
            'total_categories' => Category::count(),
            'total_reads' => (int) Article::published()->sum('views'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Submit contact form message.
     */
    public function contact(Request $request): JsonResponse
    {
        $key = 'api_contact_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many contact messages sent. Please try again later.',
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
                'errors' => $validator->errors(),
            ], 422);
        }

        ContactMessage::create($validator->validated());
        RateLimiter::hit($key);

        return response()->json([
            'success' => true,
            'message' => 'Your message has been sent successfully.',
        ]);
    }

    /**
     * Subscribe to newsletter.
     */
    public function subscribeNewsletter(Request $request): JsonResponse
    {
        $key = 'api_newsletter_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many subscription attempts. Please try again later.',
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:newsletter_subscribers,email',
        ], [
            'email.unique' => 'This email is already subscribed to our newsletter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        NewsletterSubscriber::create([
            'email' => $request->input('email'),
        ]);

        RateLimiter::hit($key);

        return response()->json([
            'success' => true,
            'message' => 'Subscribed to newsletter successfully.',
        ]);
    }

    /**
     * Get testimonials.
     */
    public function testimonials(): JsonResponse
    {
        $testimonials = Testimonial::where('is_active', true)->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $testimonials,
        ]);
    }
}
