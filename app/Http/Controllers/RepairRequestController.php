<?php

namespace App\Http\Controllers;

use App\Models\RepairRequest;
use App\Models\WasteItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RepairRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of repair requests.
     */
    public function index(Request $request)
    {
        $query = RepairRequest::with(['user', 'wasteItem', 'repairer']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter for current user
        if ($request->filled('my_requests')) {
            $query->where('user_id', Auth::id());
        }

        // Filter for repairer
        if ($request->filled('my_repairs') && Auth::user()->role === 'repairer') {
            $query->where('repairer_id', Auth::id());
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $repairs = $query->paginate(12);

        // Get statistics
        $stats = [
            'total' => RepairRequest::count(),
            'pending' => RepairRequest::where('status', 'pending')->count(),
            'in_progress' => RepairRequest::where('status', 'in_progress')->count(),
            'completed' => RepairRequest::where('status', 'completed')->count(),
        ];

        return view('repairs.index', compact('repairs', 'stats'));
    }

    /**
     * Show the form for creating a new repair request.
     */
    public function create()
    {
        $wasteItems = WasteItem::where('user_id', Auth::id())
                              ->where('status', 'available')
                              ->get();

        return view('repairs.create', compact('wasteItems'));
    }

    /**
     * Store a newly created repair request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'waste_item_id' => 'required|exists:waste_items,id',
            'description' => 'required|string|max:1000',
            'preferred_date' => 'nullable|date|after:today',
            'urgency' => 'required|in:low,medium,high',
            'budget' => 'nullable|numeric|min:0',
            'before_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        // Handle before_images upload
        if ($request->hasFile('before_images')) {
            $imagePaths = [];
            foreach ($request->file('before_images') as $image) {
                $path = $this->storeImage($image, 'repairs/before');
                $imagePaths[] = $path;
            }
            $validated['before_images'] = json_encode($imagePaths);
        }

        $repair = RepairRequest::create($validated);

        return redirect()->route('repairs.show', $repair)
            ->with('success', 'Demande de réparation créée avec succès !');
    }

    /**
     * Display the specified repair request.
     */
    public function show(RepairRequest $repair)
    {
        $repair->load(['user', 'wasteItem', 'repairer']);

        return view('repairs.show', compact('repair'));
    }

    /**
     * Show the form for editing the specified repair request.
     */
    public function edit(RepairRequest $repair)
    {
        // Only the owner can edit
        if ($repair->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $wasteItems = WasteItem::where('user_id', Auth::id())
                              ->where('status', 'available')
                              ->get();

        return view('repairs.edit', compact('repair', 'wasteItems'));
    }

    /**
     * Update the specified repair request.
     */
    public function update(Request $request, RepairRequest $repair)
    {
        // Only the owner can update
        if ($repair->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'waste_item_id' => 'required|exists:waste_items,id',
            'description' => 'required|string|max:1000',
            'preferred_date' => 'nullable|date|after:today',
            'urgency' => 'required|in:low,medium,high',
            'budget' => 'nullable|numeric|min:0',
            'before_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle new before_images upload
        if ($request->hasFile('before_images')) {
            // Delete old images
            if ($repair->before_images) {
                foreach ($repair->before_images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $imagePaths = [];
            foreach ($request->file('before_images') as $image) {
                $path = $this->storeImage($image, 'repairs/before');
                $imagePaths[] = $path;
            }
            $validated['before_images'] = $imagePaths;
        }

        $repair->update($validated);

        return redirect()->route('repairs.show', $repair)
            ->with('success', 'Demande de réparation mise à jour avec succès !');
    }

    /**
     * Remove the specified repair request.
     */
    public function destroy(RepairRequest $repair)
    {
        // Only the owner can delete
        if ($repair->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete images
        if ($repair->before_images) {
            foreach ($repair->before_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        if ($repair->after_images) {
            foreach ($repair->after_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $repair->delete();

        return redirect()->route('repairs.index')
            ->with('success', 'Demande de réparation supprimée avec succès !');
    }

    /**
     * Assign a repairer to the repair request.
     */
    public function assign(Request $request, RepairRequest $repair)
    {
        // Only repairers can assign themselves
        if (Auth::user()->role !== 'repairer') {
            abort(403, 'Seuls les réparateurs peuvent accepter des demandes.');
        }

        if ($repair->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Cette demande a déjà été prise en charge.');
        }

        $repair->update([
            'repairer_id' => Auth::id(),
            'status' => 'accepted'
        ]);

        return redirect()->route('repairs.show', $repair)
            ->with('success', 'Vous avez accepté cette demande de réparation !');
    }

    /**
     * Start the repair.
     */
    public function start(Request $request, RepairRequest $repair)
    {
        // Only assigned repairer can start
        if ($repair->repairer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($repair->status !== 'accepted') {
            return redirect()->back()
                ->with('error', 'Cette réparation ne peut pas être démarrée.');
        }

        $repair->update(['status' => 'in_progress']);

        return redirect()->route('repairs.show', $repair)
            ->with('success', 'Réparation démarrée !');
    }

    /**
     * Complete the repair with after images.
     */
    public function complete(Request $request, RepairRequest $repair)
    {
        // Only assigned repairer can complete
        if ($repair->repairer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'cost' => 'required|numeric|min:0',
            'completion_notes' => 'nullable|string|max:1000',
            'after_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle after_images upload
        if ($request->hasFile('after_images')) {
            $imagePaths = [];
            foreach ($request->file('after_images') as $image) {
                $path = $this->storeImage($image, 'repairs/after');
                $imagePaths[] = $path;
            }
            $validated['after_images'] = json_encode($imagePaths);
        }

        $validated['status'] = 'completed';
        $validated['completed_at'] = now();

        $repair->update($validated);

        return redirect()->route('repairs.show', $repair)
            ->with('success', 'Réparation terminée avec succès !');
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
