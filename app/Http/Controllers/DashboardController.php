<?php

namespace App\Http\Controllers;

use App\Models\CommunityEvent;
use App\Models\MarketplaceItem;
use App\Models\RepairRequest;
use App\Models\Transformation;
use App\Models\WasteItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Rediriger vers le dashboard approprié selon le rôle
        switch ($user->role) {
            case 'admin':
                return $this->adminDashboard();
            case 'repairer':
                return $this->repairerDashboard();
            case 'artisan':
                return $this->artisanDashboard();
            default:
                return $this->userDashboard();
        }
    }

    /**
     * Dashboard pour les utilisateurs normaux
     */
    protected function userDashboard()
    {
        $user = Auth::user();

        // Statistiques globales
        $stats = [
            'available_items' => WasteItem::where('status', 'available')->count(),
            'total_transformations' => Transformation::count(),
            'active_events' => CommunityEvent::where('starts_at', '>', now())->count(),
            'marketplace_items' => MarketplaceItem::where('status', 'available')->count(),
        ];

        // Mes statistiques
        $myStats = [
            'my_waste_items' => WasteItem::where('user_id', $user->id)->count(),
            'my_repair_requests' => RepairRequest::where('user_id', $user->id)->count(),
            'my_events' => $user->eventRegistrations()->count(),
        ];

        // Événements à venir
        $upcomingEvents = CommunityEvent::where('starts_at', '>', now())
            ->orderBy('starts_at', 'asc')
            ->limit(5)
            ->get();

        // Mes articles récents
        $myWasteItems = WasteItem::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboards.user', compact('stats', 'myStats', 'upcomingEvents', 'myWasteItems', 'user'));
    }

    /**
     * Dashboard pour les réparateurs
     */
    protected function repairerDashboard()
    {
        $user = Auth::user();

        // Statistiques de réparation
        $stats = [
            'pending_repairs' => RepairRequest::where('status', 'pending')->count(),
            'my_accepted_repairs' => RepairRequest::where('repairer_id', $user->id)
                ->where('status', 'accepted')
                ->count(),
            'my_completed_repairs' => RepairRequest::where('repairer_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'total_earnings' => RepairRequest::where('repairer_id', $user->id)
                ->where('status', 'completed')
                ->sum('actual_cost') ?? 0,
        ];

        // Demandes en attente
        $pendingRepairs = RepairRequest::where('status', 'pending')
            ->with('user', 'wasteItem')
            ->latest()
            ->limit(10)
            ->get();

        // Mes réparations en cours
        $myActiveRepairs = RepairRequest::where('repairer_id', $user->id)
            ->whereIn('status', ['accepted', 'in_progress'])
            ->with('user', 'wasteItem')
            ->latest()
            ->get();

        // Historique de mes réparations
        $myCompletedRepairs = RepairRequest::where('repairer_id', $user->id)
            ->where('status', 'completed')
            ->with('user', 'wasteItem')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboards.repairer', compact('stats', 'pendingRepairs', 'myActiveRepairs', 'myCompletedRepairs', 'user'));
    }

    /**
     * Dashboard pour les artisans
     */
    protected function artisanDashboard()
    {
        $user = Auth::user();

        // Statistiques de transformation
        $stats = [
            'my_transformations' => Transformation::where('artisan_id', $user->id)->count(),
            'pending_transformations' => Transformation::where('artisan_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'in_progress_transformations' => Transformation::where('artisan_id', $user->id)
                ->where('status', 'in_progress')
                ->count(),
            'completed_transformations' => Transformation::where('artisan_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'marketplace_items' => MarketplaceItem::where('seller_id', $user->id)->count(),
            'total_revenue' => MarketplaceItem::where('seller_id', $user->id)
                ->where('status', 'sold')
                ->sum('price') ?? 0,
        ];

        // Mes transformations récentes
        $myTransformations = Transformation::where('artisan_id', $user->id)
            ->with('wasteItem')
            ->latest()
            ->limit(6)
            ->get();

        // Articles disponibles à transformer
        $availableWasteItems = WasteItem::where('status', 'available')
            ->latest()
            ->limit(8)
            ->get();

        // Mes articles en vente sur marketplace
        $myMarketplaceItems = MarketplaceItem::where('seller_id', $user->id)
            ->latest()
            ->limit(6)
            ->get();

        return view('dashboards.artisan', compact('stats', 'myTransformations', 'availableWasteItems', 'myMarketplaceItems', 'user'));
    }

    /**
     * Dashboard pour les administrateurs (redirection vers panel admin)
     */
    protected function adminDashboard()
    {
        return redirect()->route('admin.dashboard');
    }
}
