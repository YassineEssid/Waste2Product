<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Transformation;
use App\Models\MarketplaceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MarketplaceItem>
 */
class MarketplaceItemFactory extends Factory
{
    protected $model = MarketplaceItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'seller_id' => User::factory(),
            'transformation_id' => Transformation::factory(),
            'title' => fake()->sentence(3),
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 10, 500),
            'category' => fake()->randomElement(['electronics', 'furniture', 'clothing', 'art', 'tools', 'other']),
            'condition' => fake()->randomElement(['new', 'excellent', 'good', 'fair']),
            'quantity' => fake()->numberBetween(1, 10),
            'is_negotiable' => fake()->boolean(),
            'delivery_method' => fake()->randomElement(['pickup', 'delivery', 'both']),
            'delivery_notes' => fake()->optional()->sentence(),
            'status' => 'active',
            'is_featured' => false,
            'views_count' => fake()->numberBetween(0, 500),
            'promoted_until' => null,
            'is_available' => true,
        ];
    }
}
