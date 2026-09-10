<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);
        return [
            'title'      => $title,
            'slug'       => Str::slug($title) . '-' . uniqid(),
            'description'=> fake()->paragraph(),
            'event_date' => fake()->dateTimeBetween('+1 week', '+3 months')->format('Y-m-d'),
            'starts_at'  => fake()->dateTimeBetween('+1 week', '+3 months'),
            'ends_at'    => fake()->dateTimeBetween('+3 months', '+6 months'),
            'location'   => fake()->city(),
            'venue'      => fake()->company(),
            'price'      => 0,
            'event_type' => 'general',
            'status'     => 'published',
            'is_free'    => true,
            'is_featured'=> false,
        ];
    }
}
