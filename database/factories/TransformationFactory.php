<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WasteItem;
use App\Models\Transformation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transformation>
 */
class TransformationFactory extends Factory
{
    protected $model = Transformation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'artisan_id' => User::factory(),
            'waste_item_id' => WasteItem::factory(),
            'product_title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'impact' => fake()->paragraph(2),
            'price' => fake()->randomFloat(2, 10, 500),
            'before_image' => null,
            'before_images' => [],
            'after_image' => null,
            'after_images' => [],
            'process_images' => [],
            'time_spent_hours' => fake()->numberBetween(1, 40),
            'materials_cost' => fake()->randomFloat(2, 5, 100),
            'status' => 'completed',
            'is_featured' => false,
            'views_count' => fake()->numberBetween(0, 1000),
        ];
    }
}
