<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\CommunityEvent;
use App\Models\EventRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventRegistration>
 */
class EventRegistrationFactory extends Factory
{
    protected $model = EventRegistration::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'community_event_id' => CommunityEvent::factory(),
            'status' => 'registered',
            'registered_at' => now(),
        ];
    }
}
