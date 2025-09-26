@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-2"><i class="fas fa-recycle text-success me-2"></i>Waste Items</h1>
                    <p class="text-muted">Discover items that can be repaired or transformed</p>
                </div>
                @auth
                    <a href="{{ route('waste-items.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Add New Item
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-success">
                        <i class="fas fa-sign-in-alt me-2"></i>Login to Add Items
                    </a>
                @endauth
            </div>

            <!-- Filter Options -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('waste-items.index') }}" class="d-flex">
                        <div class="input-group">
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                        {{ ucfirst($category) }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="{{ route('waste-items.index') }}" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search items..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($wasteItems->count() > 0)
                <div class="row">
                    @foreach($wasteItems as $item)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 shadow-sm border-0">
                                @if($item->images && count($item->images) > 0)
                                    <img src="{{ asset('storage/' . $item->images[0]) }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $item->title }}">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-image text-muted fs-1"></i>
                                    </div>
                                @endif
                                
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0">{{ $item->title }}</h5>
                                        <span class="badge bg-{{ $item->status === 'available' ? 'success' : ($item->status === 'claimed' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-tag me-1"></i>{{ ucfirst($item->category) }}
                                        @if($item->location)
                                            <span class="ms-3"><i class="fas fa-map-marker-alt me-1"></i>{{ $item->location }}</span>
                                        @endif
                                    </p>
                                    
                                    <p class="card-text flex-grow-1">{{ Str::limit($item->description, 100) }}</p>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>{{ $item->user->name }}
                                            </small>
                                            <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                                        </div>
                                        
                                        <div class="d-flex gap-2 mt-3">
                                            <a href="{{ route('waste-items.show', $item) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                                <i class="fas fa-eye me-1"></i>View Details
                                            </a>
                                            @auth
                                                @if($item->user_id === auth()->id() || auth()->user()->role === 'admin')
                                                    <a href="{{ route('waste-items.edit', $item) }}" class="btn btn-outline-secondary btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            @endauth
                                        </div>
                                        
                                        @auth
                                            @if($item->status === 'available' && $item->user_id !== auth()->id())
                                                <button class="btn btn-success btn-sm w-100 mt-2" onclick="claimItem({{ $item->id }})">
                                                    <i class="fas fa-hand-paper me-1"></i>Claim for Repair/Transform
                                                </button>
                                            @endif
                                        @else
                                            @if($item->status === 'available')
                                                <a href="{{ route('login') }}" class="btn btn-outline-success btn-sm w-100 mt-2">
                                                    <i class="fas fa-sign-in-alt me-1"></i>Login to Claim
                                                </a>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $wasteItems->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-recycle text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="text-muted mb-3">No Waste Items Found</h4>
                    <p class="text-muted mb-4">Be the first to share items that can be given new life!</p>
                    <a href="{{ route('waste-items.create') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-plus me-2"></i>Add Your First Item
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Claim Item Modal -->
<div class="modal fade" id="claimModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Claim Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to claim this item for repair or transformation?</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    By claiming this item, you commit to either repairing it or transforming it into something new.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmClaim">Confirm Claim</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .badge {
        font-size: 0.7rem;
    }
</style>
@endpush

@push('scripts')
<script>
let currentItemId = null;

function claimItem(itemId) {
    currentItemId = itemId;
    new bootstrap.Modal(document.getElementById('claimModal')).show();
}

document.getElementById('confirmClaim').addEventListener('click', function() {
    if (currentItemId) {
        // Create a form to submit the claim
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/waste-items/${currentItemId}/claim`;
        
        // Add CSRF token
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfField);
        
        // Add method field for PATCH
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
});
</script>
@endpush
@endsection