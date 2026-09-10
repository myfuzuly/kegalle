<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = fake()->sentence(4);
        return [
            'title'        => $title,
            'slug'         => Str::slug($title) . '-' . uniqid(),
            'excerpt'      => fake()->paragraph(),
            'body'         => '<p>' . fake()->paragraphs(3, true) . '</p>',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ];
    }
}
