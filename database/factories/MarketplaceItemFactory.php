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
            'title' => fake()->sentence(3),
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 10, 500),
            'status' => 'available',
            'is_available' => true,
        ];
    }
}
