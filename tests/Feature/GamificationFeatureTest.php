<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GamificationFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_earns_points_for_creating_event()
    {
        $user = User::factory()->create(['total_points' => 0]);
        $service = app(GamificationService::class);

        $service->awardPoints($user, 'event_created', 'Created a new event');

        $user->refresh();
        $this->assertEquals(50, $user->total_points);
    }

    /** @test */
    public function user_levels_up_after_earning_enough_points()
    {
        $user = User::factory()->create([
            'total_points' => 50,
            'current_level' => 1,
        ]);
        $service = app(GamificationService::class);

        // Award 100 more points (total 150 = level 2)
        $service->awardPoints($user, 'event_created', 'Event 1');
        $service->awardPoints($user, 'event_created', 'Event 2');

        $user->refresh();
        $this->assertEquals(150, $user->total_points);
        $this->assertGreaterThanOrEqual(2, $user->current_level);
    }

    /** @test */
    public function leaderboard_shows_users_in_correct_order()
    {
        User::factory()->create(['name' => 'User 1', 'total_points' => 500]);
        User::factory()->create(['name' => 'User 2', 'total_points' => 300]);
        User::factory()->create(['name' => 'User 3', 'total_points' => 800]);

        $service = app(GamificationService::class);
        $leaderboard = $service->getLeaderboard(10);

        $this->assertEquals(800, $leaderboard[0]->total_points);
        $this->assertEquals(500, $leaderboard[1]->total_points);
        $this->assertEquals(300, $leaderboard[2]->total_points);
    }

    /** @test */
    public function user_rank_is_calculated_correctly()
    {
        $user1 = User::factory()->create(['total_points' => 500]);
        $user2 = User::factory()->create(['total_points' => 300]);
        $user3 = User::factory()->create(['total_points' => 800]);

        $service = app(GamificationService::class);

        $this->assertEquals(1, $service->getUserRank($user3));
        $this->assertEquals(2, $service->getUserRank($user1));
        $this->assertEquals(3, $service->getUserRank($user2));
    }
}
