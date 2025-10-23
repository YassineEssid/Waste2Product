<?php

namespace Tests\Unit\Services;

use App\Models\Badge;
use App\Models\PointTransaction;
use App\Models\User;
use App\Models\UserBadge;
use App\Services\GamificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GamificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected GamificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GamificationService();
    }

    /** @test */
    public function it_can_award_points_to_user()
    {
        $user = User::factory()->create(['total_points' => 0]);

        $transaction = $this->service->awardPoints(
            $user,
            'event_created',
            'Created an event'
        );

        $this->assertInstanceOf(PointTransaction::class, $transaction);
        $this->assertEquals(50, $transaction->points);
        $this->assertEquals('event_created', $transaction->type);

        $user->refresh();
        $this->assertEquals(50, $user->total_points);
    }

    /** @test */
    public function it_throws_exception_for_invalid_point_type()
    {
        $user = User::factory()->create();

        $this->expectException(\InvalidArgumentException::class);

        $this->service->awardPoints(
            $user,
            'invalid_type',
            'Invalid'
        );
    }

    /** @test */
    public function it_calculates_level_correctly()
    {
        $this->assertEquals(1, $this->service->calculateLevel(0));
        $this->assertEquals(1, $this->service->calculateLevel(99));
        $this->assertEquals(2, $this->service->calculateLevel(100));
        $this->assertEquals(2, $this->service->calculateLevel(199));
        $this->assertEquals(3, $this->service->calculateLevel(200));
        $this->assertEquals(11, $this->service->calculateLevel(1000));
    }

    /** @test */
    public function it_calculates_points_for_next_level()
    {
        $this->assertEquals(200, $this->service->pointsForNextLevel(1));
        $this->assertEquals(300, $this->service->pointsForNextLevel(2));
        $this->assertEquals(600, $this->service->pointsForNextLevel(5));
    }

    /** @test */
    public function it_checks_level_up_correctly()
    {
        $user = User::factory()->create([
            'total_points' => 250,
            'current_level' => 1,
        ]);

        $leveledUp = $this->service->checkLevelUp($user);

        $this->assertTrue($leveledUp);
        $this->assertEquals(3, $user->current_level);
    }

    /** @test */
    public function it_does_not_level_up_if_not_enough_points()
    {
        $user = User::factory()->create([
            'total_points' => 50,
            'current_level' => 1,
        ]);

        $leveledUp = $this->service->checkLevelUp($user);

        $this->assertFalse($leveledUp);
        $this->assertEquals(1, $user->current_level);
    }

    /** @test */
    public function it_can_award_badge_to_user()
    {
        $user = User::factory()->create();
        $badge = Badge::factory()->create([
            'name' => 'First Event',
            'slug' => 'first-event',
            'required_count' => 1,
        ]);

        $userBadge = $this->service->awardBadge($user, $badge);

        $this->assertInstanceOf(UserBadge::class, $userBadge);
        $this->assertNotNull($userBadge->earned_at);
        $this->assertTrue($user->hasBadge('first-event'));
    }

    /** @test */
    public function it_does_not_award_same_badge_twice()
    {
        $user = User::factory()->create();
        $badge = Badge::factory()->create();

        $userBadge1 = $this->service->awardBadge($user, $badge);
        $userBadge2 = $this->service->awardBadge($user, $badge);

        $this->assertEquals($userBadge1->id, $userBadge2->id);
        $this->assertEquals(1, UserBadge::where('user_id', $user->id)->count());
    }

    /** @test */
    public function it_updates_badge_progress()
    {
        $user = User::factory()->create();
        $badge = Badge::factory()->create([
            'required_count' => 5,
        ]);

        $this->service->updateBadgeProgress($user, $badge, 3);

        $userBadge = UserBadge::where('user_id', $user->id)
                              ->where('badge_id', $badge->id)
                              ->first();

        $this->assertNotNull($userBadge);
        $this->assertEquals(3, $userBadge->progress);
        $this->assertNull($userBadge->earned_at);
    }

    /** @test */
    public function it_awards_badge_when_progress_reaches_requirement()
    {
        $user = User::factory()->create();
        $badge = Badge::factory()->create([
            'required_count' => 5,
        ]);

        $this->service->updateBadgeProgress($user, $badge, 5);

        $userBadge = UserBadge::where('user_id', $user->id)
                              ->where('badge_id', $badge->id)
                              ->first();

        $this->assertNotNull($userBadge->earned_at);
    }

    /** @test */
    public function it_gets_user_rank_correctly()
    {
        User::factory()->create(['total_points' => 500]);
        User::factory()->create(['total_points' => 300]);
        $user = User::factory()->create(['total_points' => 200]);
        User::factory()->create(['total_points' => 100]);

        $rank = $this->service->getUserRank($user);

        $this->assertEquals(3, $rank);
    }

    /** @test */
    public function it_gets_leaderboard()
    {
        User::factory()->create(['total_points' => 500]);
        User::factory()->create(['total_points' => 300]);
        User::factory()->create(['total_points' => 200]);

        $leaderboard = $this->service->getLeaderboard(10);

        $this->assertCount(3, $leaderboard);
        $this->assertEquals(500, $leaderboard->first()->total_points);
        $this->assertEquals(200, $leaderboard->last()->total_points);
    }

    /** @test */
    public function it_gets_user_stats()
    {
        $user = User::factory()->create([
            'total_points' => 150,
            'current_level' => 2,
        ]);

        Badge::factory()->create();
        Badge::factory()->create();

        $stats = $this->service->getUserStats($user);

        $this->assertArrayHasKey('total_points', $stats);
        $this->assertArrayHasKey('current_level', $stats);
        $this->assertArrayHasKey('rank', $stats);
        $this->assertArrayHasKey('badges_earned', $stats);
        $this->assertEquals(150, $stats['total_points']);
        $this->assertEquals(2, $stats['current_level']);
    }

    /** @test */
    public function it_maintains_balance_after_in_transaction()
    {
        $user = User::factory()->create(['total_points' => 100]);

        $transaction = $this->service->awardPoints(
            $user,
            'event_attended',
            'Attended event'
        );

        $this->assertEquals(130, $transaction->balance_after);
        $user->refresh();
        $this->assertEquals(130, $user->total_points);
    }
}
