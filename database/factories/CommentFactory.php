<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        $comments = [
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
            'Your writing has a rare quality — it respects the reader\'s intelligence while remaining accessible. More of this, please.',
            'The comparison table in the middle was exactly what I needed to make a decision at work today. Perfect timing.',
            'I appreciate that you did not shy away from the trade-offs. Too many articles present only one side of the story.',
            'This deserves a sequel. I want to hear more about the implementation challenges you mentioned briefly.',
            'The opening paragraph hooked me immediately. By the end, I had already bookmarked three of your references.',
            'As someone transitioning from frontend to full-stack, this article bridged a gap I have been struggling with for weeks.',
            'Your critique of the popular framework was measured and fair. You made your case without being dismissive.',
        ];

        return [
            'user_id' => User::factory(),
            'article_id' => Article::factory(),
            'content' => fake()->randomElement($comments),
            'status' => fake()->randomElement(['approved', 'approved', 'approved', 'pending']),
            'likes' => fake()->numberBetween(0, 50),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }
}
