<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WasteItem;
use App\Models\RepairRequest;
use App\Models\Transformation;
use App\Models\CommunityEvent;
use App\Models\MarketplaceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with statistics and analytics
     */
    public function index()
    {
        // User statistics
        $userStats = [
            'total' => User::count(),
            'users' => User::where('role', 'user')->count(),
            'repairers' => User::where('role', 'repairer')->count(),
            'artisans' => User::where('role', 'artisan')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        // Content statistics
        $contentStats = [
            'waste_items' => WasteItem::count(),
            'available_items' => WasteItem::where('status', 'available')->count(),
            'repair_requests' => RepairRequest::count(),
            'pending_repairs' => RepairRequest::where('status', 'pending')->count(),
            'transformations' => Transformation::count(),
            'published_transformations' => Transformation::where('status', 'published')->count(),
            'community_events' => CommunityEvent::count(),
            'upcoming_events' => CommunityEvent::where('starts_at', '>', now())->count(),
            'marketplace_items' => MarketplaceItem::count(),
        ];

        // Environmental impact
        $environmentalStats = [
            'total_co2_saved' => Transformation::sum('co2_saved') ?? 0,
            'total_waste_reduced' => WasteItem::count() * 2.5, // 2.5kg per item estimation
        ];

        // Recent users
        $recentUsers = User::latest()->take(5)->get();

        // User growth chart data (last 6 months)
        $userGrowth = User::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return view('admin.dashboard', compact(
            'userStats',
            'contentStats',
            'environmentalStats',
            'recentUsers',
            'userGrowth'
        ));
    }
}
