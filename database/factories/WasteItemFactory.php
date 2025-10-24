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
        // Predefined categories to avoid duplicates
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic waste items'],
            ['name' => 'Furniture', 'description' => 'Furniture and home items'],
            ['name' => 'Clothing', 'description' => 'Clothing and textiles'],
            ['name' => 'Metal', 'description' => 'Metal materials'],
            ['name' => 'Plastic', 'description' => 'Plastic materials'],
            ['name' => 'Glass', 'description' => 'Glass materials'],
            ['name' => 'Paper', 'description' => 'Paper and cardboard'],
            ['name' => 'Organic', 'description' => 'Organic waste'],
            ['name' => 'Other', 'description' => 'Other materials'],
        ];

        $categoryData = fake()->randomElement($categories);
        
        // Use firstOrCreate to avoid duplicate key errors
        $category = Category::firstOrCreate(
            ['name' => $categoryData['name']],
            ['description' => $categoryData['description']]
        );

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'category_id' => $category->id,
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
