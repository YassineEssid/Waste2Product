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
    public function events(Request $request)
    {
        $query = CommunityEvent::whereIn('status', ['published', 'upcoming'])
            ->withCount('registrations');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $now = Carbon::now();
            switch ($request->status) {
                case 'upcoming':
                    $query->where('starts_at', '>', $now);
                    break;
                case 'ongoing':
                    $query->where('starts_at', '<=', $now)
                          ->where(function($q) use ($now) {
                              $q->whereNull('ends_at')
                                ->orWhere('ends_at', '>=', $now);
                          });
                    break;
                case 'completed':
                    $query->whereNotNull('ends_at')
                          ->where('ends_at', '<', $now);
                    break;
            }
        }

        $events = $query->orderBy('starts_at', 'desc')->paginate(12);

        return view('front.events', compact('events'));
    }

    /**
     * Display marketplace for public viewing.
     */
    public function marketplace(Request $request)
    {
        $query = MarketplaceItem::where('status', 'available')
            ->with(['seller', 'images']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Sort filter
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'popular':
                    $query->orderBy('views_count', 'desc');
                    break;
                case 'recent':
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $items = $query->paginate(16);

        return view('front.marketplace', compact('items'));
    }

    /**
     * AJAX search for events (real-time)
     */
    public function searchEvents(Request $request)
    {
        $query = CommunityEvent::whereIn('status', ['published', 'upcoming'])
            ->withCount('registrations');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $now = Carbon::now();
            switch ($request->status) {
                case 'upcoming':
                    $query->where('starts_at', '>', $now);
                    break;
                case 'ongoing':
                    $query->where('starts_at', '<=', $now)
                          ->where(function($q) use ($now) {
                              $q->whereNull('ends_at')
                                ->orWhere('ends_at', '>=', $now);
                          });
                    break;
                case 'completed':
                    $query->whereNotNull('ends_at')
                          ->where('ends_at', '<', $now);
                    break;
            }
        }

        $events = $query->orderBy('starts_at', 'desc')->paginate(12);

        return response()->json([
            'events' => $events->items(),
            'pagination' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ]
        ]);
    }

    /**
     * AJAX search for marketplace (real-time)
     */
    public function searchMarketplace(Request $request)
    {
        $query = MarketplaceItem::where('status', 'available')
            ->with(['seller', 'images']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Sort filter
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'popular':
                    $query->orderBy('views_count', 'desc');
                    break;
                case 'recent':
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $items = $query->paginate(16);

        return response()->json([
            'items' => $items->items(),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ]
        ]);
    }
}
