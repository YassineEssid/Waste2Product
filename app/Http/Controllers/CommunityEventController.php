<?php

namespace App\Http\Controllers;

use App\Models\CommunityEvent;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CommunityEventController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->middleware('auth');
        $this->gamificationService = $gamificationService;
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
        $validated = $request->validate([
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:20|max:5000',
            'location' => 'nullable|string|max:255',
            'event_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:event_date',
            'max_participants' => 'nullable|integer|min:1|max:1000',
            'event_type' => 'nullable|in:workshop,cleanup,recycling,education,community,other',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'title.required' => 'Title is required.',
            'title.min' => 'Title must be at least 5 characters.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'description.required' => 'Description is required.',
            'description.min' => 'Description must be at least 20 characters.',
            'description.max' => 'Description cannot exceed 5000 characters.',
            'event_date.required' => 'Start date is required.',
            'event_date.after' => 'Start date must be in the future.',
            'end_date.required' => 'End date is required.',
            'end_date.after' => 'End date must be after start date.',
            'max_participants.integer' => 'Number of participants must be an integer.',
            'max_participants.min' => 'Minimum number of participants is 1.',
            'max_participants.max' => 'Maximum number of participants cannot exceed 1000.',
            'event_type.in' => 'Selected event type is not valid.',
            'image.image' => 'File must be an image.',
            'image.mimes' => 'Image must be in jpeg, png, jpg or gif format.',
            'image.max' => 'Image size cannot exceed 2 MB.',
        ]);

        // Additional conditional validations
        if (strlen(trim($request->title)) < 5) {
            return back()->withErrors([
                'title' => 'Title must contain at least 5 meaningful characters.'
            ])->withInput();
        }

        if (strlen(trim($request->description)) < 20) {
            return back()->withErrors([
                'description' => 'Description must contain at least 20 meaningful characters.'
            ])->withInput();
        }

        // Check that event duration is reasonable (max 30 days)
        $startDate = \Carbon\Carbon::parse($request->event_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $durationInDays = $startDate->diffInDays($endDate);

        if ($durationInDays > 30) {
            return back()->withErrors([
                'end_date' => 'Event duration cannot exceed 30 days.'
            ])->withInput();
        }

        if ($durationInDays < 0) {
            return back()->withErrors([
                'end_date' => 'End date must be after start date.'
            ])->withInput();
        }

        $data = [
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'starts_at' => $request->event_date,
            'ends_at' => $request->end_date,
            'status' => 'published'
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
            $data['image'] = $imagePath;
        }

        $event = CommunityEvent::create($data);

        // Award points for event creation
        $this->gamificationService->awardPoints(
            Auth::user(),
            'event_created',
            'Created event: ' . $event->title,
            $event
        );

        return redirect()->route('events.index')
            ->with('success', 'Event created successfully!');
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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'event_date' => 'required|date',
            'end_date' => 'required|date|after:event_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'starts_at' => $request->event_date,
            'ends_at' => $request->end_date,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($event->image && Storage::disk('public')->exists($event->image)) {
                Storage::disk('public')->delete($event->image);
            }
            $imagePath = $request->file('image')->store('events', 'public');
            $data['image'] = $imagePath;
        }

        $event->update($data);

        return redirect()->route('events.show', $event)
            ->with('success', 'Événement mis à jour avec succès !');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(CommunityEvent $event)
    {
        // Delete image if exists
        if ($event->image && Storage::disk('public')->exists($event->image)) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Événement supprimé avec succès !');
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
