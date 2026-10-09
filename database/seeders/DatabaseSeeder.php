<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedArticles();
        $this->seedComments();
        $this->seedLikesBookmarksFollows();
        $this->seedTestimonials();
        $this->call(ActivityLogSeeder::class);
    }

    private function seedUsers(): void
    {
        if (User::where('email', 'admin@bloghub.com')->exists()) {
            return;
        }

        $usedUsernames = User::pluck('username')->filter()->all();

        $uniqueUsername = function (string $name) use (&$usedUsernames): string {
            $base = Str::slug($name) ?: 'user';
            $username = $base;
            $suffix = 2;
            while (in_array($username, $usedUsernames, true)) {
                $username = $base . '-' . $suffix++;
            }
            $usedUsernames[] = $username;

            return $username;
        };

        User::create([
            'name' => 'Admin User',
            'username' => $uniqueUsername('Admin User'),
            'email' => 'admin@bloghub.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'avatar' => 'https://i.pravatar.cc/300?img=1',
            'cover_image' => 'https://picsum.photos/seed/admin-cover/1200/400',
            'bio' => 'Platform administrator and chief editor at BlogHub.',
            'tagline' => 'Chief Editor',
            'specialty' => 'Editorial',
            'social' => ['twitter' => 'https://twitter.com/bloghub', 'github' => 'https://github.com/bloghub', 'linkedin' => 'https://linkedin.com/company/bloghub'],
        ]);

        $authors = [
            ['name' => 'Elena Rostova', 'email' => 'elena@bloghub.com', 'specialty' => 'Distributed Systems', 'tagline' => 'Principal Engineer & Tech Essayist', 'avatar' => 47, 'cover' => 'elena-cover'],
            ['name' => 'Marcus Chen', 'email' => 'marcus@bloghub.com', 'specialty' => 'UI/UX Design', 'tagline' => 'Design Director & Typographer', 'avatar' => 12, 'cover' => 'marcus-cover'],
            ['name' => 'Dr. Sarah Jenkins', 'email' => 'sarah@bloghub.com', 'specialty' => 'Cognitive Science', 'tagline' => 'Neuroscientist & Researcher', 'avatar' => 32, 'cover' => 'sarah-cover'],
            ['name' => 'Mateo Morales', 'email' => 'mateo@bloghub.com', 'specialty' => 'Travel & Documentary', 'tagline' => 'Photojournalist & Essayist', 'avatar' => 60, 'cover' => 'mateo-cover'],
            ['name' => 'Camille Laurent', 'email' => 'camille@bloghub.com', 'specialty' => 'Culinary Traditions', 'tagline' => 'Food Systems Researcher', 'avatar' => 49, 'cover' => 'camille-cover'],
            ['name' => 'Julian Vance', 'email' => 'julian@bloghub.com', 'specialty' => 'Business Strategy', 'tagline' => 'Venture Strategist', 'avatar' => 53, 'cover' => 'julian-cover'],
            ['name' => 'Amina Nour', 'email' => 'amina@bloghub.com', 'specialty' => 'Sports Physiology', 'tagline' => 'Endurance Athlete & Physiologist', 'avatar' => 26, 'cover' => 'amina-cover'],
            ['name' => 'Dr. Aris Thorne', 'email' => 'aris@bloghub.com', 'specialty' => 'Preventative Medicine', 'tagline' => 'Physician & Longevity Author', 'avatar' => 15, 'cover' => 'aris-cover'],
            ['name' => 'Priya Sharma', 'email' => 'priya@bloghub.com', 'specialty' => 'Macroeconomics', 'tagline' => 'Quantitative Analyst & Columnist', 'avatar' => 44, 'cover' => 'priya-cover'],
            ['name' => 'David Kim', 'email' => 'david@bloghub.com', 'specialty' => 'Visual Arts & Architecture', 'tagline' => 'Curator & Art Critic', 'avatar' => 68, 'cover' => 'david-cover'],
        ];

        foreach ($authors as $author) {
            User::create([
                'name' => $author['name'],
                'username' => $uniqueUsername($author['name']),
                'email' => $author['email'],
                'password' => Hash::make('password'),
                'role' => 'author',
                'is_active' => true,
                'email_verified_at' => now(),
                'avatar' => "https://i.pravatar.cc/300?img={$author['avatar']}",
                'cover_image' => "https://picsum.photos/seed/{$author['cover']}/1200/400",
                'bio' => fake()->paragraph(3),
                'tagline' => $author['tagline'],
                'specialty' => $author['specialty'],
                'social' => ['twitter' => 'https://twitter.com/' . Str::slug($author['name']), 'github' => 'https://github.com/' . Str::slug($author['name']), 'linkedin' => 'https://linkedin.com/in/' . Str::slug($author['name'])],
            ]);
        }

        for ($i = 1; $i <= 15; $i++) {
            $name = fake()->name();
            User::create([
                'name' => $name,
                'username' => $uniqueUsername($name),
                'email' => "reader{$i}@bloghub.com",
                'password' => Hash::make('password'),
                'role' => 'reader',
                'is_active' => true,
                'email_verified_at' => now(),
                'avatar' => 'https://i.pravatar.cc/300?img=' . fake()->numberBetween(1, 70),
                'cover_image' => 'https://picsum.photos/seed/reader-' . $i . '/1200/400',
                'bio' => fake()->paragraph(2),
                'tagline' => fake()->sentence(),
                'specialty' => fake()->word(),
                'social' => ['twitter' => 'https://twitter.com/' . fake()->userName()],
            ]);
        }
    }

    private function seedArticles(): void
    {
        $articles = [
            ['title' => 'The Art of Building Resilient Distributed Systems', 'category' => 'technology', 'tags' => ['distributed systems', 'architecture', 'reliability'], 'excerpt' => 'Exploring the fundamental patterns that keep distributed systems running through failures, network partitions, and unexpected load spikes.'],
            ['title' => 'Why Rust is the Future of Systems Programming', 'category' => 'technology', 'tags' => ['rust', 'systems', 'memory safety'], 'excerpt' => 'How Rust ownership model eliminates entire classes of bugs while maintaining C++-level performance.'],
            ['title' => 'The Hidden Costs of Microservices Architecture', 'category' => 'technology', 'tags' => ['microservices', 'architecture', 'devops'], 'excerpt' => 'A critical examination of when microservices help and when they create more problems than they solve.'],
            ['title' => 'How Spaced Repetition Transforms Long-Term Learning', 'category' => 'education', 'tags' => ['learning', 'memory', 'cognitive science'], 'excerpt' => 'The science behind why reviewing material at increasing intervals produces dramatically better retention than cramming.'],
            ['title' => 'The Case Against Homework: What Research Actually Shows', 'category' => 'education', 'tags' => ['homework', 'research', 'policy'], 'excerpt' => 'Examining the evidence on whether homework improves academic outcomes, and what alternatives might work better.'],
            ['title' => 'Slow Travel: Why Taking Longer Leads to Better Experiences', 'category' => 'travel', 'tags' => ['slow travel', 'culture', 'mindfulness'], 'excerpt' => 'How spending more time in fewer places creates deeper cultural understanding and more meaningful memories.'],
            ['title' => 'Digital Nomad Life: The Reality Behind the Instagram Posts', 'category' => 'travel', 'tags' => ['digital nomad', 'remote work', 'lifestyle'], 'excerpt' => 'An honest account of the challenges and rewards of working remotely while traveling the world.'],
            ['title' => 'The Science of Fermentation: From Kimchi to Kombucha', 'category' => 'food', 'tags' => ['fermentation', 'science', 'cooking'], 'excerpt' => 'Understanding the microbial processes that transform ordinary ingredients into complex, probiotic-rich foods.'],
            ['title' => 'Why Farm-to-Table is More Than a Marketing Buzzword', 'category' => 'food', 'tags' => ['sustainability', 'agriculture', 'local food'], 'excerpt' => 'Examining the real environmental and economic impacts of local food systems versus industrial agriculture.'],
            ['title' => 'The Psychology of Pricing: Why 9.99 Feels Different from 10', 'category' => 'business', 'tags' => ['pricing', 'psychology', 'marketing'], 'excerpt' => 'How cognitive biases influence purchasing decisions and how ethical businesses can use this knowledge responsibly.'],
            ['title' => 'Building a Company Culture That Survives Hypergrowth', 'category' => 'business', 'tags' => ['culture', 'startups', 'management'], 'excerpt' => 'How to maintain the values and practices that made your startup successful as you scale from 10 to 1000 employees.'],
            ['title' => 'The Science of Endurance: How Elite Marathoners Train', 'category' => 'sports', 'tags' => ['endurance', 'training', 'physiology'], 'excerpt' => 'A deep dive into the physiological adaptations and training methodologies that enable sub-2-hour marathon performances.'],
            ['title' => 'Recovery: The Most Undertrained Aspect of Athletic Performance', 'category' => 'sports', 'tags' => ['recovery', 'sleep', 'performance'], 'excerpt' => 'Why rest days, sleep, and active recovery are where fitness is actually built, not just maintained.'],
            ['title' => 'The Gut-Brain Connection: How Your Microbiome Affects Your Mood', 'category' => 'health', 'tags' => ['microbiome', 'mental health', 'nutrition'], 'excerpt' => 'Emerging research reveals the profound influence of gut bacteria on mental health, cognition, and emotional regulation.'],
            ['title' => 'Intermittent Fasting: What the Evidence Actually Supports', 'category' => 'health', 'tags' => ['fasting', 'metabolism', 'longevity'], 'excerpt' => 'Separating the science from the hype around time-restricted eating and its effects on weight, longevity, and metabolic health.'],
            ['title' => 'Digital Minimalism: Reclaiming Your Attention in the Attention Economy', 'category' => 'lifestyle', 'tags' => ['minimalism', 'technology', 'focus'], 'excerpt' => 'How intentional technology use can improve focus, creativity, and overall life satisfaction.'],
            ['title' => 'The Art of Doing Nothing: Why Idleness Is Essential for Creativity', 'category' => 'lifestyle', 'tags' => ['creativity', 'rest', 'productivity'], 'excerpt' => 'How boredom and unstructured time are prerequisites for creative insight and problem-solving.'],
            ['title' => 'The Psychology of Money: Why Behavior Beats Intelligence', 'category' => 'finance', 'tags' => ['investing', 'psychology', 'behavioral economics'], 'excerpt' => 'How emotional discipline and behavioral biases matter more than IQ in building long-term wealth.'],
            ['title' => 'Index Funds vs. Active Management: The Evidence Decades in the Making', 'category' => 'finance', 'tags' => ['index funds', 'investing', 'evidence'], 'excerpt' => 'Why the vast majority of actively managed funds fail to beat simple index funds over any meaningful time horizon.'],
            ['title' => 'The James Webb Space Telescope: Rewriting Our Understanding of the Early Universe', 'category' => 'science', 'tags' => ['astronomy', 'space', 'cosmology'], 'excerpt' => 'How JWST observations are challenging established models of galaxy formation and cosmic evolution.'],
            ['title' => 'Quantum Computing: From Theory to Practical Applications', 'category' => 'science', 'tags' => ['quantum computing', 'technology', 'physics'], 'excerpt' => 'How quantum computers work, what they can and cannot do, and when they will become practically relevant.'],
            ['title' => 'The Renaissance of Vinyl: Why Analog Media is Thriving in the Digital Age', 'category' => 'art', 'tags' => ['vinyl', 'music', 'analog'], 'excerpt' => 'Exploring the cultural and sensory reasons behind the unexpected resurgence of vinyl records.'],
            ['title' => 'Street Art as Social Commentary: From Vandalism to Gallery', 'category' => 'art', 'tags' => ['street art', 'culture', 'social commentary'], 'excerpt' => 'How street art evolved from underground vandalism to a respected form of public discourse and artistic expression.'],
            ['title' => 'The Death of the Office: What We Lose When We Work from Home', 'category' => 'culture', 'tags' => ['remote work', 'culture', 'society'], 'excerpt' => 'An honest assessment of the social and cultural costs of remote work that often get ignored in the flexibility debate.'],
            ['title' => 'The Rise of Solo Living: How One-Person Households Are Reshaping Cities', 'category' => 'culture', 'tags' => ['urbanism', 'society', 'demographics'], 'excerpt' => 'The demographic shift toward single-person households and its profound implications for housing, urban planning, and social policy.'],
        ];

        $authorIds = User::where('role', 'author')->pluck('id')->toArray();

        foreach ($articles as $index => $article) {
            $title = $article['title'];
            $slug = Str::slug($title) . '-' . Str::random(4);
            $content = '<h2>Introduction</h2><p>' . fake()->paragraph(3) . '</p><blockquote><p>"' . fake()->sentence() . '"</p></blockquote><h3>Key Insights</h3><p>' . fake()->paragraph(4) . '</p><pre><code>// Example code block
function example() {
    return "Hello, World!";
}</code></pre><p>' . fake()->paragraph(3) . '</p><h3>Conclusion</h3><p>' . fake()->paragraph(2) . '</p>';

            Article::create([
                'user_id' => $authorIds[$index % count($authorIds)],
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $article['excerpt'],
                'content' => $content,
                'category_slug' => $article['category'],
                'tags' => $article['tags'],
                'image' => 'https://picsum.photos/seed/' . Str::slug($title) . '/1200/675',
                'status' => 'published',
                'views' => rand(500, 8000),
                'likes' => rand(10, 400),
                'comment_count' => 0,
                'reading_time' => rand(4, 14) . ' min read',
                'published_at' => now()->subDays(rand(1, 365)),
                'is_featured' => $index < 3,
            ]);
        }
    }

    private function seedComments(): void
    {
        $articles = Article::all();
        $users = User::whereIn('role', ['reader', 'author'])->get();
        $commentTexts = [
            'This is exactly what I needed. The section on system design really clarified my understanding of distributed architectures.',
            'I appreciate how you balanced theory with practical examples. The code snippets made it easy to follow along.',
            'Could you expand on the performance implications? I would love to see benchmarks comparing both approaches.',
            'Well-written and thoroughly researched. The references to primary sources added a lot of credibility.',
            'I disagree with the conclusion, but I appreciate the structured argument. The evidence presented is compelling even if I reach a different conclusion.',
            'This reminded me of the early days of computing when engineers had to be more deliberate about every design decision. We could use more of that mindset today.',
            'The historical context at the beginning really helped me understand why this approach emerged. Excellent work.',
            'I have been following your writing for months and this is your best piece yet. The narrative flow is perfect.',
            'Have you considered the ethical implications of this technology? The article touches on it but I think it deserves deeper exploration.',
            'This is a masterclass in technical writing. Complex concepts explained with clarity and precision.',
            'I shared this with my entire team. The diagrams and examples are incredibly helpful for onboarding new engineers.',
            'The section on failure modes was particularly insightful. It is rare to see honest discussion of what goes wrong.',
            'I have been working with this stack for five years and still learned something new. Thank you for the deep dive.',
            'Your writing has a rare quality — it respects the reader intelligence while remaining accessible. More of this, please.',
            'The comparison table in the middle was exactly what I needed to make a decision at work today. Perfect timing.',
            'I appreciate that you did not shy away from the trade-offs. Too many articles present only one side of the story.',
            'This deserves a sequel. I want to hear more about the implementation challenges you mentioned briefly.',
            'The opening paragraph hooked me immediately. By the end, I had already bookmarked three of your references.',
            'As someone transitioning from frontend to full-stack, this article bridged a gap I have been struggling with for weeks.',
            'Your critique of the popular framework was measured and fair. You made your case without being dismissive.',
        ];

        $commentCount = 0;
        foreach ($articles->take(12) as $article) {
            $numComments = rand(3, 7);
            for ($i = 0; $i < $numComments && $commentCount < 60; $i++) {
                Comment::create([
                    'user_id' => $users->random()->id,
                    'article_id' => $article->id,
                    'content' => $commentTexts[array_rand($commentTexts)],
                    'status' => rand(1, 10) <= 8 ? 'approved' : 'pending',
                    'likes' => rand(0, 30),
                ]);
                $commentCount++;
            }
        }

        foreach ($articles as $article) {
            $article->update(['comment_count' => $article->comments()->count()]);
        }
    }

    private function seedLikesBookmarksFollows(): void
    {
        $readers = User::where('role', 'reader')->get();
        $authors = User::where('role', 'author')->get();
        $articles = Article::all();

        foreach ($readers as $reader) {
            $likedArticles = $articles->random(rand(3, 10));
            foreach ($likedArticles as $article) {
                DB::table('article_likes')->updateOrInsert(
                    ['user_id' => $reader->id, 'article_id' => $article->id],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        foreach ($readers as $reader) {
            $bookmarkedArticles = $articles->random(rand(2, 8));
            foreach ($bookmarkedArticles as $article) {
                DB::table('bookmarks')->updateOrInsert(
                    ['user_id' => $reader->id, 'article_id' => $article->id],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        foreach ($readers as $reader) {
            $followedAuthors = $authors->random(rand(1, 4));
            foreach ($followedAuthors as $author) {
                DB::table('follows')->updateOrInsert(
                    ['user_id' => $reader->id, 'author_id' => $author->id],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }

    private function seedTestimonials(): void
    {
        $testimonials = [
            ['name' => 'James Mitchell', 'photo' => 11, 'quote' => 'BlogHub has become my daily reading ritual. The depth of analysis here is unmatched by any other platform.', 'rating' => 5],
            ['name' => 'Lisa Park', 'photo' => 25, 'quote' => 'I appreciate the editorial standards. Every article feels carefully crafted and thoroughly researched.', 'rating' => 5],
            ['name' => 'Robert Taylor', 'photo' => 33, 'quote' => 'The writing quality is exceptional. I have discovered so many talented authors through this platform.', 'rating' => 5],
            ['name' => 'Amanda White', 'photo' => 41, 'quote' => 'Finally a platform that respects its readers. No clickbait, no sensationalism — just thoughtful content.', 'rating' => 5],
            ['name' => 'Daniel Brown', 'photo' => 52, 'quote' => 'The diversity of topics is impressive. From distributed systems to culinary traditions, everything is covered with equal depth.', 'rating' => 5],
            ['name' => 'Sophie Turner', 'photo' => 59, 'quote' => 'I recommend BlogHub to all my colleagues. The tech articles have directly helped my work as an engineer.', 'rating' => 4],
            ['name' => 'Michael Chang', 'photo' => 61, 'quote' => 'The reading experience is clean and distraction-free. I can focus on the content without annoying popups.', 'rating' => 4],
            ['name' => 'Rachel Green', 'photo' => 16, 'quote' => 'Great platform overall. Would love to see more interactive features, but the content quality keeps me coming back.', 'rating' => 4],
            ['name' => 'Thomas Anderson', 'photo' => 38, 'quote' => 'Good selection of articles, though I would appreciate more frequent updates in the science category.', 'rating' => 3],
            ['name' => 'Nina Patel', 'photo' => 20, 'quote' => 'Solid platform with quality content. The mobile experience could be slightly better, but the writing itself is excellent.', 'rating' => 3],
        ];

        foreach ($testimonials as $t) {
            Testimonial::create([
                'name' => $t['name'],
                'photo' => "https://i.pravatar.cc/100?img={$t['photo']}",
                'quote' => $t['quote'],
                'rating' => $t['rating'],
            ]);
        }
    }
}
