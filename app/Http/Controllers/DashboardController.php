<?php

namespace App\Http\Controllers;

use App\Models\CommunityEvent;
use App\Models\MarketplaceItem;
use App\Models\Transformation;
use App\Models\WasteItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get global statistics (avec des noms de clés cohérents)
        $stats = [
            'available_items' => WasteItem::where('status', 'available')->count(),
            'total_transformations' => Transformation::where('status', 'published')->count(),
            'active_events' => CommunityEvent::where('starts_at', '>', now())->count(),
            'marketplace_items' => MarketplaceItem::where('status', 'available')->count(),
            'co2_saved' => Transformation::where('status', 'published')->sum('co2_saved') ?? 0,
            'waste_reduced' => WasteItem::count() * 2.5, // Estimation: 2.5kg par item
        ];

        // Get upcoming events (sans relation user)
        $upcomingEvents = CommunityEvent::where('starts_at', '>', now())
            ->orderBy('starts_at', 'asc')
            ->limit(5)
            ->get();

        // User stats simplifiés (éviter les relations qui n'existent pas)
        $myStats = [
            'waste_items' => 0,
            'repair_requests' => 0,
            'transformations' => 0,
            'event_registrations' => 0,
            'assigned_repairs' => 0,
            'marketplace_items' => 0,
        ];

        // Recent activity - collections vides pour éviter les erreurs
        $recentWasteItems = collect();
        $recentTransformations = collect();

        return view('dashboard', compact(
            'stats', 
            'upcomingEvents', 
            'myStats', 
            'recentWasteItems',
            'recentTransformations',
            'user'
        ));
    }
}