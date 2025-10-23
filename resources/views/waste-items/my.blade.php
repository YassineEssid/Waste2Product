@extends('layouts.app')

@section('title', 'My Items - Waste2Product')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-user me-2"></i>My Waste Items</h2>
                <p class="text-muted">Manage your listed items and track their impact</p>
            </div>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="fas fa-plus me-2"></i>Add New Item
            </button>
        </div>
    </div>
</div>

<!-- Stats Row -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-list stats-icon"></i>
                <h4 class="mt-2">{{ $wasteItems->total() }}</h4>
                <p class="mb-0">Total Items</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card" style="background: linear-gradient(45deg, #28a745, #20c997); color: white;">
            <div class="card-body text-center">
                <i class="fas fa-eye stats-icon"></i>
                <h4 class="mt-2">{{ $wasteItems->sum('views') ?? 0 }}</h4>
                <p class="mb-0">Total Views</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card" style="background: linear-gradient(45deg, #17a2b8, #6f42c1); color: white;">
            <div class="card-body text-center">
                <i class="fas fa-handshake stats-icon"></i>
                <h4 class="mt-2">{{ $wasteItems->where('status', 'claimed')->count() }}</h4>
                <p class="mb-0">Claimed Items</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card" style="background: linear-gradient(45deg, #ffc107, #fd7e14); color: white;">
            <div class="card-body text-center">
                <i class="fas fa-leaf stats-icon"></i>
                <h4 class="mt-2">{{ number_format($wasteItems->sum('estimated_value'), 0) }} kg</h4>
                <p class="mb-0">CO₂ Saved</p>
            </div>
        </div>
    </div>
</div>

@if($wasteItems->count() > 0)
<!-- Items Grid -->
<div class="row">
    @foreach($wasteItems as $item)
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
            @if($item->images && count($item->images) > 0)
    <div class="position-relative">
        <img src="{{ asset('storage/' . $item->images[0]) }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $item->title }}">
        <span class="position-absolute top-0 end-0 badge bg-{{ $item->status === 'available' ? 'success' : ($item->status === 'claimed' ? 'warning' : 'secondary') }} m-2">
            {{ ucfirst($item->status) }}
        </span>
    </div>
@else
    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
        <i class="fas fa-image text-muted fa-3x"></i>
    </div>
@endif
            
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">{{ $item->title }}</h5>
                <p class="card-text text-muted flex-grow-1">{{ Str::limit($item->description, 100) }}</p>
                
                <div class="mb-3">
                    <small class="text-muted">
                        <i class="fas fa-tag me-1"></i>{{ ucfirst($item->category) }} • 
                        <i class="fas fa-balance-scale me-1"></i>{{ $item->weight }}kg • 
                        <i class="fas fa-eye me-1"></i>{{ $item->views ?? 0 }} views
                    </small>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>Posted {{ $item->created_at->diffForHumans() }}
                    </small>
                </div>
                
                <div class="btn-group-vertical w-100">
                    <a href="{{ route('waste-items.show', $item) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye me-1"></i>View Details
                    </a>
                    <a href="{{ route('waste-items.edit', $item) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    @if($item->status === 'available')
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="markAsClaimed({{ $item->id }})">
                            <i class="fas fa-handshake me-1"></i>Mark as Claimed
                        </button>
                    @endif
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteItem({{ $item->id }})">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $wasteItems->links() }}
</div>

@else
<!-- Empty State -->
<div class="text-center py-5">
    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
    <h4>No Items Yet</h4>
    <p class="text-muted mb-4">You haven't listed any items yet. Start contributing to the circular economy!</p>
    <a href="{{ route('waste-items.create') }}" class="btn btn-success btn-lg">
        <i class="fas fa-plus me-2"></i>List Your First Item
    </a>
</div>
@endif

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this item? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Item</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Claimed Modal -->
<div class="modal fade" id="claimedModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark as Claimed</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Mark this item as claimed? This will indicate that someone has picked it up.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="claimedForm" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-warning">Mark as Claimed</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('waste-items.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Add New Waste Item
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Item Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="furniture">Furniture</option>
                            <option value="electronics">Electronics</option>
                            <option value="clothing">Clothing</option>
                            <option value="books">Books</option>
                            <option value="toys">Toys</option>
                            <option value="household">Household Items</option>
                            <option value="garden">Garden Items</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="row">
                        <!-- Weight -->
                        <div class="col-md-6 mb-3">
                            <label for="weight" class="form-label">Weight (kg)</label>
                            <input type="number" class="form-control" id="weight" name="weight" step="0.1" min="0">
                        </div>

                        <!-- Condition -->
                        <div class="col-md-6 mb-3">
                            <label for="condition" class="form-label">Condition</label>
                            <select class="form-select" id="condition" name="condition" required>
                                <option value="excellent">Excellent</option>
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                                <option value="broken">Broken</option>
                            </select>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" placeholder="City, Area">
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Item Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="text-muted">Optional: Upload a photo of your item</small>
                    </div>

                    <!-- Additional Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any special instructions or additional information..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Add Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteItem(itemId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/waste-items/${itemId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function markAsClaimed(itemId) {
    const claimedForm = document.getElementById('claimedForm');
    claimedForm.action = `/waste-items/${itemId}/claim`;
    new bootstrap.Modal(document.getElementById('claimedModal')).show();
}
</script>
@endpush