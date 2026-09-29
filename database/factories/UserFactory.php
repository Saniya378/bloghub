<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'reader',
            'is_active' => true,
            'avatar' => 'https://i.pravatar.cc/300?img=' . fake()->numberBetween(1, 70),
            'cover_image' => 'https://picsum.photos/seed/' . Str::slug(fake()->word()) . '/1200/400',
            'bio' => fake()->paragraph(),
            'tagline' => fake()->sentence(),
            'specialty' => fake()->word(),
            'social' => [
                'twitter' => 'https://twitter.com/' . fake()->userName(),
                'github' => 'https://github.com/' . fake()->userName(),
                'linkedin' => 'https://linkedin.com/in/' . fake()->userName(),
            ],
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    public function author(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'author',
        ]);
    }

    public function reader(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'reader',
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
