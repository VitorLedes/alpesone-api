<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true),
            'author' => $this->faker->name(),
            'pages' => $this->faker->numberBetween(150, 700),
            'description' => $this->faker->paragraph(1),
            'published_at' => random_int(1, 3) == 1 ? $this->faker->date() : null,
        ];
    }
}
