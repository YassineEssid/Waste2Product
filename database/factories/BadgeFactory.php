<?php

namespace Database\Factories;

use App\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

class BadgeFactory extends Factory
{
    protected $model = Badge::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->unique()->slug,
            'description' => $this->faker->sentence,
            'icon' => $this->faker->randomElement(['🏆', '⭐', '🎖️', '🥇', '🎯']),
            'requirement_type' => $this->faker->randomElement([
                'events_attended',
                'events_created',
                'comments_posted',
                'waste_items_posted',
            ]),
            'required_count' => $this->faker->numberBetween(1, 10),
            'required_points' => $this->faker->numberBetween(0, 100),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}
