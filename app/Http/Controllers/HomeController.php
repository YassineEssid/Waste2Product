<?php

namespace App\Http\Controllers;

use App\Models\WasteItem;
use App\Models\CommunityEvent;
use App\Models\MarketplaceItem;
use App\Models\Transformation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Display the front office home page.
     */
    public function index()
    {
        // Get statistics
        $stats = [
            'total_items' => WasteItem::count(),
            'items_recycled' => WasteItem::where('status', 'recycled')->count(),
            'transformations' => Transformation::count(),
            'community_members' => \App\Models\User::count(),
        ];

        // Get recent waste items (available)
        $recentItems = WasteItem::where('status', 'available')
            ->with('user')
            ->latest()
            ->limit(6)
            ->get();

        // Get upcoming events
        $upcomingEvents = CommunityEvent::where('starts_at', '>', Carbon::now())
            ->whereIn('status', ['published', 'upcoming'])
            ->orderBy('starts_at', 'asc')
            ->limit(3)
            ->get();

        // Get featured marketplace items
        $featuredItems = MarketplaceItem::where('status', 'available')
            ->with(['seller', 'images'])
            ->latest()
            ->limit(4)
            ->get();

        // Get recent transformations
        $recentTransformations = Transformation::where('status', 'completed')
            ->with('user')
            ->latest()
            ->limit(4)
            ->get();

        return view('front.home', compact(
            'stats',
            'recentItems',
            'upcomingEvents',
            'featuredItems',
            'recentTransformations'
        ));
    }

    /**
     * Display the about page.
     */
    public function about()
    {
        return view('front.about');
    }

    /**
     * Display the how it works page.
     */
    public function howItWorks()
    {
        return view('front.how-it-works');
    }

    /**
     * Display the contact page.
     */
    public function contact()
    {
        return view('front.contact');
    }

    /**
     * Display all events for public viewing.
     */
    public function events()
    {
        $events = CommunityEvent::whereIn('status', ['published', 'upcoming'])
            ->withCount('registrations')
            ->orderBy('starts_at', 'desc')
            ->paginate(12);

        return view('front.events', compact('events'));
    }

    /**
     * Display marketplace for public viewing.
     */
    public function marketplace()
    {
        $items = MarketplaceItem::where('status', 'available')
            ->with(['seller', 'images'])
            ->latest()
            ->paginate(16);

        return view('front.marketplace', compact('items'));
    }
}
