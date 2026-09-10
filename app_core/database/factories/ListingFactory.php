<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ListingFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->words(4, true);
        return [
            'user_id' => User::factory(),
            'store_id' => Store::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->randomNumber(4),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(100, 500000),
            'location' => 'Kegalle',
            'type' => 'product',
            'status' => 'pending',
            'is_featured' => false,
            'is_top' => false,
        ];
    }
}
