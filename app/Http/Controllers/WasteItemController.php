<?php

namespace App\Http\Controllers;

use App\Models\Category;
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
        $categories = Category::all();

return view('waste-items.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'You must be logged in to add a waste item.');
    }

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'category_id' => 'required|exists:categories,id',
        'quantity' => 'required|integer|min:1',
        'condition' => 'required|in:poor,fair,good,excellent',
        'location_address' => 'nullable|string',
        'location_lat' => 'nullable|numeric|between:-90,90',
        'location_lng' => 'nullable|numeric|between:-180,180',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $wasteItem = new WasteItem();
    $wasteItem->title = $validated['title'];
    $wasteItem->description = $validated['description'];
    $wasteItem->quantity = $validated['quantity'];
    $wasteItem->condition = $validated['condition'];
    $wasteItem->location_address = $validated['location_address'] ?? null;
    $wasteItem->location_lat = $validated['location_lat'] ?? null;
    $wasteItem->location_lng = $validated['location_lng'] ?? null;
    $wasteItem->user_id = Auth::id(); // sûr maintenant
    $wasteItem->is_available = $request->has('is_available') ? 1 : 0;

    if ($request->hasFile('images')) {
        $imagePaths = [];
        foreach ($request->file('images') as $image) {
            $imagePaths[] = $this->storeImage($image, 'waste-items');
        }
        $wasteItem->images = $imagePaths;
    }

    $category = Category::findOrFail($validated['category_id']);
    $wasteItem->category()->associate($category);

    $wasteItem->save();

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
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . config('services.gemini.api_key'),
                [
                    'contents' => [
                        [
                            'parts' => [[
                                'text' => $question .
                                    "\n\nRespond ONLY in pure JSON format, with either:\n" .
                                    "1️⃣ A single object: { \"sale\": \"...\", \"donate\": \"...\", \"craft\": \"...\" }\n" .
                                    "or\n" .
                                    "2️⃣ An array of objects if multiple items are described: [ { \"sale\": \"...\", \"donate\": \"...\", \"craft\": \"...\" }, ... ]"
                            ]]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]
            );

            $result = $response->json();
            $output = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$output) {
                return response()->json(['error' => 'No valid response from Gemini'], 500);
            }

            // Decode JSON output safely
            $parsed = json_decode($output, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'error' => 'Invalid JSON format from Gemini',
                    'raw' => $output,
                ], 500);
            }

            // Normalize structure
            if (isset($parsed['sale']) && isset($parsed['donate']) && isset($parsed['craft'])) {
                // Single item
                return response()->json($parsed);
            } elseif (isset($parsed[0])) {
                // Multiple items (array)
                return response()->json(['items' => $parsed]);
            } else {
                return response()->json([
                    'error' => 'Unexpected format from Gemini',
                    'raw' => $parsed
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get recommendation',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}