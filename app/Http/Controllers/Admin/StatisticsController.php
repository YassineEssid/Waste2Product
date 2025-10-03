<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WasteItem;
use App\Models\RepairRequest;
use App\Models\Transformation;
use App\Models\CommunityEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Display advanced statistics and analytics
     */
    public function index()
    {
        // User analytics
        $usersByRole = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        // User registration trend (last 12 months)
        $userRegistrationTrend = User::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Activity statistics
        $activityStats = [
            'waste_items_this_month' => WasteItem::whereMonth('created_at', now()->month)->count(),
            'waste_items_last_month' => WasteItem::whereMonth('created_at', now()->subMonth()->month)->count(),
            'repairs_this_month' => RepairRequest::whereMonth('created_at', now()->month)->count(),
            'repairs_last_month' => RepairRequest::whereMonth('created_at', now()->subMonth()->month)->count(),
        ];

        // Top users by contributions
        $topContributors = User::withCount(['wasteItems', 'repairRequests'])
            ->orderByDesc('waste_items_count')
            ->take(10)
            ->get();

        // Environmental impact
        $environmentalImpact = [
            'total_co2_saved' => Transformation::sum('co2_saved') ?? 0,
            'waste_reduced_kg' => WasteItem::count() * 2.5,
            'items_recycled' => WasteItem::where('status', '!=', 'available')->count(),
        ];

        return view('admin.statistics', compact(
            'usersByRole',
            'userRegistrationTrend',
            'activityStats',
            'topContributors',
            'environmentalImpact'
        ));
    }
}
