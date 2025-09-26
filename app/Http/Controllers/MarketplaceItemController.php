<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MarketplaceItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of marketplace items.
     */
    public function index(Request $request)
    {
        $query = MarketplaceItem::with('seller', 'images')->where('is_available', true);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
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
            'total_items' => MarketplaceItem::where('is_available', true)->count(),
            'total_sellers' => MarketplaceItem::distinct('seller_id')->count(),
            'avg_price' => MarketplaceItem::where('is_available', true)->avg('price'),
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'condition' => 'required|in:excellent,good,fair,needs_repair',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $item = new MarketplaceItem($request->all());
        $item->seller_id = Auth::id();
        $item->save();

        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('marketplace', 'public');
                $item->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('marketplace.index')->with('success', 'Item listed successfully!');
    }

    /**
     * Display the specified marketplace item.
     */
    public function show(MarketplaceItem $marketplaceItem)
    {
        $marketplaceItem->load('seller', 'images');
        
        // Get related items
        $relatedItems = MarketplaceItem::where('category', $marketplaceItem->category)
                                    ->where('id', '!=', $marketplaceItem->id)
                                    ->where('is_available', true)
                                    ->limit(4)
                                    ->get();

        return view('marketplace.show', compact('marketplaceItem', 'relatedItems'));
    }

    /**
     * Show the form for editing the specified marketplace item.
     */
    public function edit(MarketplaceItem $marketplaceItem)
    {
        $this->authorize('update', $marketplaceItem);
        
        $categories = [
            'furniture', 'electronics', 'clothing', 'books', 
            'toys', 'tools', 'decorative', 'appliances', 'other'
        ];
        $conditions = ['excellent', 'good', 'fair', 'needs_repair'];
        
        $marketplaceItem->load('images');
        
        return view('marketplace.edit', compact('marketplaceItem', 'categories', 'conditions'));
    }

    /**
     * Update the specified marketplace item in storage.
     */
    public function update(Request $request, MarketplaceItem $marketplaceItem)
    {
        $this->authorize('update', $marketplaceItem);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'condition' => 'required|in:excellent,good,fair,needs_repair',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'is_available' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $marketplaceItem->update($request->all());

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('marketplace', 'public');
                $marketplaceItem->images()->create(['path' => $path]);
            }
        }

        // Handle image deletions
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = $marketplaceItem->images()->find($imageId);
                if ($image) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }
        }

        return redirect()->route('marketplace.show', $marketplaceItem)->with('success', 'Item updated successfully!');
    }

    /**
     * Remove the specified marketplace item from storage.
     */
    public function destroy(MarketplaceItem $marketplaceItem)
    {
        $this->authorize('delete', $marketplaceItem);

        // Delete all images
        foreach ($marketplaceItem->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $marketplaceItem->delete();

        return redirect()->route('marketplace.index')->with('success', 'Item deleted successfully!');
    }

    /**
     * Get items by category.
     */
    public function byCategory($category)
    {
        $items = MarketplaceItem::with('seller', 'images')
                               ->where('category', $category)
                               ->where('is_available', true)
                               ->paginate(12);

        return view('marketplace.category', compact('items', 'category'));
    }
}