<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\CommunityEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommunityEvent>
 */
class CommunityEventFactory extends Factory
{
    protected $model = CommunityEvent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('+1 week', '+2 months');
        
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(3),
            'image' => null,
            'location' => fake()->address(),
            'location_lat' => fake()->latitude(),
            'location_lng' => fake()->longitude(),
            'starts_at' => $startsAt,
            'ends_at' => fake()->dateTimeBetween($startsAt, $startsAt->format('Y-m-d H:i:s').' +4 hours'),
            'max_participants' => fake()->numberBetween(10, 100),
            'status' => 'upcoming',
        ];
    }
}
