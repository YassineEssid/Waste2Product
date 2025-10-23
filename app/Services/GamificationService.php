<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;
use App\Models\UserBadge;
use App\Models\PointTransaction;
use App\Models\EventComment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GamificationService
{
    // Points configuration
    const POINTS = [
        'event_created' => 50,
        'event_attended' => 30,
        'comment_posted' => 10,
        'comment_approved' => 5,
        'rating_given' => 5,
        'registration' => 20,
        'profile_completed' => 25,
        'waste_item_posted' => 15,
        'marketplace_item_sold' => 40,
        'transformation_completed' => 60,
        'repair_completed' => 50,
    ];

    // Level configuration
    const LEVEL_MULTIPLIER = 100; // Points needed: level * 100

    /**
     * Award points to a user
     */
    public function awardPoints(User $user, string $type, string $description, $reference = null): PointTransaction
    {
        $points = self::POINTS[$type] ?? 0;

        if ($points <= 0) {
            throw new \InvalidArgumentException("Invalid point type: {$type}");
        }

        return DB::transaction(function () use ($user, $type, $description, $points, $reference) {
            // Create transaction
            $transaction = PointTransaction::create([
                'user_id' => $user->id,
                'points' => $points,
                'type' => $type,
                'description' => $description,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference ? $reference->id : null,
                'balance_after' => $user->total_points + $points,
            ]);

            // Update user points
            $user->total_points += $points;
            $user->last_point_earned_at = now();

            // Check for level up
            $this->checkLevelUp($user);

            $user->save();

            // Check for badge unlock
            $this->checkBadgeProgress($user, $type);

            Log::info("Points awarded", [
                'user_id' => $user->id,
                'points' => $points,
                'type' => $type,
                'balance' => $user->total_points,
            ]);

            return $transaction;
        });
    }

    /**
     * Check and update user level
     */
    public function checkLevelUp(User $user): bool
    {
        $newLevel = $this->calculateLevel($user->total_points);

        if ($newLevel > $user->current_level) {
            $oldLevel = $user->current_level;
            $user->current_level = $newLevel;
            $user->points_to_next_level = $this->pointsForNextLevel($newLevel);
            $user->title = $user->level_title;

            Log::info("Level up!", [
                'user_id' => $user->id,
                'old_level' => $oldLevel,
                'new_level' => $newLevel,
                'title' => $user->title,
            ]);

            // Award level up badge
            $this->checkLevelBadges($user);

            return true;
        }

        return false;
    }

    /**
     * Calculate level from total points
     */
    public function calculateLevel(int $points): int
    {
        return (int) floor($points / self::LEVEL_MULTIPLIER) + 1;
    }

    /**
     * Calculate points needed for next level
     */
    public function pointsForNextLevel(int $currentLevel): int
    {
        return ($currentLevel + 1) * self::LEVEL_MULTIPLIER;
    }

    /**
     * Award badge to user
     */
    public function awardBadge(User $user, Badge $badge): UserBadge
    {
        // Check if user already has this badge
        $userBadge = UserBadge::where('user_id', $user->id)
                              ->where('badge_id', $badge->id)
                              ->first();

        if ($userBadge && $userBadge->earned_at) {
            return $userBadge; // Already earned
        }

        if (!$userBadge) {
            $userBadge = UserBadge::create([
                'user_id' => $user->id,
                'badge_id' => $badge->id,
                'progress' => $badge->required_count,
                'earned_at' => now(),
                'is_displayed' => true,
            ]);
        } else {
            $userBadge->update([
                'earned_at' => now(),
                'progress' => $badge->required_count,
            ]);
        }

        Log::info("Badge earned!", [
            'user_id' => $user->id,
            'badge_id' => $badge->id,
            'badge_name' => $badge->name,
        ]);

        // Award bonus points for earning badge
        if ($badge->required_points > 0) {
            $this->awardPoints(
                $user,
                'achievement',
                "Badge earned: {$badge->name}",
                $badge
            );
        }

        return $userBadge;
    }

    /**
     * Update badge progress
     */
    public function updateBadgeProgress(User $user, Badge $badge, int $progress): void
    {
        $userBadge = UserBadge::firstOrCreate(
            [
                'user_id' => $user->id,
                'badge_id' => $badge->id,
            ],
            [
                'progress' => 0,
                'is_displayed' => true,
            ]
        );

        if (!$userBadge->earned_at) {
            $userBadge->progress = $progress;

            // Check if badge is earned
            if ($progress >= $badge->required_count) {
                $this->awardBadge($user, $badge);
            } else {
                $userBadge->save();
            }
        }
    }

    /**
     * Check badge progress based on action type
     */
    public function checkBadgeProgress(User $user, string $type): void
    {
        $badges = Badge::active()->where('requirement_type', $type)->get();

        foreach ($badges as $badge) {
            $progress = $this->calculateProgressForBadge($user, $badge);
            $this->updateBadgeProgress($user, $badge, $progress);
        }
    }

    /**
     * Calculate progress for a specific badge
     */
    protected function calculateProgressForBadge(User $user, Badge $badge): int
    {
        return match($badge->requirement_type) {
            'events_attended' => $user->eventRegistrations()->count(),
            'events_created' => $user->events()->count(),
            'comments_posted' => EventComment::where('user_id', $user->id)->count(),
            'waste_items_posted' => $user->wasteItems()->count(),
            'repairs_completed' => $user->assignedRepairs()->where('status', 'completed')->count(),
            'transformations_completed' => $user->transformations()->where('status', 'completed')->count(),
            default => 0,
        };
    }

    /**
     * Check and award level-based badges
     */
    protected function checkLevelBadges(User $user): void
    {
        $levelBadges = [
            5 => 'level-5-badge',
            10 => 'level-10-badge',
            20 => 'level-20-badge',
            30 => 'level-30-badge',
            50 => 'level-50-badge',
        ];

        foreach ($levelBadges as $level => $slug) {
            if ($user->current_level >= $level) {
                $badge = Badge::where('slug', $slug)->first();
                if ($badge && !$user->hasBadge($slug)) {
                    $this->awardBadge($user, $badge);
                }
            }
        }
    }

    /**
     * Get leaderboard
     */
    public function getLeaderboard(int $limit = 50, string $period = 'all'): \Illuminate\Database\Eloquent\Collection
    {
        $query = User::query();

        if ($period === 'month') {
            $query->whereHas('pointTransactions', function ($q) {
                $q->where('created_at', '>=', now()->startOfMonth());
            });
        } elseif ($period === 'week') {
            $query->whereHas('pointTransactions', function ($q) {
                $q->where('created_at', '>=', now()->startOfWeek());
            });
        }

        return $query->orderBy('total_points', 'desc')
                     ->orderBy('current_level', 'desc')
                     ->limit($limit)
                     ->get();
    }

    /**
     * Get user rank
     */
    public function getUserRank(User $user): int
    {
        return User::where('total_points', '>', $user->total_points)->count() + 1;
    }

    /**
     * Get user statistics
     */
    public function getUserStats(User $user): array
    {
        return [
            'total_points' => $user->total_points,
            'current_level' => $user->current_level,
            'level_title' => $user->level_title,
            'points_to_next_level' => $user->points_to_next_level,
            'progress_to_next_level' => $user->progress_to_next_level,
            'rank' => $this->getUserRank($user),
            'badges_earned' => $user->earnedBadges()->count(),
            'total_badges' => Badge::active()->count(),
            'recent_transactions' => $user->pointTransactions()->latest()->limit(10)->get(),
        ];
    }
}
