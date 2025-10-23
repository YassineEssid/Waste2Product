<?php

namespace Tests\Unit\Models;

use App\Models\Badge;
use App\Models\CommunityEvent;
use App\Models\EventRegistration;
use App\Models\MarketplaceItem;
use App\Models\PointTransaction;
use App\Models\RepairRequest;
use App\Models\Transformation;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\WasteItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'user',
        ]);

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('user', $user->role);
    }

    /** @test */
    public function it_hides_password_and_remember_token()
    {
        $user = User::factory()->create();
        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    /** @test */
    public function it_checks_if_user_has_role()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('user'));
    }

    /** @test */
    public function it_checks_if_user_is_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($user->isAdmin());
    }

    /** @test */
    public function it_checks_if_user_is_repairer()
    {
        $repairer = User::factory()->create(['role' => 'repairer']);
        $user = User::factory()->create(['role' => 'user']);

        $this->assertTrue($repairer->isRepairer());
        $this->assertFalse($user->isRepairer());
    }

    /** @test */
    public function it_checks_if_user_is_artisan()
    {
        $artisan = User::factory()->create(['role' => 'artisan']);
        $user = User::factory()->create(['role' => 'user']);

        $this->assertTrue($artisan->isArtisan());
        $this->assertFalse($user->isArtisan());
    }

    /** @test */
    public function it_has_waste_items_relationship()
    {
        $user = User::factory()->create();
        WasteItem::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->wasteItems);
    }

    /** @test */
    public function it_has_repair_requests_relationship()
    {
        $user = User::factory()->create();
        RepairRequest::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->repairRequests);
    }

    /** @test */
    public function it_has_assigned_repairs_relationship()
    {
        $repairer = User::factory()->create(['role' => 'repairer']);
        RepairRequest::factory()->count(2)->create(['repairer_id' => $repairer->id]);

        $this->assertCount(2, $repairer->assignedRepairs);
    }

    /** @test */
    public function it_has_transformations_relationship()
    {
        $user = User::factory()->create();
        Transformation::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->transformations);
    }

    /** @test */
    public function it_has_events_relationship()
    {
        $user = User::factory()->create();
        CommunityEvent::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->events);
    }

    /** @test */
    public function it_has_event_registrations_relationship()
    {
        $user = User::factory()->create();
        EventRegistration::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->eventRegistrations);
    }

    /** @test */
    public function it_has_marketplace_items_relationship()
    {
        $user = User::factory()->create();
        MarketplaceItem::factory()->count(3)->create(['seller_id' => $user->id]);

        $this->assertCount(3, $user->marketplaceItems);
    }

    /** @test */
    public function it_has_point_transactions_relationship()
    {
        $user = User::factory()->create();
        PointTransaction::factory()->count(5)->create(['user_id' => $user->id]);

        $this->assertCount(5, $user->pointTransactions);
    }

    /** @test */
    public function it_gets_level_title_for_beginner()
    {
        $user = User::factory()->create(['current_level' => 1]);
        $this->assertEquals('Eco Beginner', $user->level_title);
    }

    /** @test */
    public function it_gets_level_title_for_contributor()
    {
        $user = User::factory()->create(['current_level' => 5]);
        $this->assertEquals('Green Contributor', $user->level_title);
    }

    /** @test */
    public function it_gets_level_title_for_enthusiast()
    {
        $user = User::factory()->create(['current_level' => 10]);
        $this->assertEquals('Eco Enthusiast', $user->level_title);
    }

    /** @test */
    public function it_gets_level_title_for_expert()
    {
        $user = User::factory()->create(['current_level' => 20]);
        $this->assertEquals('Environmental Expert', $user->level_title);
    }

    /** @test */
    public function it_gets_level_title_for_champion()
    {
        $user = User::factory()->create(['current_level' => 30]);
        $this->assertEquals('Green Champion', $user->level_title);
    }

    /** @test */
    public function it_gets_level_title_for_master()
    {
        $user = User::factory()->create(['current_level' => 40]);
        $this->assertEquals('Sustainability Master', $user->level_title);
    }

    /** @test */
    public function it_gets_level_title_for_legend()
    {
        $user = User::factory()->create(['current_level' => 50]);
        $this->assertEquals('Eco Legend', $user->level_title);
    }

    /** @test */
    public function it_calculates_progress_to_next_level()
    {
        $user = User::factory()->create([
            'total_points' => 150,
            'points_to_next_level' => 200,
        ]);

        $progress = $user->progress_to_next_level;
        $this->assertGreaterThan(0, $progress);
        $this->assertLessThanOrEqual(100, $progress);
    }

    /** @test */
    public function it_checks_if_user_has_badge()
    {
        $user = User::factory()->create();
        $badge = Badge::factory()->create(['slug' => 'test-badge']);

        UserBadge::create([
            'user_id' => $user->id,
            'badge_id' => $badge->id,
            'earned_at' => now(),
        ]);

        $this->assertTrue($user->hasBadge('test-badge'));
        $this->assertFalse($user->hasBadge('non-existent-badge'));
    }

    /** @test */
    public function it_gets_only_earned_badges()
    {
        $user = User::factory()->create();
        $badge1 = Badge::factory()->create();
        $badge2 = Badge::factory()->create();

        // Earned badge
        UserBadge::create([
            'user_id' => $user->id,
            'badge_id' => $badge1->id,
            'earned_at' => now(),
        ]);

        // In-progress badge
        UserBadge::create([
            'user_id' => $user->id,
            'badge_id' => $badge2->id,
            'earned_at' => null,
            'progress' => 3,
        ]);

        $this->assertCount(1, $user->earnedBadges);
        $this->assertCount(2, $user->userBadges);
    }

    /** @test */
    public function it_casts_location_coordinates_to_decimal()
    {
        $user = User::factory()->create([
            'location_lat' => 36.8065,
            'location_lng' => 10.1815,
        ]);

        $this->assertIsFloat($user->location_lat);
        $this->assertIsFloat($user->location_lng);
        $this->assertEquals(36.8065, $user->location_lat);
        $this->assertEquals(10.1815, $user->location_lng);
    }
}
