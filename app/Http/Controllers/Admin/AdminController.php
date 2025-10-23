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
            'new_this_week' => User::where('created_at', '>=', now()->subWeek())->count(),
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
            'marketplace_available' => MarketplaceItem::where('status', 'available')->count(),
            'marketplace_sold' => MarketplaceItem::where('status', 'sold')->count(),
        ];

        // Environmental impact
        // Note: impact is a JSON column with structure: {co2_saved: X, waste_reduced: Y}
        // Using accessor getCo2SavedAttribute() from Transformation model
        $transformations = Transformation::whereNotNull('impact')->get();
        $totalCo2Saved = $transformations->sum('co2_saved'); // Using accessor

        $environmentalStats = [
            'total_co2_saved' => $totalCo2Saved,
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

        // Marketplace categories distribution
        $marketplaceCategories = MarketplaceItem::select('category', DB::raw('count(*) as count'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('count')
            ->take(6)
            ->get();

        // Event participation trend (last 6 months)
        $eventRegistrations = DB::table('event_registrations')
            ->join('community_events', 'event_registrations.community_event_id', '=', 'community_events.id')
            ->select(
                DB::raw('DATE_FORMAT(community_events.starts_at, "%Y-%m") as month'),
                DB::raw('count(*) as registrations')
            )
            ->where('community_events.starts_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Recent marketplace items
        $recentMarketplaceItems = MarketplaceItem::with('seller')
            ->latest()
            ->take(5)
            ->get();

        // Recent transformations
        $recentTransformations = Transformation::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Top artisans by transformations
        $topArtisans = User::where('role', 'artisan')
            ->withCount('transformations')
            ->orderByDesc('transformations_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'userStats',
            'contentStats',
            'environmentalStats',
            'recentUsers',
            'userGrowth',
            'marketplaceCategories',
            'eventRegistrations',
            'recentMarketplaceItems',
            'recentTransformations',
            'topArtisans'
        ));
    }
}
