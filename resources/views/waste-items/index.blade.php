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
                                <div class="form-check m-2">
    <input class="form-check-input waste-checkbox" type="checkbox" value="{{ $item->id }}" id="waste-{{ $item->id }}">
    <label class="form-check-label" for="waste-{{ $item->id }}">
        Select this waste
    </label>
</div>

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
@auth
<div class="d-flex justify-content-end mt-3">
    <button class="btn btn-primary" id="getRecommendations" disabled>
        <i class="fas fa-lightbulb me-1"></i> Get Recommendations
    </button>
</div>
@endauth
<!-- Recommendation Results Zone -->
<div class="container mt-5" id="recommendationZone" style="display: none;">
    <div class="card shadow-sm border-success">
        <div class="card-header bg-success text-white d-flex align-items-center">
            <i class="fas fa-lightbulb me-2"></i>
            <h5 class="mb-0">AI Recommendations</h5>
        </div>
        <div class="card-body" id="recommendationContent">
            <p class="text-muted mb-0">Loading recommendations...</p>
        </div>
    </div>
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
const checkboxes = document.querySelectorAll('.waste-checkbox');
const getRecommendationsBtn = document.getElementById('getRecommendations');

checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', () => {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        getRecommendationsBtn.disabled = !anyChecked;
    });
});

document.getElementById('getRecommendations').addEventListener('click', async function() {
  const selected = Array.from(checkboxes)
                          .filter(cb => cb.checked)
                          .map(cb => cb.value);

    if (selected.length === 0) return; // safety check

    // For each selected waste item, send a request to Gemini Flash API
        const products = selected.map(itemId => {
        const card = document.querySelector(`#waste-${itemId}`).closest('.card');
        const itemTitle = card.querySelector('.card-title').innerText;
        const itemDescription = card.querySelector('.card-text').innerText;
        return `- ${itemTitle}: ${itemDescription}`;
    });
    const zone = document.getElementById('recommendationZone');
    const content = document.getElementById('recommendationContent');
    zone.style.display = 'block';
    content.innerHTML = `<div class="text-center py-3">
        <div class="spinner-border text-success" role="status"></div>
        <p class="text-muted mt-2 mb-0">Generating recommendations...</p>
    </div>`;

    const question = `For the following waste items, which products can be created? Provide 3 plans (sale, donate, craft) for each item:\n${products.join('\n')}`;

        try {
            const response = await fetch('/waste-items/gemini-flash', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ question })
            });

            const data = await response.json();
 let html = '';

        if (data.items && Array.isArray(data.items)) {
            html += `<div class="accordion" id="recommendationAccordion">`;
            data.items.forEach((item, i) => {
                html += `
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading${i}">
                        <button class="accordion-button ${i > 0 ? 'collapsed' : ''}" 
                                type="button" data-bs-toggle="collapse" 
                                data-bs-target="#collapse${i}" aria-expanded="${i === 0}" 
                                aria-controls="collapse${i}">
                            🗑️ Item ${i + 1}
                        </button>
                    </h2>
                    <div id="collapse${i}" class="accordion-collapse collapse ${i === 0 ? 'show' : ''}" 
                         aria-labelledby="heading${i}" data-bs-parent="#recommendationAccordion">
                        <div class="accordion-body">
                            <p><strong>💰 Sale:</strong> ${item.sale}</p>
                            <p><strong>🎁 Donate:</strong> ${item.donate}</p>
                            <p><strong>🎨 Craft:</strong> ${item.craft}</p>
                        </div>
                    </div>
                </div>`;
            });
            html += `</div>`;
        } 
        else if (data.sale && data.donate && data.craft) {
            html = `
                <p><strong>💰 Sale:</strong> ${data.sale}</p>
                <p><strong>🎁 Donate:</strong> ${data.donate}</p>
                <p><strong>🎨 Craft:</strong> ${data.craft}</p>
            `;
        } 
        else if (data.error) {
            html = `<div class="alert alert-danger">${data.error}</div>`;
        } 
        else {
            html = `<div class="alert alert-warning">Unexpected response format from Gemini.</div>`;
        }

        // Display final results
        content.innerHTML = html;

        } catch (error) {
            console.error('Error fetching recommendations:', error);
        }
    
});

</script>
@endpush
@endsection