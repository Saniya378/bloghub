<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * All blog posts data (static array — no database).
     * Har post ka apna unique "slug" hai jo URL mein use hota hai.
     */
    protected function posts()
    {
        return [

            'future-of-ai' => [
                'title'       => 'The Future of Artificial Intelligence',
                'category'    => 'Technology',
                'badge_color' => 'bg-danger',
                'image'       => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSVTC-sGcFxRBrTzO-WIiUVUCpS3egZD3PBnug8tz5LzA&s=10',
                'thumb'       => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSVTC-sGcFxRBrTzO-WIiUVUCpS3egZD3PBnug8tz5LzA&s=10',
                'author'      => 'Sarah Johnson',
                'date'        => 'May 16, 2025',
                'read_time'   => '5 min read',
                'tags'        => ['AI', 'Machine Learning', 'Future', 'Innovation', 'Tech'],
                'sections'    => [
                    [
                        'text' => 'Artificial Intelligence (AI) is no longer a concept of the future — it is here and now. From healthcare to finance, education to entertainment, AI is revolutionising the way we live and work.',
                    ],
                    [
                        'heading' => '1. What is Artificial Intelligence?',
                        'text'    => 'AI refers to the simulation of human intelligence in machines that are programmed to think and learn like humans. These systems can analyse data, recognise patterns and make decisions with minimal human involvement.',
                    ],
                    [
                        'heading' => '2. Applications Across Industries',
                        'text'    => 'AI is being used in healthcare for diagnosis, in finance for fraud detection, and in transportation for self driving vehicles. Its reach continues to expand into nearly every sector.',
                    ],
                    [
                        'heading' => '3. What Lies Ahead',
                        'text'    => 'As AI continues to evolve, it will bring both opportunities and challenges. Understanding its capabilities and limitations will be key to using it responsibly.',
                    ],
                ],
            ],

            '5g-technology' => [
                'title'       => '5G Technology: What It Means for You',
                'category'    => 'Technology',
                'badge_color' => 'bg-danger',
                'image'       => 'https://loremflickr.com/900/400/5g,network',
                'thumb'       => 'https://loremflickr.com/300/180/5g,network',
                'author'      => 'Sarah Johnson',
                'date'        => 'May 15, 2025',
                'read_time'   => '5 min read',
                'tags'        => ['5G', 'Technology', 'Networks', 'Innovation'],
                'sections'    => [
                    [
                        'text' => '5G is the next generation of mobile internet connectivity, promising dramatically faster data speeds, lower latency and more reliable connections.',
                    ],
                    [
                        'heading' => 'Faster Speeds, Real Impact',
                        'text'    => 'With 5G, downloads that once took minutes now take seconds. This opens the door for richer streaming, smoother video calls and instant cloud access.',
                    ],
                    [
                        'heading' => 'Powering New Technology',
                        'text'    => 'Beyond phones, 5G is a key enabler for smart cities, autonomous vehicles and the Internet of Things, connecting more devices than ever before.',
                    ],
                ],
            ],

            'best-laptops-2025' => [
                'title'       => 'Best Laptops for Developers in 2025',
                'category'    => 'Technology',
                'badge_color' => 'bg-danger',
                'image'       => 'https://loremflickr.com/900/400/laptop,coding',
                'thumb'       => 'https://loremflickr.com/300/180/laptop,coding',
                'author'      => 'Sarah Johnson',
                'date'        => 'May 12, 2025',
                'read_time'   => '6 min read',
                'tags'        => ['Laptops', 'Developers', 'Tech', 'Reviews'],
                'sections'    => [
                    [
                        'text' => 'Choosing the right laptop can make a huge difference in a developer\'s daily workflow. Here\'s what to look for in 2025.',
                    ],
                    [
                        'heading' => 'Performance Matters',
                        'text'    => 'Look for strong multi-core CPUs, at least 16GB RAM and fast SSD storage to handle compiling, virtual machines and multiple IDEs at once.',
                    ],
                    [
                        'heading' => 'Battery and Portability',
                        'text'    => "A great developer laptop balances raw power with all-day battery life, so you're not tied to an outlet during long coding sessions.",
                    ],
                ],
            ],

            'maldives-guide' => [
                'title'       => 'Exploring the Maldives: A Complete Guide',
                'category'    => 'Travel',
                'badge_color' => 'bg-success',
                'image'       => 'https://loremflickr.com/900/400/maldives,beach',
                'thumb'       => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTXLHedoXnRsGLmryCFmkRr67p6DKDhIupswrSYmaBIvg&s=10',
                'author'      => 'Sarah Johnson',
                'date'        => 'May 16, 2025',
                'read_time'   => '6 min read',
                'tags'        => ['Travel', 'Maldives', 'Beaches', 'Guide'],
                'sections'    => [
                    [
                        'text' => 'The Maldives is a dream destination for travellers seeking crystal-clear waters, white sand beaches and unforgettable overwater villas.',
                    ],
                    [
                        'heading' => 'Best Time to Visit',
                        'text'    => 'The dry season, from November to April, offers the sunniest weather and the calmest seas for diving and snorkelling.',
                    ],
                    [
                        'heading' => 'Top Things to Do',
                        'text'    => 'From snorkelling with manta rays to relaxing spa days on private islands, the Maldives offers something for every type of traveller.',
                    ],
                ],
            ],

            'healthy-recipes' => [
                'title'       => '10 Healthy Recipes for a Better You',
                'category'    => 'Food',
                'badge_color' => 'bg-warning text-dark',
                'image'       => 'https://loremflickr.com/900/400/healthy,food',
                'thumb'       => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQsU55EnkZObOdYQXWuOqSaKVJqhsqSYja4qEfgK3x-cg&s=10',
                'author'      => 'Sarah Johnson',
                'date'        => 'May 14, 2025',
                'read_time'   => '6 min read',
                'tags'        => ['Food', 'Health', 'Recipes', 'Wellness'],
                'sections'    => [
                    [
                        'text' => 'Eating well doesn\'t have to be complicated. These simple, healthy recipes are packed with flavour and nutrition.',
                    ],
                    [
                        'heading' => 'Start With Whole Foods',
                        'text'    => 'Building meals around vegetables, lean proteins and whole grains is the foundation of a healthier diet.',
                    ],
                    [
                        'heading' => 'Easy Meal Prep Tips',
                        'text'    => 'Preparing ingredients in advance makes it far easier to stick to healthy eating habits throughout a busy week.',
                    ],
                ],
            ],

            'startup-ideas-2025' => [
                'title'       => 'Startup Ideas to Watch in 2025',
                'category'    => 'Business',
                'badge_color' => 'bg-info text-dark',
                'image'       => 'https://loremflickr.com/900/400/startup,office',
                'thumb'       => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTNlHdWYiA-2nXpEfxzzu-K8UGzPhMdtek1acZEYbyw0Q&s=10',
                'author'      => 'Sarah Johnson',
                'date'        => 'May 13, 2025',
                'read_time'   => '7 min read',
                'tags'        => ['Startups', 'Business', 'Entrepreneurship'],
                'sections'    => [
                    [
                        'text' => 'The startup world keeps evolving, with new ideas emerging around sustainability, AI, and remote-first business models.',
                    ],
                    [
                        'heading' => 'Sustainability-Driven Startups',
                        'text'    => 'Businesses focused on eco-friendly products and services are attracting growing consumer interest and investment.',
                    ],
                    [
                        'heading' => 'AI-Powered Tools',
                        'text'    => 'Startups building AI tools for small businesses are helping automate tasks that once required large teams.',
                    ],
                ],
            ],

            'cybersecurity-basics' => [
                'title'       => 'Cybersecurity Basics Everyone Should Know',
                'category'    => 'Technology',
                'badge_color' => 'bg-danger',
                'image'       => 'https://loremflickr.com/900/400/cybersecurity',
                'thumb'       => 'https://loremflickr.com/70/60/cybersecurity',
                'author'      => 'Sarah Johnson',
                'date'        => 'May 10, 2025',
                'read_time'   => '4 min read',
                'tags'        => ['Cybersecurity', 'Safety', 'Technology'],
                'sections'    => [
                    [
                        'text' => 'With more of our lives online, understanding basic cybersecurity practices has never been more important.',
                    ],
                    [
                        'heading' => 'Use Strong, Unique Passwords',
                        'text'    => 'A password manager can help you create and store strong, unique passwords for every account you use.',
                    ],
                    [
                        'heading' => 'Enable Two-Factor Authentication',
                        'text'    => 'Adding a second layer of verification makes it significantly harder for attackers to access your accounts.',
                    ],
                ],
            ],

            'top-tech-gadgets-2025' => [
                'title'       => 'Top 10 Tech Gadgets in 2025',
                'category'    => 'Technology',
                'badge_color' => 'bg-danger',
                'image'       => 'https://loremflickr.com/900/400/gadgets,technology',
                'thumb'       => 'https://loremflickr.com/120/90/gadgets,technology',
                'author'      => 'Sarah Johnson',
                'date'        => 'May 9, 2025',
                'read_time'   => '5 min read',
                'tags'        => ['Gadgets', 'Technology', 'Reviews'],
                'sections'    => [
                    [
                        'text' => 'From wearables to smart home devices, 2025 has brought some genuinely useful gadgets worth knowing about.',
                    ],
                    [
                        'heading' => 'Wearables Get Smarter',
                        'text'    => 'New health-tracking wearables now offer more accurate insights than ever, from sleep to heart health.',
                    ],
                ],
            ],

            'tech-changing-education' => [
                'title'       => 'How Technology is Changing Education',
                'category'    => 'Education',
                'badge_color' => 'bg-primary',
                'image'       => 'https://loremflickr.com/900/400/education,classroom',
                'thumb'       => 'https://loremflickr.com/120/90/education,classroom',
                'author'      => 'Sarah Johnson',
                'date'        => 'May 7, 2025',
                'read_time'   => '4 min read',
                'tags'        => ['Education', 'Technology', 'Learning'],
                'sections'    => [
                    [
                        'text' => 'Classrooms today look very different from a decade ago, largely thanks to advances in educational technology.',
                    ],
                    [
                        'heading' => 'Personalised Learning',
                        'text'    => 'Adaptive learning platforms now tailor lessons to each student\'s pace and learning style.',
                    ],
                ],
            ],

            'ai-healthcare' => [
                'title'       => 'AI in Healthcare: Benefits and Challenges',
                'category'    => 'Technology',
                'badge_color' => 'bg-danger',
                'image'       => 'https://loremflickr.com/900/400/healthcare,medical',
                'thumb'       => 'https://loremflickr.com/160/140/healthcare,medical',
                'author'      => 'Sarah Johnson',
                'date'        => 'May 15, 2025',
                'read_time'   => '6 min read',
                'tags'        => ['AI', 'Healthcare', 'Technology'],
                'sections'    => [
                    [
                        'text' => 'AI is transforming healthcare, from faster diagnostics to personalised treatment plans — but it also raises important questions.',
                    ],
                    [
                        'heading' => 'Faster, More Accurate Diagnosis',
                        'text'    => 'Machine learning models can analyse medical images faster than ever, helping doctors catch issues earlier.',
                    ],
                    [
                        'heading' => 'Ethical Considerations',
                        'text'    => 'As AI takes on a bigger role in patient care, questions around privacy, bias and accountability remain critical.',
                    ],
                ],
            ],

            'machine-learning-basics' => [
                'title'       => 'Machine Learning Basics for Beginners',
                'category'    => 'Technology',
                'badge_color' => 'bg-danger',
                'image'       => 'https://loremflickr.com/900/400/machinelearning,computer',
                'thumb'       => 'https://loremflickr.com/160/140/machinelearning,computer',
                'author'      => 'Sarah Johnson',
                'date'        => 'May 11, 2025',
                'read_time'   => '5 min read',
                'tags'        => ['Machine Learning', 'AI', 'Beginners'],
                'sections'    => [
                    [
                        'text' => 'Machine learning can sound intimidating, but the core ideas are more approachable than you might think.',
                    ],
                    [
                        'heading' => 'What is Machine Learning?',
                        'text'    => 'It\'s a way of teaching computers to find patterns in data and make predictions, without being explicitly programmed for every scenario.',
                    ],
                ],
            ],

            'ai-changing-world' => [
                'title'       => 'How AI is Changing the World',
                'category'    => 'Technology',
                'badge_color' => 'bg-danger',
                'image'       => 'https://loremflickr.com/900/400/artificialintelligence,robot',
                'thumb'       => 'https://loremflickr.com/160/140/artificialintelligence,robot',
                'author'      => 'Sarah Johnson',
                'date'        => 'May 9, 2025',
                'read_time'   => '6 min read',
                'tags'        => ['AI', 'Technology', 'Innovation'],
                'sections'    => [
                    [
                        'text' => 'Artificial Intelligence is reshaping industries across the globe, changing how we work, communicate and solve problems.',
                    ],
                    [
                        'heading' => 'A New Era of Automation',
                        'text'    => 'Repetitive tasks across many industries are increasingly handled by AI, freeing people to focus on more creative work.',
                    ],
                ],
            ],

        ];
    }

    /**
     * Show a single blog post by its slug.
     */
    public function show($slug)
    {
        $posts = $this->posts();

        // Agar slug exist nahi karta to 404 page dikhao
        if (!isset($posts[$slug])) {
            abort(404);
        }

        $post = $posts[$slug];
        $post['slug'] = $slug;

        // Related posts: same category ke 3 posts (current ko chhor kar), warna kisi bhi 3
        $sameCategory = collect($posts)
            ->except($slug)
            ->filter(fn ($p) => $p['category'] === $post['category'])
            ->take(3);

        if ($sameCategory->count() < 3) {
            $others = collect($posts)->except($slug)->except($sameCategory->keys()->all());
            $sameCategory = $sameCategory->merge($others->take(3 - $sameCategory->count()));
        }

        $related = $sameCategory->map(function ($p, $key) {
            $p['slug'] = $key;
            return $p;
        })->values();

        return view('frontend.blog-single', compact('post', 'related'));
    }

    /**
     * Show all posts belonging to a category (matched via slug, e.g. "technology").
     */
    public function category($slug)
    {
        $posts = $this->posts();

        // Slug ko wapas readable category name mein convert karo (technology -> Technology)
        $categoryName = ucwords(str_replace('-', ' ', $slug));

        $filtered = collect($posts)
            ->filter(fn ($p) => strtolower($p['category']) === strtolower($categoryName))
            ->map(function ($p, $key) {
                $p['slug'] = $key;
                return $p;
            })
            ->values();

        return view('frontend.category', [
            'categoryName' => $categoryName,
            'posts'        => $filtered,
        ]);
    }
}