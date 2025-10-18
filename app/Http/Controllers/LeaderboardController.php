<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    /**
     * Display the leaderboard
     */
    public function index(Request $request)
    {
        // Period filter (all, month, week)
        $period = $request->get('period', 'all');

        // Category filter (overall, events, comments, etc.)
        $category = $request->get('category', 'overall');

        // Get base leaderboard
        $users = $this->getLeaderboardUsers($period, $category);

        // Get current user's rank
        $userRank = null;
        $userStats = null;
        if (Auth::check()) {
            $userRank = $this->gamificationService->getUserRank(Auth::user());
            $userStats = $this->gamificationService->getUserStats(Auth::user());
        }

        // Top performers of the week
        $weeklyTop = User::whereHas('pointTransactions', function ($q) {
                            $q->where('created_at', '>=', now()->startOfWeek());
                        })
                        ->withSum(['pointTransactions' => function ($query) {
                            $query->where('created_at', '>=', now()->startOfWeek());
                        }], 'points')
                        ->orderBy('point_transactions_sum_points', 'desc')
                        ->limit(3)
                        ->get();

        // Statistics
        $stats = [
            'total_users' => User::count(),
            'active_this_week' => User::whereHas('pointTransactions', function ($q) {
                $q->where('created_at', '>=', now()->startOfWeek());
            })->count(),
            'total_points_awarded' => DB::table('point_transactions')->sum('points'),
            'avg_user_level' => round(User::avg('current_level'), 1),
        ];

        return view('leaderboard.index', compact(
            'users',
            'userRank',
            'userStats',
            'weeklyTop',
            'stats',
            'period',
            'category'
        ));
    }

    /**
     * Get leaderboard users based on period and category
     */
    protected function getLeaderboardUsers(string $period, string $category)
    {
        $query = User::query();

        // Apply period filter
        if ($period === 'month') {
            $query->whereHas('pointTransactions', function ($q) {
                $q->where('created_at', '>=', now()->startOfMonth());
            })
            ->withSum(['pointTransactions' => function ($query) {
                $query->where('created_at', '>=', now()->startOfMonth());
            }], 'points')
            ->orderBy('point_transactions_sum_points', 'desc');
        } elseif ($period === 'week') {
            $query->whereHas('pointTransactions', function ($q) {
                $q->where('created_at', '>=', now()->startOfWeek());
            })
            ->withSum(['pointTransactions' => function ($query) {
                $query->where('created_at', '>=', now()->startOfWeek());
            }], 'points')
            ->orderBy('point_transactions_sum_points', 'desc');
        } else {
            // All time
            $query->orderBy('total_points', 'desc')
                  ->orderBy('current_level', 'desc');
        }

        // Apply category filter
        if ($category === 'events') {
            $query->whereHas('pointTransactions', function ($q) {
                $q->where('type', 'event_created')
                  ->orWhere('type', 'event_attended');
            });
        } elseif ($category === 'comments') {
            $query->whereHas('pointTransactions', function ($q) {
                $q->where('type', 'comment_posted')
                  ->orWhere('type', 'comment_approved');
            });
        } elseif ($category === 'marketplace') {
            $query->whereHas('pointTransactions', function ($q) {
                $q->where('type', 'marketplace_item_sold')
                  ->orWhere('type', 'waste_item_posted');
            });
        }

        return $query->with('earnedBadges')
                    ->limit(50)
                    ->get()
                    ->map(function ($user, $index) use ($period) {
                        $user->rank = $index + 1;

                        // Calculate period points if applicable
                        if ($period === 'month' || $period === 'week') {
                            $user->period_points = $user->point_transactions_sum_points ?? 0;
                        }

                        return $user;
                    });
    }

    /**
     * Show user's leaderboard profile
     */
    public function userProfile(User $user)
    {
        $stats = $this->gamificationService->getUserStats($user);

        // Get user's rank
        $rank = $this->gamificationService->getUserRank($user);

        // Get recent activity
        $recentTransactions = $user->pointTransactions()
                                   ->latest()
                                   ->limit(20)
                                   ->get();

        // Get earned badges
        $earnedBadges = $user->earnedBadges()
                            ->orderByPivot('earned_at', 'desc')
                            ->get();

        // Get nearby competitors
        $nearbyUsers = User::whereBetween('total_points', [
                            $user->total_points - 500,
                            $user->total_points + 500
                        ])
                        ->where('id', '!=', $user->id)
                        ->orderBy('total_points', 'desc')
                        ->limit(5)
                        ->get();

        return view('leaderboard.profile', compact(
            'user',
            'stats',
            'rank',
            'recentTransactions',
            'earnedBadges',
            'nearbyUsers'
        ));
    }
}
