<?php

namespace App\Http\Controllers;

use App\Models\Transformation;
use App\Models\WasteItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransformationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of transformations.
     */
    public function index(Request $request)
    {
        $query = Transformation::with(['user', 'wasteItem']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter for current user (artisan only)
        if ($request->filled('my_transformations') && Auth::user()->role === 'artisan') {
            $query->where('user_id', Auth::id());
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $transformations = $query->paginate(12);

        // Get statistics
        $stats = [
            'total' => Transformation::count(),
            'in_progress' => Transformation::where('status', 'in_progress')->count(),
            'completed' => Transformation::where('status', 'completed')->count(),
        ];

        return view('transformations.index', compact('transformations', 'stats'));
    }

    /**
     * Show the form for creating a new transformation.
     */
    public function create()
    {
        // Only artisans can create transformations
        if (Auth::user()->role !== 'artisan') {
            abort(403, 'Seuls les artisans peuvent créer des transformations.');
        }

        $wasteItems = WasteItem::where('status', 'available')->get();

        return view('transformations.create', compact('wasteItems'));
    }

    /**
     * Store a newly created transformation.
     */
    public function store(Request $request)
    {
        // Only artisans can create transformations
        if (Auth::user()->role !== 'artisan') {
            abort(403, 'Seuls les artisans peuvent créer des transformations.');
        }

        $validated = $request->validate([
            'waste_item_id' => 'required|exists:waste_items,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'category' => 'required|string|max:100',
            'estimated_time' => 'nullable|integer|min:1',
            'difficulty_level' => 'required|in:easy,medium,hard,expert',
            'materials_needed' => 'nullable|string|max:1000',
            'before_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'process_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'in_progress';

        // Handle before_images upload
        if ($request->hasFile('before_images')) {
            $imagePaths = [];
            foreach ($request->file('before_images') as $image) {
                $path = $this->storeImage($image, 'transformations/before');
                $imagePaths[] = $path;
            }
            $validated['before_images'] = json_encode($imagePaths);
        }

        // Handle process_images upload
        if ($request->hasFile('process_images')) {
            $imagePaths = [];
            foreach ($request->file('process_images') as $image) {
                $path = $this->storeImage($image, 'transformations/process');
                $imagePaths[] = $path;
            }
            $validated['process_images'] = json_encode($imagePaths);
        }

        $transformation = Transformation::create($validated);

        return redirect()->route('transformations.show', $transformation)
            ->with('success', 'Transformation créée avec succès !');
    }

    /**
     * Display the specified transformation.
     */
    public function show(Transformation $transformation)
    {
        $transformation->load(['user', 'wasteItem', 'marketplaceItem']);
        
        // Get related transformations
        $relatedTransformations = Transformation::where('category', $transformation->category)
            ->where('id', '!=', $transformation->id)
            ->where('status', 'completed')
            ->with('user')
            ->latest()
            ->limit(4)
            ->get();

        return view('transformations.show', compact('transformation', 'relatedTransformations'));
    }

    /**
     * Show the form for editing the specified transformation.
     */
    public function edit(Transformation $transformation)
    {
        // Only the owner can edit
        if ($transformation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $wasteItems = WasteItem::where('status', 'available')->get();

        return view('transformations.edit', compact('transformation', 'wasteItems'));
    }

    /**
     * Update the specified transformation.
     */
    public function update(Request $request, Transformation $transformation)
    {
        // Only the owner can update
        if ($transformation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'waste_item_id' => 'required|exists:waste_items,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'category' => 'required|string|max:100',
            'estimated_time' => 'nullable|integer|min:1',
            'difficulty_level' => 'required|in:easy,medium,hard,expert',
            'materials_needed' => 'nullable|string|max:1000',
            'before_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'process_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'after_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle before_images upload
        if ($request->hasFile('before_images')) {
            // Delete old images
            if ($transformation->before_images) {
                $oldImages = json_decode($transformation->before_images, true);
                foreach ($oldImages as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $imagePaths = [];
            foreach ($request->file('before_images') as $image) {
                $path = $this->storeImage($image, 'transformations/before');
                $imagePaths[] = $path;
            }
            $validated['before_images'] = json_encode($imagePaths);
        }

        // Handle process_images upload
        if ($request->hasFile('process_images')) {
            // Delete old images
            if ($transformation->process_images) {
                $oldImages = json_decode($transformation->process_images, true);
                foreach ($oldImages as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $imagePaths = [];
            foreach ($request->file('process_images') as $image) {
                $path = $this->storeImage($image, 'transformations/process');
                $imagePaths[] = $path;
            }
            $validated['process_images'] = json_encode($imagePaths);
        }

        // Handle after_images upload
        if ($request->hasFile('after_images')) {
            // Delete old images
            if ($transformation->after_images) {
                $oldImages = json_decode($transformation->after_images, true);
                foreach ($oldImages as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $imagePaths = [];
            foreach ($request->file('after_images') as $image) {
                $path = $this->storeImage($image, 'transformations/after');
                $imagePaths[] = $path;
            }
            $validated['after_images'] = json_encode($imagePaths);
        }

        $transformation->update($validated);

        return redirect()->route('transformations.show', $transformation)
            ->with('success', 'Transformation mise à jour avec succès !');
    }

    /**
     * Remove the specified transformation.
     */
    public function destroy(Transformation $transformation)
    {
        // Only the owner can delete
        if ($transformation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete all images
        if ($transformation->before_images) {
            $beforeImages = json_decode($transformation->before_images, true);
            foreach ($beforeImages as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        if ($transformation->process_images) {
            $processImages = json_decode($transformation->process_images, true);
            foreach ($processImages as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        if ($transformation->after_images) {
            $afterImages = json_decode($transformation->after_images, true);
            foreach ($afterImages as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $transformation->delete();

        return redirect()->route('transformations.index')
            ->with('success', 'Transformation supprimée avec succès !');
    }

    /**
     * Publish the transformation (mark as completed and ready for marketplace).
     */
    public function publish(Request $request, Transformation $transformation)
    {
        // Only the owner can publish
        if ($transformation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'after_images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle after_images upload
        if ($request->hasFile('after_images')) {
            $imagePaths = [];
            foreach ($request->file('after_images') as $image) {
                $path = $this->storeImage($image, 'transformations/after');
                $imagePaths[] = $path;
            }
            $validated['after_images'] = json_encode($imagePaths);
        }

        $validated['status'] = 'completed';
        $validated['completed_at'] = now();

        $transformation->update($validated);

        return redirect()->route('transformations.show', $transformation)
            ->with('success', 'Transformation publiée avec succès ! Vous pouvez maintenant la vendre sur la marketplace.');
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
}
