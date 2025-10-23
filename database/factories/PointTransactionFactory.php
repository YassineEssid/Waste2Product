<?php

namespace Database\Factories;

use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PointTransactionFactory extends Factory
{
    protected $model = PointTransaction::class;

    public function definition(): array
    {
        $points = $this->faker->numberBetween(5, 100);
        
        return [
            'user_id' => User::factory(),
            'points' => $points,
            'type' => $this->faker->randomElement([
                'event_created',
                'event_attended',
                'comment_posted',
                'waste_item_posted',
            ]),
            'description' => $this->faker->sentence,
            'balance_after' => $this->faker->numberBetween($points, 1000),
            'reference_type' => null,
            'reference_id' => null,
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
