<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $conversations = Conversation::where('buyer_id', $user->id)
                                     ->orWhere('seller_id', $user->id)
                                     ->with(['buyer', 'seller', 'item.images'])
                                     ->latest()
                                     ->get();

        return view('messages.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $conversation->load(['messages.sender', 'item.images']);

        // Mark messages as read (only messages from the other person)
        $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('messages.show', compact('conversation'));
    }

    public function store(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $request->validate([
            'message' => 'required|string',
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Load sender relationship for broadcasting
        $message->load('sender');

        // Update conversation's last_message_at
        $conversation->update(['last_message_at' => now()]);

        // Broadcast the message in real-time
        broadcast(new \App\Events\NewMessage($message))->toOthers();

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return back()->with('success', 'Message sent successfully!');
    }

    /**
     * Poll for new messages (AJAX polling alternative to WebSocket)
     */
    public function poll(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $sinceId = $request->get('since', 0);

        $newMessages = $conversation->messages()
            ->where('id', '>', $sinceId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender->name,
                    'message' => $message->message,
                    'created_at' => $message->created_at->format('h:i A'),
                ];
            });

        return response()->json([
            'messages' => $newMessages
        ]);
    }

    /**
     * Get count of unread messages for the authenticated user
     */
    public function unreadCount()
    {
        $user = Auth::user();

        // Count unread messages where the user is NOT the sender
        $unreadCount = \App\Models\Message::whereHas('conversation', function ($query) use ($user) {
                $query->where('buyer_id', $user->id)
                      ->orWhere('seller_id', $user->id);
            })
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Get recent conversations for dropdown preview
     */
    public function recentConversations()
    {
        $user = Auth::user();

        $conversations = Conversation::where(function($query) use ($user) {
                $query->where('buyer_id', $user->id)
                      ->orWhere('seller_id', $user->id);
            })
            ->with(['buyer', 'seller', 'item', 'messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->withCount(['messages as unread_count' => function($query) use ($user) {
                $query->where('sender_id', '!=', $user->id)
                      ->where('is_read', false);
            }])
            ->latest('last_message_at')
            ->limit(5)
            ->get()
            ->map(function($conversation) use ($user) {
                $otherUser = $conversation->buyer_id === $user->id
                    ? $conversation->seller
                    : $conversation->buyer;

                $lastMessage = $conversation->messages->first();

                return [
                    'id' => $conversation->id,
                    'other_user_name' => $otherUser->name,
                    'other_user_avatar' => $otherUser->avatar ? Storage::url($otherUser->avatar) : null,
                    'item_title' => $conversation->item->title ?? 'Item',
                    'last_message' => $lastMessage ? Str::limit($lastMessage->message, 50) : 'Aucun message',
                    'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : '',
                    'unread_count' => $conversation->unread_count,
                    'url' => route('messages.show', $conversation->id)
                ];
            });

        return response()->json([
            'conversations' => $conversations
        ]);
    }
}
