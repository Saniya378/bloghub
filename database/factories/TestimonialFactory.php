<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'photo' => 'https://i.pravatar.cc/100?img=' . fake()->numberBetween(1, 70),
            'quote' => fake()->paragraph(2),
            'rating' => fake()->numberBetween(3, 5),
        ];
    }
}
