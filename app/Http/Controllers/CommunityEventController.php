<?php

namespace App\Http\Controllers;

use App\Models\CommunityEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CommunityEventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of community events.
     */
    public function index(Request $request)
    {
        $query = CommunityEvent::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->status;
            $now = Carbon::now();

            switch ($status) {
                case 'upcoming':
                    $query->where('starts_at', '>', $now);
                    break;
                case 'ongoing':
                    $query->where('starts_at', '<=', $now)
                          ->where('ends_at', '>', $now);
                    break;
                case 'completed':
                    $query->where('ends_at', '<=', $now);
                    break;
            }
        }

        // Sort options
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'date_desc':
                    $query->orderBy('starts_at', 'desc');
                    break;
                case 'title':
                    $query->orderBy('title', 'asc');
                    break;
                default:
                    $query->orderBy('starts_at', 'asc');
            }
        } else {
            $query->orderBy('starts_at', 'asc');
        }

        $events = $query->paginate(12);

        // Get statistics
        $stats = [
            'total' => CommunityEvent::count(),
            'upcoming' => CommunityEvent::where('starts_at', '>', Carbon::now())->count(),
            'ongoing' => CommunityEvent::where('starts_at', '<=', Carbon::now())
                                ->where('ends_at', '>', Carbon::now())->count(),
        ];

        return view('events.index', compact('events', 'stats'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:event_date',
        ]);

        $event = CommunityEvent::create([
            'title' => $request->title,
            'description' => $request->description,
            'starts_at' => $request->event_date,
            'ends_at' => $request->end_date,
            'status' => 'published'
        ]);

        return redirect()->route('events.index')->with('success', 'Event created successfully!');
    }

    /**
     * Display the specified event.
     */
    public function show(CommunityEvent $event)
    {
        // Get related events (excluding current event)
        $relatedEvents = CommunityEvent::where('id', '!=', $event->id)
                                     ->where('starts_at', '>', Carbon::now())
                                     ->orderBy('starts_at', 'asc')
                                     ->limit(3)
                                     ->get();

        return view('events.show', compact('event', 'relatedEvents'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(CommunityEvent $event)
    {
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, CommunityEvent $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'end_date' => 'required|date|after:event_date',
        ]);

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'starts_at' => $request->event_date,
            'ends_at' => $request->end_date,
        ]);

        return redirect()->route('events.show', $event)->with('success', 'Event updated successfully!');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(CommunityEvent $event)
    {
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted successfully!');
    }

    /**
     * Register user for event (feature disabled temporarily).
     */
    public function register(CommunityEvent $event)
    {
        return redirect()->back()->with('info', 'Registration system is temporarily disabled.');
    }

    /**
     * Unregister user from event (feature disabled temporarily).
     */
    public function unregister(CommunityEvent $event)
    {
        return redirect()->back()->with('info', 'Registration system is temporarily disabled.');
    }

    /**
     * Export events to CSV (bonus feature).
     */
    public function export(Request $request)
    {
        $events = CommunityEvent::all();
        
        $fileName = 'events_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
        ];

        $callback = function() use ($events) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['Title', 'Description', 'Start Date', 'End Date', 'Status']);
            
            // Data
            foreach ($events as $event) {
                fputcsv($file, [
                    $event->title,
                    $event->description,
                    $event->starts_at->format('Y-m-d H:i'),
                    $event->ends_at->format('Y-m-d H:i'),
                    $event->current_status
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}