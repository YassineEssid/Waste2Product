<?php

namespace App\Http\Controllers;

use App\Models\WasteItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class WasteItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = WasteItem::with('user')->available();

        // Search functionality
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Location filter
        if ($request->filled('lat') && $request->filled('lng')) {
            $radius = $request->input('radius', 10);
            $query->nearLocation($request->lat, $request->lng, $radius);
        }

        $wasteItems = $query->orderBy('created_at', 'desc')->paginate(12);

        $categories = WasteItem::distinct('category')
            ->whereNotNull('category')
            ->pluck('category')
            ->sort();

        return view('waste-items.index', compact('wasteItems', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        return view('waste-items.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:poor,fair,good,excellent',
            'location_address' => 'nullable|string',
            'location_lat' => 'nullable|numeric|between:-90,90',
            'location_lng' => 'nullable|numeric|between:-180,180',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $validated['user_id'] = Auth::id();

        // Handle image uploads
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $this->storeImage($image, 'waste-items');
                $imagePaths[] = $path;
            }
            $validated['images'] = $imagePaths;
        }

        $wasteItem = WasteItem::create($validated);

        return redirect()->route('waste-items.show', $wasteItem)
            ->with('success', 'Waste item listed successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(WasteItem $wasteItem)
    {
        $wasteItem->load('user', 'repairRequests.repairer', 'transformations.user');
        
        // Get related items from same category
        $relatedItems = WasteItem::where('category', $wasteItem->category)
            ->where('id', '!=', $wasteItem->id)
            ->where('status', 'available')
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();
        
        return view('waste-items.show', compact('wasteItem', 'relatedItems'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WasteItem $wasteItem)
    {
         
        return view('waste-items.edit', compact('wasteItem'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WasteItem $wasteItem)
    {
 
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:poor,fair,good,excellent',
            'location_address' => 'nullable|string',
            'location_lat' => 'nullable|numeric|between:-90,90',
            'location_lng' => 'nullable|numeric|between:-180,180',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            // Delete old images
            if ($wasteItem->images) {
                foreach ($wasteItem->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $this->storeImage($image, 'waste-items');
                $imagePaths[] = $path;
            }
            $validated['images'] = $imagePaths;
        }

        $wasteItem->update($validated);

        return redirect()->route('waste-items.show', $wasteItem)
            ->with('success', 'Waste item updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WasteItem $wasteItem)
    {
 
        // Delete images
        if ($wasteItem->images) {
            foreach ($wasteItem->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $wasteItem->delete();

        return redirect()->route('waste-items.index')
            ->with('success', 'Waste item deleted successfully!');
    }

    /**
     * Show user's own waste items
     */
    public function my()
    {
        $wasteItems = Auth::user()
            ->wasteItems()
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('waste-items.my', compact('wasteItems'));
    }

    /**
     * Mark item as claimed
     */
    public function claim(WasteItem $wasteItem)
    {
        // Only the owner can mark their item as claimed
        if ($wasteItem->user_id !== Auth::id()) {
            return redirect()->back()
                ->with('error', 'You can only mark your own items as claimed.');
        }

        $wasteItem->update(['status' => 'claimed']);

        return redirect()->back()
            ->with('success', 'Item marked as claimed successfully!');
    }

    /**
     * Toggle item availability
     */
    public function toggleAvailability(WasteItem $wasteItem)
    {
        // Only the owner can toggle their item's availability
        if ($wasteItem->user_id !== Auth::id()) {
            return redirect()->back()
                ->with('error', 'You can only modify your own items.');
        }

        $newStatus = $wasteItem->status === 'available' ? 'unavailable' : 'available';
        $wasteItem->update(['status' => $newStatus]);

        return redirect()->back()
            ->with('success', 'Item status updated successfully!');
    }

    /**
     * Store image with optimization
     */
    private function storeImage($image, $folder)
    {
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
        $path = $folder . '/' . $filename;

        // Store the image
        Storage::disk('public')->putFileAs($folder, $image, $filename);

        return $path;
    }

    
public function getRecommendation(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
        ]);

        $question = $request->input('question');

        try {
            // Call Gemini Flash 2.5 API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.gemini.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent', [
                'prompt' => [
                    'text' => $question
                ],
                'temperature' => 0.7,
                'candidate_count' => 1,
                'max_output_tokens' => 500
            ]);

            // Return API response as JSON
            return $response->json();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get recommendation',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
