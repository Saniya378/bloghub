<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence();

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(6),
            'excerpt' => fake()->paragraph(2),
            'content' => $this->generateContent(),
            'category_slug' => fake()->randomElement(['technology', 'education', 'travel', 'food', 'business', 'sports', 'health', 'lifestyle', 'finance', 'science', 'art', 'culture']),
            'tags' => fake()->words(3),
            'image' => 'https://picsum.photos/seed/' . Str::slug($title) . '/1200/675',
            'status' => 'published',
            'views' => fake()->numberBetween(100, 10000),
            'likes' => fake()->numberBetween(0, 500),
            'comment_count' => 0,
            'reading_time' => fake()->numberBetween(3, 15) . ' min read',
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    private function generateContent(): string
    {
        $paragraphs = fake()->paragraphs(4, true);
        $heading = fake()->sentence(4);
        $quote = fake()->sentence();
        $code = fake()->randomElement([
            "function hello() {\n    return 'world';\n}",
            "const data = await fetch('/api/posts');",
            "SELECT * FROM users WHERE active = 1;",
            "docker-compose up -d --build",
        ]);

        return "<h2>{$heading}</h2>"
            . "<p>{$paragraphs}</p>"
            . "<blockquote><p>\"{$quote}\"</p></blockquote>"
            . "<p>" . fake()->paragraph(2) . "</p>"
            . "<pre><code>{$code}</code></pre>"
            . "<p>" . fake()->paragraph(3) . "</p>";
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }
}
