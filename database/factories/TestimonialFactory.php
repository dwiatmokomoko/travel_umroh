<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TestimonialFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->name(), 'city' => 'Bantul', 'body' => fake()->paragraph(), 'rating' => 5, 'is_published' => false, 'consent_confirmed' => false];
    }
}
