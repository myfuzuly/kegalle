<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StoreFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();
        return [
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->randomNumber(4),
            'description' => fake()->sentence(),
            'phone' => '+947' . fake()->numerify('########'),
            'email' => fake()->safeEmail(),
            'city' => 'Kegalle',
            'status' => 'approved',
        ];
    }
}
