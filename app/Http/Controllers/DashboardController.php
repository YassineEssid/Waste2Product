<?php

namespace App\Http\Controllers;

use App\Models\WasteItem;
use App\Models\RepairRequest;
use App\Models\Transformation;
use App\Models\CommunityEvent;
use App\Models\MarketplaceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get statistics based on user role
        $stats = $this->getDashboardStats($user);
        
        // Get recent activities
        $recentWasteItems = WasteItem::with('user')
            ->available()
            ->latest()
            ->limit(5)
            ->get();
            
        $recentTransformations = Transformation::with('user', 'wasteItem')
            ->published()
            ->latest()
            ->limit(5)
            ->get();
            
        $upcomingEvents = CommunityEvent::with('user')
            ->upcoming()
            ->latest('starts_at')
            ->limit(5)
            ->get();
            
        // Get user-specific data
        $myStats = [
            'waste_items' => $user->wasteItems()->count(),
            'repair_requests' => $user->repairRequests()->count(),
            'transformations' => $user->transformations()->count(),
            'event_registrations' => $user->eventRegistrations()->count(),
        ];
        
        if ($user->isRepairer()) {
            $myStats['assigned_repairs'] = $user->assignedRepairs()
                ->whereIn('status', ['assigned', 'in_progress'])
                ->count();
        }
        
        if ($user->isArtisan()) {
            $myStats['marketplace_items'] = $user->marketplaceItems()->count();
        }

        return view('dashboard', compact(
            'stats', 
            'recentWasteItems', 
            'recentTransformations', 
            'upcomingEvents',
            'myStats'
        ));
    }

    private function getDashboardStats($user)
    {
        $stats = [
            'total_waste_items' => WasteItem::count(),
            'available_items' => WasteItem::available()->count(),
            'total_transformations' => Transformation::published()->count(),
            'active_events' => CommunityEvent::upcoming()->count(),
            'marketplace_items' => MarketplaceItem::available()->count(),
        ];

        // Add environmental impact
        $transformations = Transformation::published();
        $stats['co2_saved'] = $transformations->sum('co2_saved');
        $stats['waste_reduced'] = $transformations->sum('waste_reduced');

        return $stats;
    }
}
