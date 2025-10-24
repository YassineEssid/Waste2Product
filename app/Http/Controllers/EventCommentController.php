<?php

namespace App\Http\Controllers;

use App\Models\EventComment;
use App\Models\CommunityEvent;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EventCommentController extends Controller
{
    use AuthorizesRequests;

    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->middleware('auth');
        $this->gamificationService = $gamificationService;
        
    }

    /**
     * Display a listing of comments.
     */
    public function index(Request $request)
    {
        $query = EventComment::with(['user', 'event']);

        // Filter by event
        if ($request->filled('event_id')) {
            $query->forEvent($request->event_id);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->withRating($request->rating);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('comment', 'like', "%{$search}%");
        }

        // Filter by approval status
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->approved();
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        // Sort options
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->orderBy('commented_at', 'asc');
                    break;
                case 'rating_high':
                    $query->orderBy('rating', 'desc');
                    break;
                case 'rating_low':
                    $query->orderBy('rating', 'asc');
                    break;
                default:
                    $query->orderBy('commented_at', 'desc');
            }
        } else {
            $query->orderBy('commented_at', 'desc');
        }

        $comments = $query->paginate(15);

        // Statistics
        $stats = [
            'total' => EventComment::count(),
            'approved' => EventComment::where('is_approved', true)->count(),
            'pending' => EventComment::where('is_approved', false)->count(),
            'average_rating' => round(EventComment::whereNotNull('rating')->avg('rating'), 1),
        ];

        // Get all events for filter dropdown
        $events = CommunityEvent::orderBy('title')->get();

        return view('event-comments.index', compact('comments', 'stats', 'events'));
    }

    /**
     * Show the form for creating a new comment.
     */
    public function create(Request $request)
    {
        $eventId = $request->get('event_id');
        $event = null;

        if ($eventId) {
            $event = CommunityEvent::findOrFail($eventId);
        }

        $events = CommunityEvent::where('starts_at', '>', now())
                                ->orWhere('ends_at', '>', now())
                                ->orderBy('title')
                                ->get();

        return view('event-comments.create', compact('events', 'event'));
    }

    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'community_event_id' => 'required|exists:community_events,id',
            'comment' => 'required|string|min:10|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ], [
            'community_event_id.required' => 'Veuillez sélectionner un événement.',
            'community_event_id.exists' => 'L\'événement sélectionné n\'existe pas.',
            'comment.required' => 'Le commentaire est obligatoire.',
            'comment.min' => 'Le commentaire doit contenir au moins 10 caractères.',
            'comment.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
            'rating.min' => 'La note doit être comprise entre 1 et 5.',
            'rating.max' => 'La note doit être comprise entre 1 et 5.',
        ]);

        $comment = EventComment::create([
            'community_event_id' => $validated['community_event_id'],
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
            'rating' => $validated['rating'] ?? null,
            'is_approved' => true,
            'commented_at' => now(),
        ]);

        // Award points for posting comment
        $this->gamificationService->awardPoints(
            Auth::user(),
            'comment_posted',
            "Comment posted on event: {$comment->event->title}",
            $comment
        );

        // Award points for giving rating
        if ($validated['rating'] ?? null) {
            $this->gamificationService->awardPoints(
                Auth::user(),
                'rating_given',
                "Rating given for event: {$comment->event->title}",
                $comment
            );
        }

        return redirect()
            ->route('event-comments.show', $comment)
            ->with('success', 'Commentaire ajouté avec succès ! Vous avez gagné des points !');
    }

    /**
     * Display the specified comment.
     */
    public function show(EventComment $eventComment)
    {
        $eventComment->load(['user', 'event']);

        // Get other comments from the same event
        $relatedComments = EventComment::forEvent($eventComment->community_event_id)
                                      ->where('id', '!=', $eventComment->id)
                                      ->approved()
                                      ->orderBy('commented_at', 'desc')
                                      ->limit(5)
                                      ->get();

        return view('event-comments.show', compact('eventComment', 'relatedComments'));
    }

    /**
     * Show the form for editing the specified comment.
     */
    public function edit(EventComment $eventComment)
    {
        // Check if user can edit this comment
        if ($eventComment->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce commentaire.');
        }

        $events = CommunityEvent::orderBy('title')->get();

        return view('event-comments.edit', compact('eventComment', 'events'));
    }

    /**
     * Update the specified comment in storage.
     */
    public function update(Request $request, EventComment $eventComment)
    {
        // Check if user can update this comment
        if ($eventComment->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce commentaire.');
        }

        $validated = $request->validate([
            'community_event_id' => 'required|exists:community_events,id',
            'comment' => 'required|string|min:10|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
            'is_approved' => 'nullable|boolean',
        ], [
            'community_event_id.required' => 'Veuillez sélectionner un événement.',
            'community_event_id.exists' => 'L\'événement sélectionné n\'existe pas.',
            'comment.required' => 'Le commentaire est obligatoire.',
            'comment.min' => 'Le commentaire doit contenir au moins 10 caractères.',
            'comment.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
            'rating.min' => 'La note doit être comprise entre 1 et 5.',
            'rating.max' => 'La note doit être comprise entre 1 et 5.',
        ]);

        $eventComment->update([
            'community_event_id' => $validated['community_event_id'],
            'comment' => $validated['comment'],
            'rating' => $validated['rating'] ?? null,
            'is_approved' => $validated['is_approved'] ?? $eventComment->is_approved,
        ]);

        return redirect()
            ->route('event-comments.show', $eventComment)
            ->with('success', 'Commentaire mis à jour avec succès !');
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(EventComment $eventComment)
    {
        // Check if user can delete this comment
        if ($eventComment->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer ce commentaire.');
        }

        $eventComment->delete();

        return redirect()
            ->route('event-comments.index')
            ->with('success', 'Commentaire supprimé avec succès !');
    }

    /**
     * Toggle the approval status of a comment.
     */
    public function toggleApproval(EventComment $eventComment)
    {
        // Only admins can toggle approval
        if (!Auth::user()->is_admin) {
            abort(403, 'Vous n\'êtes pas autorisé à effectuer cette action.');
        }

        $wasApproved = $eventComment->is_approved;

        $eventComment->update([
            'is_approved' => !$eventComment->is_approved,
        ]);

        // Award points when comment gets approved
        if (!$wasApproved && $eventComment->is_approved) {
            $this->gamificationService->awardPoints(
                $eventComment->user,
                'comment_approved',
                "Comment approved on event: {$eventComment->event->title}",
                $eventComment
            );
        }

        $status = $eventComment->is_approved ? 'approuvé' : 'désapprouvé';

        return back()->with('success', "Commentaire {$status} avec succès !");
    }
}
