<?php

namespace App\Http\Controllers;

use App\Models\CommunityEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
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
        $query = CommunityEvent::with('creator', 'attendees');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location_address', 'like', "%{$search}%");
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

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Sort by date
        $query->orderBy('starts_at', 'asc');

        $events = $query->paginate(12);

        // Get statistics
        $stats = [
            'total' => CommunityEvent::count(),
            'upcoming' => CommunityEvent::where('starts_at', '>', Carbon::now())->count(),
            'participants' => DB::table('community_event_user')->count(),
        ];

        return view('events.index', compact('events', 'stats'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        $types = ['workshop', 'cleanup', 'exhibition', 'seminar', 'other']; // Only allowed types
        return view('events.create', compact('types'));
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:workshop,cleanup,exhibition,seminar,other', // Only allowed types
            'event_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:event_date',
            'location' => 'required|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $event = new CommunityEvent();
        $event->title = $request->title;
        $event->description = $request->description;
        $event->type = $request->type;
        $event->starts_at = $request->event_date;
        $event->ends_at = $request->end_date;
        $event->location_address = $request->location;
        $event->max_participants = $request->max_participants;
        $event->user_id = Auth::id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            $event->images = json_encode([$path]);
        }

        $event->save();

        return redirect()->route('events.index')->with('success', 'Event created successfully!');
    }

    /**
     * Display the specified event.
     */
    public function show(CommunityEvent $event)
    {
        $event->load('creator', 'attendees');
        $isAttending = Auth::check() && $event->attendees->contains(Auth::id());
        $canRegister = !$isAttending &&
                      ($event->max_participants === null || $event->attendees->count() < $event->max_participants) &&
                      $event->starts_at > Carbon::now();

        // Get related events
        $relatedEvents = CommunityEvent::where('type', $event->type)
                                     ->where('id', '!=', $event->id)
                                     ->where('starts_at', '>', Carbon::now())
                                     ->limit(3)
                                     ->get();

        return view('events.show', compact('event', 'isAttending', 'canRegister', 'relatedEvents'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(CommunityEvent $event)
    {
        $this->authorize('update', $event);
        $types = ['workshop', 'cleanup', 'exhibition', 'seminar', 'other'];
        return view('events.edit', compact('event', 'types'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, CommunityEvent $event)
    {
        $this->authorize('update', $event);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:workshop,cleanup,exhibition,seminar,other',
            'event_date' => 'required|date',
            'end_date' => 'required|date|after:event_date',
            'location' => 'required|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Map form fields to model attributes
        $data = $request->except(['image']);
        $data['starts_at'] = $request->event_date;
        $data['ends_at'] = $request->end_date;
        $data['location_address'] = $request->location;

        $event->fill($data);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image(s)
            if ($event->images) {
                foreach (json_decode($event->images, true) as $img) {
                    Storage::disk('public')->delete($img);
                }
            }
            $path = $request->file('image')->store('events', 'public');
            $event->images = json_encode([$path]);
        }

        $event->save();

        return redirect()->route('events.show', $event)->with('success', 'Event updated successfully!');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(CommunityEvent $event)
    {
        $this->authorize('delete', $event);

        // Delete image(s)
        if ($event->images) {
            foreach (json_decode($event->images, true) as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted successfully!');
    }

    /**
     * Register user for event.
     */
    public function register(CommunityEvent $event)
    {
        if ($event->attendees->contains(Auth::id())) {
            return redirect()->back()->with('error', 'You are already registered for this event.');
        }

        if ($event->max_participants && $event->attendees->count() >= $event->max_participants) {
            return redirect()->back()->with('error', 'This event is full.');
        }

        if ($event->starts_at <= Carbon::now()) {
            return redirect()->back()->with('error', 'Registration has closed for this event.');
        }

        $event->attendees()->attach(Auth::id());

        return redirect()->back()->with('success', 'You have successfully registered for this event!');
    }

    /**
     * Unregister user from event.
     */
    public function unregister(CommunityEvent $event)
    {
        $event->attendees()->detach(Auth::id());

        return redirect()->back()->with('success', 'You have been unregistered from this event.');
    }
}
