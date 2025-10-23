<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\UserBadge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BadgeController extends Controller
{
    /**
     * Display a listing of badges
     */
    public function index(Request $request)
    {
        $query = Badge::query()->active();

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by rarity
        if ($request->has('rarity') && $request->rarity !== 'all') {
            $query->where('rarity', $request->rarity);
        }

        // Search by name or description
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Order by rarity (highest first) then by required_points
        $badges = $query->orderBy('rarity', 'desc')
                        ->orderBy('required_points', 'desc')
                        ->paginate(12);

        // Get user's earned badges if authenticated
        $earnedBadgeIds = [];
        $userBadges = collect();
        if (Auth::check()) {
            $userBadges = Auth::user()->userBadges()->with('badge')->get();
            $earnedBadgeIds = $userBadges->where('earned_at', '!=', null)
                                        ->pluck('badge_id')
                                        ->toArray();
        }

        // Statistics
        $stats = [
            'total_badges' => Badge::active()->count(),
            'earned_badges' => Auth::check() ? Auth::user()->earnedBadges()->count() : 0,
            'legendary_badges' => Badge::active()->where('rarity', 4)->count(),
            'epic_badges' => Badge::active()->where('rarity', 3)->count(),
        ];

        return view('badges.index', compact('badges', 'earnedBadgeIds', 'userBadges', 'stats'));
    }

    /**
     * Display the specified badge
     */
    public function show(Badge $badge)
    {
        // Get users who earned this badge
        $holders = $badge->users()
                        ->wherePivot('earned_at', '!=', null)
                        ->orderByPivot('earned_at', 'desc')
                        ->limit(20)
                        ->get();

        // Get user's progress for this badge
        $userProgress = null;
        $isEarned = false;
        if (Auth::check()) {
            $userBadge = UserBadge::where('user_id', Auth::id())
                                  ->where('badge_id', $badge->id)
                                  ->first();

            if ($userBadge) {
                $userProgress = $userBadge->progress;
                $isEarned = $userBadge->earned_at !== null;
            }
        }

        // Get related badges (same type)
        $relatedBadges = Badge::active()
                             ->where('type', $badge->type)
                             ->where('id', '!=', $badge->id)
                             ->limit(4)
                             ->get();

        return view('badges.show', compact('badge', 'holders', 'userProgress', 'isEarned', 'relatedBadges'));
    }

    /**
     * Show user's badge collection
     */
    public function collection()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Get all badges with user's progress
        $allBadges = Badge::active()->get();
        $userBadges = $user->userBadges()->with('badge')->get()->keyBy('badge_id');

        // Group badges by status
        $earnedBadges = $allBadges->filter(function ($badge) use ($userBadges) {
            return isset($userBadges[$badge->id]) && $userBadges[$badge->id]->earned_at !== null;
        });

        $inProgressBadges = $allBadges->filter(function ($badge) use ($userBadges) {
            return isset($userBadges[$badge->id])
                && $userBadges[$badge->id]->earned_at === null
                && $userBadges[$badge->id]->progress > 0;
        });

        $lockedBadges = $allBadges->filter(function ($badge) use ($userBadges) {
            return !isset($userBadges[$badge->id])
                || ($userBadges[$badge->id]->earned_at === null && $userBadges[$badge->id]->progress === 0);
        });

        // Statistics
        $stats = [
            'total_badges' => $allBadges->count(),
            'earned_badges' => $earnedBadges->count(),
            'in_progress' => $inProgressBadges->count(),
            'locked_badges' => $lockedBadges->count(),
            'completion_rate' => $allBadges->count() > 0
                ? round(($earnedBadges->count() / $allBadges->count()) * 100, 1)
                : 0,
        ];

        return view('badges.collection', compact(
            'earnedBadges',
            'inProgressBadges',
            'lockedBadges',
            'userBadges',
            'stats'
        ));
    }

    /**
     * Toggle badge display status
     */
    public function toggleDisplay(Badge $badge)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userBadge = UserBadge::where('user_id', Auth::id())
                              ->where('badge_id', $badge->id)
                              ->first();

        if (!$userBadge || !$userBadge->earned_at) {
            return response()->json(['error' => 'Badge not earned'], 403);
        }

        $userBadge->is_displayed = !$userBadge->is_displayed;
        $userBadge->save();

        return response()->json([
            'success' => true,
            'is_displayed' => $userBadge->is_displayed,
        ]);
    }
}
