<?php

namespace App\Http\Controllers;

use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GamificationController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    /**
     * Show user's gamification profile
     */
    public function profile()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $stats = $this->gamificationService->getUserStats($user);

        // Get recent transactions
        $recentTransactions = $user->pointTransactions()
                                   ->with('reference')
                                   ->latest()
                                   ->limit(15)
                                   ->get();

        // Get earned badges
        $earnedBadges = $user->earnedBadges()
                            ->orderByPivot('earned_at', 'desc')
                            ->limit(8)
                            ->get();

        // Get in-progress badges
        $inProgressBadges = $user->userBadges()
                                ->whereNull('earned_at')
                                ->where('progress', '>', 0)
                                ->with('badge')
                                ->orderBy('progress', 'desc')
                                ->limit(5)
                                ->get();

        // Points breakdown by type
        $pointsBreakdown = $user->pointTransactions()
                               ->selectRaw('type, SUM(points) as total_points, COUNT(*) as count')
                               ->where('points', '>', 0)
                               ->groupBy('type')
                               ->orderBy('total_points', 'desc')
                               ->get();

        // Activity chart data (last 30 days)
        $activityData = $user->pointTransactions()
                            ->selectRaw('DATE(created_at) as date, SUM(points) as points')
                            ->where('created_at', '>=', now()->subDays(30))
                            ->groupBy('date')
                            ->orderBy('date')
                            ->get()
                            ->pluck('points', 'date')
                            ->toArray();

        return view('gamification.profile', compact(
            'user',
            'stats',
            'recentTransactions',
            'earnedBadges',
            'inProgressBadges',
            'pointsBreakdown',
            'activityData'
        ));
    }

    /**
     * Get user's activity history
     */
    public function activity()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Get all transactions with pagination
        $transactions = $user->pointTransactions()
                            ->with('reference')
                            ->latest()
                            ->paginate(20);

        // Filter stats
        $filters = [
            'today' => $user->pointTransactions()->whereDate('created_at', today())->sum('points'),
            'week' => $user->pointTransactions()->where('created_at', '>=', now()->startOfWeek())->sum('points'),
            'month' => $user->pointTransactions()->where('created_at', '>=', now()->startOfMonth())->sum('points'),
            'year' => $user->pointTransactions()->where('created_at', '>=', now()->startOfYear())->sum('points'),
        ];

        return view('gamification.activity', compact('transactions', 'filters'));
    }

    /**
     * Show achievements overview
     */
    public function achievements()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Milestones
        $milestones = [
            [
                'title' => 'First Event',
                'description' => 'Create your first community event',
                'icon' => 'fa-flag',
                'completed' => $user->events()->count() > 0,
                'current' => $user->events()->count(),
                'required' => 1,
            ],
            [
                'title' => 'Social Butterfly',
                'description' => 'Attend 10 community events',
                'icon' => 'fa-users',
                'completed' => $user->eventRegistrations()->count() >= 10,
                'current' => $user->eventRegistrations()->count(),
                'required' => 10,
            ],
            [
                'title' => 'Comment Master',
                'description' => 'Post 50 comments',
                'icon' => 'fa-comment',
                'completed' => $user->eventComments()->count() >= 50,
                'current' => $user->eventComments()->count(),
                'required' => 50,
            ],
            [
                'title' => 'Eco Warrior',
                'description' => 'Post 25 waste items for transformation',
                'icon' => 'fa-recycle',
                'completed' => $user->wasteItems()->count() >= 25,
                'current' => $user->wasteItems()->count(),
                'required' => 25,
            ],
            [
                'title' => 'Master Repairer',
                'description' => 'Complete 20 repair requests',
                'icon' => 'fa-wrench',
                'completed' => $user->assignedRepairs()->where('status', 'completed')->count() >= 20,
                'current' => $user->assignedRepairs()->where('status', 'completed')->count(),
                'required' => 20,
            ],
            [
                'title' => 'Transformation Expert',
                'description' => 'Complete 15 transformations',
                'icon' => 'fa-magic',
                'completed' => $user->transformations()->where('status', 'completed')->count() >= 15,
                'current' => $user->transformations()->where('status', 'completed')->count(),
                'required' => 15,
            ],
        ];

        // Calculate overall completion
        $completedMilestones = collect($milestones)->where('completed', true)->count();
        $totalMilestones = count($milestones);
        $completionRate = $totalMilestones > 0 ? round(($completedMilestones / $totalMilestones) * 100) : 0;

        return view('gamification.achievements', compact('milestones', 'completionRate'));
    }

    /**
     * Get points information
     */
    public function pointsInfo()
    {
        $pointsConfig = [
            'Actions' => [
                ['action' => 'Create Event', 'points' => GamificationService::POINTS['event_created'], 'icon' => 'fa-calendar-plus'],
                ['action' => 'Attend Event', 'points' => GamificationService::POINTS['event_attended'], 'icon' => 'fa-check-circle'],
                ['action' => 'Post Comment', 'points' => GamificationService::POINTS['comment_posted'], 'icon' => 'fa-comment'],
                ['action' => 'Comment Approved', 'points' => GamificationService::POINTS['comment_approved'], 'icon' => 'fa-thumbs-up'],
                ['action' => 'Give Rating', 'points' => GamificationService::POINTS['rating_given'], 'icon' => 'fa-star'],
            ],
            'Profile' => [
                ['action' => 'Registration', 'points' => GamificationService::POINTS['registration'], 'icon' => 'fa-user-plus'],
                ['action' => 'Complete Profile', 'points' => GamificationService::POINTS['profile_completed'], 'icon' => 'fa-id-card'],
            ],
            'Marketplace' => [
                ['action' => 'Post Waste Item', 'points' => GamificationService::POINTS['waste_item_posted'], 'icon' => 'fa-recycle'],
                ['action' => 'Sell Marketplace Item', 'points' => GamificationService::POINTS['marketplace_item_sold'], 'icon' => 'fa-shopping-cart'],
            ],
            'Services' => [
                ['action' => 'Complete Transformation', 'points' => GamificationService::POINTS['transformation_completed'], 'icon' => 'fa-magic'],
                ['action' => 'Complete Repair', 'points' => GamificationService::POINTS['repair_completed'], 'icon' => 'fa-wrench'],
            ],
        ];

        return view('gamification.points-info', compact('pointsConfig'));
    }
}
