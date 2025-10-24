@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <!-- Image Gallery -->
            <div class="card mb-4">
                @if($wasteItem->images && count($wasteItem->images) > 0)
                    <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($wasteItem->images as $index => $image)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/' . $image) }}" class="d-block w-100" style="height: 400px; object-fit: cover;" alt="Item image {{ $index + 1 }}">
                                </div>
                            @endforeach
                        </div>
                        @if(count($wasteItem->images) > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        @endif
                    </div>
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 400px;">
                        <i class="fas fa-image text-muted" style="font-size: 4rem;"></i>
                    </div>
                @endif
            </div>

            <!-- Item Details -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="h3 mb-2">{{ $wasteItem->title }}</h1>
                            <div class="d-flex gap-3 text-muted">
                                <span><i class="fas fa-tag me-1"></i>{{ $wasteItem->categoryModel?->name ?? ucfirst($wasteItem->category) }}</span>
                                @if($wasteItem->location)
                                    <span><i class="fas fa-map-marker-alt me-1"></i>{{ $wasteItem->location }}</span>
                                @endif
                                <span><i class="fas fa-clock me-1"></i>{{ $wasteItem->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <span class="badge bg-{{ $wasteItem->status === 'available' ? 'success' : ($wasteItem->status === 'claimed' ? 'warning' : 'secondary') }} fs-6">
                            {{ ucfirst($wasteItem->status) }}
                        </span>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            @if($wasteItem->condition)
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Condition</h6>
                                    <span class="badge bg-{{ $wasteItem->condition === 'excellent' ? 'success' : ($wasteItem->condition === 'good' ? 'primary' : ($wasteItem->condition === 'fair' ? 'warning' : 'danger')) }}">
                                        {{ ucfirst($wasteItem->condition) }}
                                    </span>
                                </div>
                            @endif
                            
                            @if($wasteItem->dimensions)
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Dimensions</h6>
                                    <p class="mb-0">{{ $wasteItem->dimensions }}</p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-6">
                            @if($wasteItem->weight)
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Weight</h6>
                                    <p class="mb-0">{{ $wasteItem->weight }}</p>
                                </div>
                            @endif
                            
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Available for Claiming</h6>
                                <span class="badge bg-{{ $wasteItem->is_available ? 'success' : 'secondary' }}">
                                    {{ $wasteItem->is_available ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Description</h6>
                        <p class="lead">{{ $wasteItem->description }}</p>
                    </div>

                    @if($wasteItem->latitude && $wasteItem->longitude)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Location on Map</h6>
                            <div id="map" style="height: 300px;" class="rounded"></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Owner Info -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title">{{ $wasteItem->user->name }}</h5>
                    <p class="text-muted mb-3">
                        <i class="fas fa-user-tag me-1"></i>{{ ucfirst($wasteItem->user->role) }}
                        @if($wasteItem->user->address)
                            <br><small><i class="fas fa-map-marker-alt me-1"></i>{{ $wasteItem->user->address }}</small>
                        @endif
                    </p>
                    
                    @if(auth()->id() !== $wasteItem->user_id)
                        <div class="d-grid gap-2">
                            @if($wasteItem->status === 'available' && $wasteItem->is_available)
                                <button class="btn btn-success" onclick="claimItem({{ $wasteItem->id }})">
                                    <i class="fas fa-hand-paper me-2"></i>Claim This Item
                                </button>
                            @else
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-times me-2"></i>Not Available
                                </button>
                            @endif
                            
                            <button class="btn btn-outline-primary" onclick="contactOwner()">
                                <i class="fas fa-envelope me-2"></i>Contact Owner
                            </button>
                        </div>
                    @else
                        <div class="d-grid gap-2">
                            <a href="{{ route('waste-items.edit', $wasteItem) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>Edit Item
                            </a>
                            <button class="btn btn-outline-danger" onclick="deleteItem({{ $wasteItem->id }})">
                                <i class="fas fa-trash me-2"></i>Delete Item
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons for Admins -->
            @if(auth()->user()->role === 'admin')
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Admin Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-warning btn-sm" onclick="toggleAvailability({{ $wasteItem->id }})">
                                <i class="fas fa-toggle-{{ $wasteItem->is_available ? 'on' : 'off' }} me-2"></i>
                                {{ $wasteItem->is_available ? 'Hide' : 'Show' }} Item
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteItem({{ $wasteItem->id }})">
                                <i class="fas fa-trash me-2"></i>Delete Item
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Related Items -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-tags me-2"></i>Related Items</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($relatedItems as $related)
                            <a href="{{ route('waste-items.show', $related) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ Str::limit($related->title, 40) }}</h6>
                                        <small class="text-muted">{{ $related->user->name }}</small>
                                    </div>
                                    <small class="text-muted">{{ $related->created_at->diffForHumans() }}</small>
                                </div>
                            </a>
                        @empty
                            <div class="list-group-item text-muted text-center py-3">
                                <i class="fas fa-info-circle me-2"></i>No related items found
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Back to List Button -->
<div class="container mt-4 mb-5">
    <a href="{{ route('waste-items.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to All Items
    </a>
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
                <p>Are you sure you want to claim "<strong>{{ $wasteItem->title }}</strong>" for repair or transformation?</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    By claiming this item, you commit to either repairing it or transforming it into something new. The owner will be notified of your claim.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmClaim">Confirm Claim</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function claimItem(itemId) {
    new bootstrap.Modal(document.getElementById('claimModal')).show();
}

document.getElementById('confirmClaim')?.addEventListener('click', function() {
    // Create a form to submit the claim
      const modal = new bootstrap.Modal(document.getElementById('claimModal'));
    modal.show();

    document.getElementById('confirmClaim')?.addEventListener('click', function() {
        // Create the form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/waste-items/${itemId}/claim`;

        // CSRF token
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfField);

        // Method PATCH
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        form.appendChild(methodField);

        // Submit the form
        document.body.appendChild(form);
        form.submit(); // No .catch() here because form.submit() is synchronous

        // Hide modal
        modal.hide();
    }, { once: true }); // Prevent multiple event listeners
});

function contactOwner() {
    // This would typically open a contact modal or redirect to messaging
    alert('Contact functionality will be implemented in the messaging system.');
}

function deleteItem(itemId) {
    if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/waste-items/${itemId}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(methodInput);
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function toggleAvailability(itemId) {
    // Create a form to toggle availability
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/waste-items/${itemId}/toggle-availability`;

    // CSRF token
    const csrfField = document.createElement('input');
    csrfField.type = 'hidden';
    csrfField.name = '_token';
    csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfField);

    // PATCH method
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'PATCH';
    form.appendChild(methodField);

    document.body.appendChild(form);
    form.submit(); // Synchronous, no .then() / .catch()
}

// Initialize map if coordinates are available
@if($wasteItem->latitude && $wasteItem->longitude)
function initMap() {
    const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: { lat: {{ $wasteItem->latitude }}, lng: {{ $wasteItem->longitude }} }
    });
    
    new google.maps.Marker({
        position: { lat: {{ $wasteItem->latitude }}, lng: {{ $wasteItem->longitude }} },
        map: map,
        title: '{{ $wasteItem->title }}'
    });
}
@endif
</script>

@if($wasteItem->latitude && $wasteItem->longitude)
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap"></script>
@endif
@endpush
@endsection