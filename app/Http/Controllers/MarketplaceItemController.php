<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceItem;
use App\Models\User;
use App\Models\Conversation;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MarketplaceItemController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->middleware('auth');
        $this->gamificationService = $gamificationService;
    }

    /**
     * Display a listing of marketplace items.
     */
    public function index(Request $request)
    {
        $query = MarketplaceItem::with('seller', 'images')->where('status', 'available');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by condition
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $items = $query->paginate(12);

        // Get categories for filter
        $categories = MarketplaceItem::distinct()->pluck('category')->filter();
        $conditions = ['excellent', 'good', 'fair', 'needs_repair'];

        // Get statistics
        $stats = [
            'total_items' => MarketplaceItem::where('status', 'available')->count(),
            'total_sellers' => MarketplaceItem::distinct('seller_id')->count(),
            'avg_price' => MarketplaceItem::where('status', 'available')->avg('price'),
        ];

        return view('marketplace.index', compact('items', 'categories', 'conditions', 'stats'));
    }

    /**
     * Show the form for creating a new marketplace item.
     */
    public function create()
    {
        $categories = [
            'furniture', 'electronics', 'clothing', 'books',
            'toys', 'tools', 'decorative', 'appliances', 'other'
        ];
        $conditions = ['excellent', 'good', 'fair', 'needs_repair'];

        return view('marketplace.create', compact('categories', 'conditions'));
    }

    /**
     * Store a newly created marketplace item in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:20|max:5000',
            'category' => 'required|string|in:furniture,electronics,clothing,books,toys,tools,decorative,appliances,other',
            'condition' => 'required|in:excellent,good,fair,needs_repair',
            'price' => 'required|numeric|min:0.01|max:999999.99',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'title.required' => 'Title is required.',
            'title.min' => 'Title must be at least 5 characters.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'description.required' => 'Description is required.',
            'description.min' => 'Description must be at least 20 characters.',
            'description.max' => 'Description cannot exceed 5000 characters.',
            'category.required' => 'Category is required.',
            'category.in' => 'Selected category is not valid.',
            'condition.required' => 'Condition is required.',
            'condition.in' => 'Selected condition is not valid.',
            'price.required' => 'Price is required.',
            'price.min' => 'Price must be greater than 0.',
            'price.max' => 'Price cannot exceed 999999.99 DT.',
            'price.numeric' => 'Price must be a valid number.',
            'images.*.image' => 'File must be an image.',
            'images.*.mimes' => 'Image must be in jpeg, png, jpg or gif format.',
            'images.*.max' => 'Image size cannot exceed 2 MB.',
        ]);

        // Additional conditional validation
        if ($request->price <= 0) {
            return back()->withErrors(['price' => 'Price must be greater than zero.'])->withInput();
        }

        if (strlen(trim($request->title)) < 5) {
            return back()->withErrors(['title' => 'Title must contain at least 5 meaningful characters.'])->withInput();
        }

        if (strlen(trim($request->description)) < 20) {
            return back()->withErrors(['description' => 'Description must contain at least 20 meaningful characters.'])->withInput();
        }

        $data = [
            'title' => $request->title, // Colonne title qui est requise
            'name' => $request->title,  // Colonne name aussi
            'description' => $request->description,
            'category' => $request->category,
            'condition' => $request->condition,
            'price' => $request->price,
            'seller_id' => Auth::id(),
            'status' => 'available',
            'quantity' => 1,
        ];

        $item = MarketplaceItem::create($data);

        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('marketplace', 'public');
                $item->images()->create(['image_path' => $path]);
            }
        }

        // Award points for listing item
        $this->gamificationService->awardPoints(
            Auth::user(),
            'waste_item_posted',
            'Listed item on marketplace: ' . $item->title,
            $item
        );

        return redirect()->route('marketplace.index')->with('success', 'Item listed successfully!');
    }

    /**
     * Display the specified marketplace item.
     */
    public function show($id)
    {
        $marketplaceItem = MarketplaceItem::with('seller', 'images')->findOrFail($id);

        // Get related items
        $relatedItems = MarketplaceItem::where('category', $marketplaceItem->category)
                        ->where('id', '!=', $marketplaceItem->id)
                        ->where('status', 'available')
                        ->limit(4)
                        ->get();

        return view('marketplace.show', compact('marketplaceItem', 'relatedItems'));
    }

    /**
     * Show the form for editing the specified marketplace item.
     */
    public function edit(MarketplaceItem $marketplace)
    {
        $categories = [
            'furniture', 'electronics', 'clothing', 'books',
            'toys', 'tools', 'decorative', 'appliances', 'other'
        ];
        $conditions = ['excellent', 'good', 'fair', 'needs_repair'];

        $marketplace->load('images');

        return view('marketplace.edit', compact('marketplace', 'categories', 'conditions'));
    }

    /**
     * Update the specified marketplace item in storage.
     */
    public function update(Request $request, MarketplaceItem $marketplace)
    {
        $this->authorize('update', $marketplace);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'condition' => 'required|in:excellent,good,fair,needs_repair',
            'price' => 'required|numeric|min:0',
            'status' => 'string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'title' => $request->title,
            'name' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'condition' => $request->condition,
            'price' => $request->price,
            'status' => $request->status ?? $marketplace->status,
        ];

        $marketplace->update($updateData);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('marketplace', 'public');
                $marketplace->images()->create(['image_path' => $path]);
            }
        }

        // Handle image deletions
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = $marketplace->images()->find($imageId);
                if ($image) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }
        }

        return redirect()->route('marketplace.show', ['marketplace' => $marketplace->id])->with('success', 'Item updated successfully!');
    }

    /**
     * Remove the specified marketplace item from storage.
     */
    public function destroy(MarketplaceItem $marketplace)
    {
        // Delete all images
        if ($marketplace->images) {
            foreach ($marketplace->images as $image) {
                Storage::disk('public')->delete($image->path);
            }
        }

        $marketplace->delete();

        return redirect()->route('marketplace.index')->with('success', 'Item deleted successfully!');
    }

    /**
     * Get items by category.
     */
    public function byCategory($category)
    {
        $items = MarketplaceItem::with('seller', 'images')
                       ->where('category', $category)
                       ->where('status', 'available')
                       ->paginate(12);

        return view('marketplace.category', compact('items', 'category'));
    }

    /**
     * Toggle item status (available/sold)
     */
    public function toggleStatus(MarketplaceItem $marketplace)
    {
        $this->authorize('update', $marketplace);

        $marketplace->update([
            'status' => $marketplace->status === 'available' ? 'sold' : 'available'
        ]);

        return back()->with('success', 'Status updated successfully!');
    }

    /**
     * Search marketplace items (AJAX)
     */
    public function search(Request $request)
    {
        $query = MarketplaceItem::with(['seller', 'images'])
            ->where('status', 'available');

        // Search in title and description
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by condition
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $items = $query->paginate(12);

        return response()->json([
            'success' => true,
            'items' => $items,
            'total' => $items->total(),
            'count' => $items->count()
        ]);
    }


    /**
     * Start a new conversation or return an existing one.
     */
    public function startConversation(Request $request, MarketplaceItem $item)
    {
        $buyer = Auth::user();
        $seller = $item->seller;

        // Prevent user from starting a conversation with themselves
        if ($buyer->id === $seller->id) {
            return back()->with('error', 'You cannot start a conversation with yourself.');
        }

        // Check if a conversation already exists
        $conversation = Conversation::where('marketplace_item_id', $item->id)
            ->where('buyer_id', $buyer->id)
            ->where('seller_id', $seller->id)
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'marketplace_item_id' => $item->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $seller->id,
            ]);
        }

        return redirect()->route('messages.show', $conversation->id);
    }

    /**
     * Detect category and suggest metadata using AI
     */
    public function detectCategory(Request $request)
    {
        $request->validate([
            'description' => 'required|string|min:5|max:500'
        ]);

        $categoryService = app(\App\Services\CategoryDetectionService::class);
        $result = $categoryService->detectCategory($request->description);

        return response()->json($result);
    }

    /**
     * Suggest optimal price using AI
     */
    public function suggestPrice(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:3|max:200',
            'description' => 'required|string|min:10|max:1000',
            'category' => 'required|string',
            'condition' => 'required|string',
            'keywords' => 'nullable|array'
        ]);

        $priceService = app(\App\Services\PriceSuggestionService::class);
        $result = $priceService->suggestPrice(
            $request->title,
            $request->description,
            $request->category,
            $request->condition,
            $request->keywords ?? []
        );

        return response()->json($result);
    }
}
