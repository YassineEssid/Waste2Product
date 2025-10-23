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

    public function index(Request $request)
    {
        $query = RepairRequest::with(['user', 'wasteItem', 'repairer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('my_requests')) {
            $query->where('user_id', Auth::id());
        }

        if ($request->filled('my_repairs') && Auth::user()->role === 'repairer') {
            $query->where('repairer_id', Auth::id());
        }

        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $repairs = $query->paginate(12);

        $stats = [
            'total' => RepairRequest::count(),
            'waiting' => RepairRequest::where('status', 'waiting')->count(),
            'in_progress' => RepairRequest::where('status', 'in_progress')->count(),
            'completed' => RepairRequest::where('status', 'completed')->count(),
        ];

        return view('repairs.index', compact('repairs', 'stats'));
    }

    public function my(Request $request)
{
    $query = RepairRequest::with(['user', 'wasteItem', 'repairer'])
        ->where(function($q) {
            $q->where('user_id', Auth::id())
              ->orWhere('repairer_id', Auth::id());
        });

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $sortBy = $request->get('sort', 'created_at');
    $sortOrder = $request->get('order', 'desc');
    $query->orderBy($sortBy, $sortOrder);

    $repairs = $query->paginate(12);

    $stats = [
        'total' => RepairRequest::where('user_id', Auth::id())->orWhere('repairer_id', Auth::id())->count(),
        'waiting' => RepairRequest::where(function($q) {
            $q->where('user_id', Auth::id())
              ->orWhere('repairer_id', Auth::id());
        })->where('status', 'waiting')->count(),
        'in_progress' => RepairRequest::where(function($q) {
            $q->where('user_id', Auth::id())
              ->orWhere('repairer_id', Auth::id());
        })->where('status', 'in_progress')->count(),
        'completed' => RepairRequest::where(function($q) {
            $q->where('user_id', Auth::id())
              ->orWhere('repairer_id', Auth::id());
        })->where('status', 'completed')->count(),
    ];

    return view('repairs.index', compact('repairs', 'stats'));
}

    public function create()
    {
        $wasteItems = WasteItem::where('user_id', Auth::id())
                              ->where('status', 'available')
                              ->get();

        return view('repairs.create', compact('wasteItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'waste_item_id' => 'required|exists:waste_items,id',
            'description' => 'required|string|max:1000',
            'preferred_date' => 'nullable|date|after:today',
            'urgency' => 'required|in:low,medium,high',
            'budget' => 'nullable|numeric|min:0',
            'before_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'waiting'; // Default valid status

        // Handle before_images
        if ($request->hasFile('before_images')) {
            $imagePaths = [];
            foreach ($request->file('before_images') as $image) {
                $imagePaths[] = $this->storeImage($image, 'repairs/before');
            }
            $validated['before_images'] = json_encode($imagePaths);
        }

        RepairRequest::create($validated);

        return redirect()->route('repairs.index')
            ->with('success', 'Demande de réparation créée avec succès !');
    }

    public function show(RepairRequest $repair)
    {
        $repair->load(['user', 'wasteItem', 'repairer']);
        return view('repairs.show', compact('repair'));
    }

    public function edit(RepairRequest $repair)
    {
        if ($repair->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $wasteItems = WasteItem::where('user_id', Auth::id())
                              ->where('status', 'available')
                              ->get();

        return view('repairs.edit', compact('repair', 'wasteItems'));
    }

    public function update(Request $request, RepairRequest $repair)
    {
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

        if ($request->hasFile('before_images')) {
            if ($repair->before_images) {
                foreach (json_decode($repair->before_images, true) as $img) {
                    Storage::disk('public')->delete($img);
                }
            }

            $imagePaths = [];
            foreach ($request->file('before_images') as $image) {
                $imagePaths[] = $this->storeImage($image, 'repairs/before');
            }
            $validated['before_images'] = json_encode($imagePaths);
        }

        $repair->update($validated);

        return redirect()->route('repairs.show', $repair)
            ->with('success', 'Demande de réparation mise à jour avec succès !');
    }

    public function destroy(RepairRequest $repair)
    {
        if ($repair->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($repair->before_images) {
            foreach (json_decode($repair->before_images, true) as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        if ($repair->after_images) {
            foreach (json_decode($repair->after_images, true) as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        $repair->delete();

        return redirect()->route('repairs.index')
            ->with('success', 'Demande de réparation supprimée avec succès !');
    }

    public function assign(Request $request, RepairRequest $repair)
    {
        if (Auth::user()->role !== 'repairer') {
            abort(403, 'Seuls les réparateurs peuvent accepter des demandes.');
        }

        if ($repair->status !== 'waiting') {
            return redirect()->back()
                ->with('error', 'Cette demande a déjà été prise en charge.');
        }

        $repair->update([
            'repairer_id' => Auth::id(),
            'status' => 'in_progress'
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Vous avez accepté cette demande de réparation !');
    }

    public function start(Request $request, RepairRequest $repair)
    {
        if ($repair->repairer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($repair->status !== 'assigned') {
            return redirect()->back()
                ->with('error', 'Cette réparation ne peut pas être démarrée.');
        }

        $repair->update(['status' => 'in_progress']);

        return redirect()->route('repairs.show', $repair)
            ->with('success', 'Réparation démarrée !');
    }

    public function complete(Request $request, RepairRequest $repair)
    {
        if ($repair->repairer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'actual_cost' => 'required|numeric|min:0',
            'repairer_notes' => 'nullable|string|max:1000',
            'after_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('after_images')) {
            $imagePaths = [];
            foreach ($request->file('after_images') as $image) {
                $imagePaths[] = $this->storeImage($image, 'repairs/after');
            }
            $validated['after_images'] = json_encode($imagePaths);
        }

        $validated['status'] = 'completed';
        $validated['completed_at'] = now();

        $repair->update($validated);

        return redirect()->route('repairs.show', $repair)
            ->with('success', 'Réparation terminée avec succès !');
    }

    private function storeImage($image, $folder)
    {
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
        Storage::disk('public')->putFileAs($folder, $image, $filename);
        return $folder . '/' . $filename;
    }
}
