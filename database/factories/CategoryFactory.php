<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
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

        $category = $this->faker->randomElement($categories);

        return [
            'name' => $category['name'],
            'description' => $category['description'],
        ];
    }
}
