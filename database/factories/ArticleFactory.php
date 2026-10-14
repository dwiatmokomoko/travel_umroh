<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return ['title' => fake()->sentence(), 'slug' => fake()->unique()->slug(), 'excerpt' => fake()->paragraph(), 'body' => '## Persiapan perjalanan'."\n\n".fake()->paragraph(), 'author' => 'Redaksi Safir', 'is_published' => false, 'published_at' => null];
    }

    public function published(): static
    {
        return $this->state(fn () => ['is_published' => true, 'published_at' => now()->subHour()]);
    }
}
