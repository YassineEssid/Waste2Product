<?php

namespace App\Http\Controllers;

use App\Models\Transformation;
use App\Models\WasteItem;
use App\Models\MarketplaceItem;
use App\Services\TransformationIdeasService;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransformationController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->middleware('auth');
        $this->gamificationService = $gamificationService;
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
        if ($request->has('my') && Auth::user()->role === 'artisan') {
            $query->where('user_id', Auth::id());
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_title', 'like', "%{$search}%")
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
            'planned' => Transformation::where('status', 'planned')->count(),
            'in_progress' => Transformation::where('status', 'in_progress')->count(),
            'completed' => Transformation::where('status', 'completed')->count(),
            'published' => Transformation::where('status', 'published')->count(),
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
            'product_title' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:20|max:5000',
            'price' => 'nullable|numeric|min:0.01|max:999999.99',
            'time_spent_hours' => 'nullable|integer|min:1|max:1000',
            'materials_cost' => 'nullable|numeric|min:0|max:999999.99',
            'co2_saved' => 'nullable|numeric|min:0|max:9999',
            'waste_reduced' => 'nullable|numeric|min:0|max:9999',
            'before_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'after_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'process_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:planned,in_progress,completed'
        ], [
            'waste_item_id.required' => 'You must select a waste item.',
            'waste_item_id.exists' => 'Selected waste item does not exist.',
            'product_title.required' => 'Product title is required.',
            'product_title.min' => 'Title must be at least 5 characters.',
            'product_title.max' => 'Title cannot exceed 255 characters.',
            'description.required' => 'Description is required.',
            'description.min' => 'Description must be at least 20 characters.',
            'description.max' => 'Description cannot exceed 5000 characters.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price must be greater than 0.',
            'price.max' => 'Price cannot exceed 999999.99 DT.',
            'time_spent_hours.integer' => 'Time spent must be an integer.',
            'time_spent_hours.min' => 'Time spent must be at least 1 hour.',
            'time_spent_hours.max' => 'Time spent cannot exceed 1000 hours.',
            'materials_cost.numeric' => 'Materials cost must be a valid number.',
            'materials_cost.max' => 'Materials cost cannot exceed 999999.99 DT.',
            'co2_saved.numeric' => 'CO2 saved must be a valid number.',
            'co2_saved.max' => 'CO2 saved cannot exceed 9999 kg.',
            'waste_reduced.numeric' => 'Waste reduced must be a valid number.',
            'waste_reduced.max' => 'Waste reduced cannot exceed 9999 kg.',
            'status.required' => 'Status is required.',
            'status.in' => 'Selected status is not valid.',
            'before_images.*.image' => 'File must be an image.',
            'before_images.*.mimes' => 'Image must be in jpeg, png, jpg or gif format.',
            'before_images.*.max' => 'Image size cannot exceed 2 MB.',
            'after_images.*.image' => 'File must be an image.',
            'after_images.*.mimes' => 'Image must be in jpeg, png, jpg or gif format.',
            'after_images.*.max' => 'Image size cannot exceed 2 MB.',
            'process_images.*.image' => 'File must be an image.',
            'process_images.*.mimes' => 'Image must be in jpeg, png, jpg or gif format.',
            'process_images.*.max' => 'Image size cannot exceed 2 MB.',
        ]);

        // Additional conditional validations
        if (strlen(trim($request->product_title)) < 5) {
            return back()->withErrors([
                'product_title' => 'Title must contain at least 5 meaningful characters.'
            ])->withInput();
        }

        if (strlen(trim($request->description)) < 20) {
            return back()->withErrors([
                'description' => 'Description must contain at least 20 meaningful characters.'
            ])->withInput();
        }

        // Check that price is consistent with materials cost
        if ($request->filled('price') && $request->filled('materials_cost')) {
            if ($request->price < $request->materials_cost) {
                return back()->withErrors([
                    'price' => 'Sale price cannot be lower than materials cost.'
                ])->withInput();
            }
        }

        // Check that environmental values are consistent
        if ($request->filled('waste_reduced') && $request->waste_reduced < 0) {
            return back()->withErrors([
                'waste_reduced' => 'Waste reduced cannot be negative.'
            ])->withInput();
        }

        if ($request->filled('co2_saved') && $request->co2_saved < 0) {
            return back()->withErrors([
                'co2_saved' => 'CO2 saved cannot be negative.'
            ])->withInput();
        }

        // Prepare data
        $data = [
            'user_id' => Auth::id(),
            'waste_item_id' => $validated['waste_item_id'],
            'title' => $validated['product_title'],
            'description' => $validated['description'],
            'process_description' => $validated['description'],
            'price' => $validated['price'] ?? null,
            'time_spent_hours' => $validated['time_spent_hours'] ?? null,
            'materials_cost' => $validated['materials_cost'] ?? 0,
            'co2_saved' => $validated['co2_saved'] ?? 0,
            'waste_reduced' => $validated['waste_reduced'] ?? 0,
            'status' => $validated['status']
        ];

        // Handle image uploads
        if ($request->hasFile('before_images')) {
            $beforePaths = [];
            foreach ($request->file('before_images') as $image) {
                $beforePaths[] = $image->store('transformations/before', 'public');
            }
            $data['before_images'] = $beforePaths;
        }

        if ($request->hasFile('after_images')) {
            $afterPaths = [];
            foreach ($request->file('after_images') as $image) {
                $afterPaths[] = $image->store('transformations/after', 'public');
            }
            $data['after_images'] = $afterPaths;
        }

        if ($request->hasFile('process_images')) {
            $processPaths = [];
            foreach ($request->file('process_images') as $image) {
                $processPaths[] = $image->store('transformations/process', 'public');
            }
            $data['process_images'] = $processPaths;
        }

        $transformation = Transformation::create($data);

        // Update waste item status
        WasteItem::find($validated['waste_item_id'])->update(['status' => 'transformed']);

        // Award points based on status
        if ($transformation->status === 'completed') {
            $this->gamificationService->awardPoints(
                Auth::user(),
                'transformation_completed',
                'Completed transformation: ' . $transformation->title,
                $transformation
            );
        }

        return redirect()->route('transformations.show', $transformation)
            ->with('success', 'Transformation created successfully!');
    }

    /**
     * Display the specified transformation.
     */
    public function show(Transformation $transformation)
    {
        $transformation->load(['user', 'wasteItem']);
        $transformation->increment('views_count');

        return view('transformations.show', compact('transformation'));
    }

    /**
     * Show the form for editing the specified transformation.
     */
    public function edit(Transformation $transformation)
    {
        // Check authorization
        if ($transformation->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette transformation.');
        }

        $wasteItems = WasteItem::all();

        return view('transformations.edit', compact('transformation', 'wasteItems'));
    }

    /**
     * Update the specified transformation.
     */
    public function update(Request $request, Transformation $transformation)
    {
        // Check authorization
        if ($transformation->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette transformation.');
        }

        $validated = $request->validate([
            'waste_item_id' => 'required|exists:waste_items,id',
            'product_title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'nullable|numeric|min:0',
            'time_spent_hours' => 'nullable|integer|min:0',
            'materials_cost' => 'nullable|numeric|min:0',
            'co2_saved' => 'nullable|numeric|min:0',
            'waste_reduced' => 'nullable|numeric|min:0',
            'before_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'after_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'process_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:planned,in_progress,completed,published',
            'remove_before_images' => 'nullable|array',
            'remove_after_images' => 'nullable|array',
            'remove_process_images' => 'nullable|array',
        ]);

        $data = [
            'waste_item_id' => $validated['waste_item_id'],
            'title' => $validated['product_title'],
            'description' => $validated['description'],
            'process_description' => $validated['description'],
            'price' => $validated['price'] ?? null,
            'time_spent_hours' => $validated['time_spent_hours'] ?? null,
            'materials_cost' => $validated['materials_cost'] ?? 0,
            'co2_saved' => $validated['co2_saved'] ?? 0,
            'waste_reduced' => $validated['waste_reduced'] ?? 0,
            'status' => $validated['status']
        ];

        // Handle image removal
        if ($request->has('remove_before_images')) {
            $beforeImages = $transformation->before_images ?? [];
            foreach ($request->remove_before_images as $index) {
                if (isset($beforeImages[$index])) {
                    Storage::disk('public')->delete($beforeImages[$index]);
                    unset($beforeImages[$index]);
                }
            }
            $data['before_images'] = array_values($beforeImages);
        }

        if ($request->has('remove_after_images')) {
            $afterImages = $transformation->after_images ?? [];
            foreach ($request->remove_after_images as $index) {
                if (isset($afterImages[$index])) {
                    Storage::disk('public')->delete($afterImages[$index]);
                    unset($afterImages[$index]);
                }
            }
            $data['after_images'] = array_values($afterImages);
        }

        if ($request->has('remove_process_images')) {
            $processImages = $transformation->process_images ?? [];
            foreach ($request->remove_process_images as $index) {
                if (isset($processImages[$index])) {
                    Storage::disk('public')->delete($processImages[$index]);
                    unset($processImages[$index]);
                }
            }
            $data['process_images'] = array_values($processImages);
        }

        // Handle new image uploads
        if ($request->hasFile('before_images')) {
            $beforePaths = [];
            foreach ($request->file('before_images') as $image) {
                $beforePaths[] = $image->store('transformations/before', 'public');
            }
            $data['before_images'] = array_merge($data['before_images'] ?? $transformation->before_images ?? [], $beforePaths);
        }

        if ($request->hasFile('after_images')) {
            $afterPaths = [];
            foreach ($request->file('after_images') as $image) {
                $afterPaths[] = $image->store('transformations/after', 'public');
            }
            $data['after_images'] = array_merge($data['after_images'] ?? $transformation->after_images ?? [], $afterPaths);
        }

        if ($request->hasFile('process_images')) {
            $processPaths = [];
            foreach ($request->file('process_images') as $image) {
                $processPaths[] = $image->store('transformations/process', 'public');
            }
            $data['process_images'] = array_merge($data['process_images'] ?? $transformation->process_images ?? [], $processPaths);
        }

        // Check if status changed to published
        $wasPublished = $transformation->status === 'published';
        $transformation->update($data);

        // If status changed to published, create marketplace item
        if ($validated['status'] === 'published' && !$wasPublished) {
            $this->publishToMarketplace($transformation);
        }

        return redirect()->route('transformations.show', $transformation)
            ->with('success', 'Transformation mise à jour avec succès !');
    }

    /**
     * Remove the specified transformation.
     */
    public function destroy(Transformation $transformation)
    {
        // Check authorization
        if ($transformation->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cette transformation.');
        }

        // Delete images
        if ($transformation->before_images) {
            foreach ($transformation->before_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        if ($transformation->after_images) {
            foreach ($transformation->after_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        if ($transformation->process_images) {
            foreach ($transformation->process_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $transformation->delete();

        return redirect()->route('transformations.index')
            ->with('success', 'Transformation supprimée avec succès !');
    }

    /**
     * Publish transformation to marketplace.
     */
    public function publish(Transformation $transformation)
    {
        // Check authorization
        if ($transformation->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Vous n\'êtes pas autorisé à publier cette transformation.');
        }

        if ($transformation->status !== 'completed') {
            return back()->with('error', 'Seules les transformations terminées peuvent être publiées.');
        }

        $transformation->update(['status' => 'published']);
        $this->publishToMarketplace($transformation);

        return back()->with('success', 'Transformation publiée sur le marketplace avec succès !');
    }

    /**
     * Create marketplace item from transformation.
     */
    protected function publishToMarketplace(Transformation $transformation)
    {
        // Check if already published
        $existingItem = MarketplaceItem::where('seller_id', $transformation->user_id)
            ->where('name', $transformation->title)
            ->where('description', $transformation->description)
            ->first();

        if ($existingItem) {
            return;
        }

        $marketplaceData = [
            'seller_id' => $transformation->user_id,
            'name' => $transformation->title,
            'title' => $transformation->title,
            'description' => $transformation->description,
            'price' => $transformation->price ?? 0,
            'category' => 'recycled',
            'condition' => 'new',
            'quantity' => 1,
            'status' => 'available',
        ];

        $marketplaceItem = MarketplaceItem::create($marketplaceData);

        // Add images
        if ($transformation->after_images) {
            foreach ($transformation->after_images as $imagePath) {
                $marketplaceItem->images()->create([
                    'image_path' => $imagePath
                ]);
            }
        }
    }

    /**
     * Generate transformation ideas using AI
     */
    public function generateIdeas(Request $request, TransformationIdeasService $ideasService)
    {
        $request->validate([
            'waste_item_id' => 'required|exists:waste_items,id',
            'count' => 'nullable|integer|min:3|max:10'
        ]);

        $wasteItem = WasteItem::findOrFail($request->waste_item_id);
        $count = $request->input('count', 5);

        $result = $ideasService->generateIdeas($wasteItem, $count);

        return response()->json($result);
    }
}
