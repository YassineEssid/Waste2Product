<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WasteItem;
use App\Models\RepairRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RepairRequest>
 */
class RepairRequestFactory extends Factory
{
    protected $model = RepairRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'waste_item_id' => WasteItem::factory(),
            'repairer_id' => null,
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => 'pending',
            'repairer_notes' => null,
            'before_images' => [],
            'after_images' => [],
            'estimated_cost' => null,
            'actual_cost' => null,
            'assigned_at' => null,
            'started_at' => null,
            'completed_at' => null,
        ];
    }
}
