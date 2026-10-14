<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(), 'name' => 'Umrah 9 Hari',
            'departure_date' => today()->addMonth(), 'days' => 9, 'includes_thaif' => false,
            'price' => 32000000, 'airline' => 'Lion Air', 'madinah_hotel' => 'Arguan Al Salam',
            'madinah_stars' => 3, 'makkah_hotel' => 'Grand Al Masya', 'makkah_stars' => 4,
            'is_published' => true,
        ];
    }
}
