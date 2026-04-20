<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(3, false);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 9999),
            'description' => fake()->paragraph(),
            'short_description' => fake()->sentence(),
            'level' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'category' => fake()->randomElement(['Matematika', 'IPA', 'IPS', 'Bahasa', 'Agama']),
            'duration_minutes' => fake()->numberBetween(30, 300),
            'total_lessons' => fake()->numberBetween(5, 30),
            'price' => 0,
            'is_free' => true,
            'is_published' => true,
            'instructor_id' => User::factory()->state(['role' => 'instructor']),
        ];
    }
}
