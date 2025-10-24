<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use App\Models\WasteItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WasteItem>
 */
class WasteItemFactory extends Factory
{
    protected $model = WasteItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'category_id' => Category::factory(),
            'quantity' => fake()->numberBetween(1, 100),
            'condition' => fake()->randomElement(['new', 'good', 'fair', 'poor', 'broken']),
            'location_address' => fake()->address(),
            'location_lat' => fake()->latitude(),
            'location_lng' => fake()->longitude(),
            'images' => [],
            'is_available' => true,
            'status' => 'available',
        ];
    }
}
