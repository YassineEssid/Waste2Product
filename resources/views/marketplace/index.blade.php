@extends('layouts.app')

@section('title', 'Marketplace')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="hero-section bg-gradient-success text-white py-5 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-store me-3"></i>Eco Marketplace
                    </h1>
                    <p class="lead mb-4">Discover unique upcycled products, sustainable crafts, and eco-friendly items from our community of makers.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('marketplace.create') }}" class="btn btn-lg btn-list-item shadow-sm px-4 py-2 d-flex align-items-center">
                            <span class="icon-circle bg-white text-success d-flex align-items-center justify-content-center me-2" style="width:2.5rem;height:2.5rem;border-radius:50%;">
                                <i class="fas fa-plus"></i>
                            </span>
                            <span class="fw-bold">List Item</span>
                        </a>

                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="hero-stats">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="stat-card">
                                    <h3 class="h4 mb-1">{{ number_format($stats['total_items']) }}</h3>
                                    <small>Items Available</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <h3 class="h4 mb-1">{{ number_format($stats['total_sellers']) }}</h3>
                                    <small>Active Sellers</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="stat-card">
                                    <h3 class="h4 mb-1">${{ number_format($stats['avg_price'], 0) }}</h3>
                                    <small>Average Price</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .btn-list-item, .btn-filters {
            border: none;
            border-radius: 1.5rem;
            transition: box-shadow 0.2s, transform 0.2s;
            font-size: 1.15rem;
            pointer-events: auto !important;
            z-index: 10;
        }
        .btn-list-item:hover, .btn-filters:hover {
            box-shadow: 0 0 0 0.2rem rgba(40,167,69,0.15);
            transform: translateY(-2px) scale(1.03);
        }
        .icon-circle {
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
    </style>
    <div class="container">
        <!-- Search Bar -->
        <div class="row mb-4">
            <div class="col-12">
                <form method="GET" class="search-form">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text"
                               class="form-control border-start-0 border-end-0"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search for eco-friendly products, crafts, upcycled items...">
                        <button class="btn btn-success" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Category Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="category-filters d-flex flex-wrap gap-2">
                    <a href="{{ route('marketplace.index') }}"
                       class="btn btn-outline-success {{ !request()->hasAny(['category', 'condition']) ? 'active' : '' }}">
                        <i class="fas fa-globe me-1"></i>All Items
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ route('marketplace.index', ['category' => $category]) }}"
                           class="btn btn-outline-success {{ request('category') == $category ? 'active' : '' }}">
                            @switch($category)
                                @case('furniture')
                                    <i class="fas fa-chair me-1"></i>Furniture
                                    @break
                                @case('electronics')
                                    <i class="fas fa-laptop me-1"></i>Electronics
                                    @break
                                @case('clothing')
                                    <i class="fas fa-tshirt me-1"></i>Clothing
                                    @break
                                @case('books')
                                    <i class="fas fa-book me-1"></i>Books
                                    @break
                                @case('toys')
                                    <i class="fas fa-puzzle-piece me-1"></i>Toys
                                    @break
                                @case('tools')
                                    <i class="fas fa-hammer me-1"></i>Tools
                                    @break
                                @case('decorative')
                                    <i class="fas fa-palette me-1"></i>Decorative
                                    @break
                                @case('appliances')
                                    <i class="fas fa-blender me-1"></i>Appliances
                                    @break
                                @default
                                    <i class="fas fa-box me-1"></i>{{ ucfirst($category) }}
                            @endswitch
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sort Options -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="results-count">
                        <p class="text-muted mb-0">
                            Showing {{ $items->count() }} of {{ $items->total() }} items
                            @if(request('search'))
                                for "<strong>{{ request('search') }}</strong>"
                            @endif
                        </p>
                    </div>
                    <div class="sort-options">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-sort me-2"></i>Sort By
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_direction' => 'desc']) }}">
                                    <i class="fas fa-clock me-2"></i>Newest First
                                </a></li>
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'price', 'sort_direction' => 'asc']) }}">
                                    <i class="fas fa-dollar-sign me-2"></i>Price: Low to High
                                </a></li>
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'price', 'sort_direction' => 'desc']) }}">
                                    <i class="fas fa-dollar-sign me-2"></i>Price: High to Low
                                </a></li>
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'title', 'sort_direction' => 'asc']) }}">
                                    <i class="fas fa-sort-alpha-down me-2"></i>A to Z
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($items->count() > 0)
            <!-- Items Grid -->
            <div class="row">
                @foreach($items as $item)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="marketplace-card h-100">
                            <!-- Item Image -->
                            <div class="item-image">
                                @if(optional($item->images)->count() > 0)
                                    <div id="carousel{{ $item->id }}" class="carousel slide item-carousel">
                                        <div class="carousel-inner">
                                            @foreach($item->images as $index => $image)
                                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                    <img src="{{ Storage::url($image->path) }}" alt="{{ $item->title }}" class="d-block w-100">
                                                </div>
                                            @endforeach
                                        </div>
                                        @if($item->images->count() > 1)
                                            <button class="carousel-control-prev" type="button" data-bs-target="#carousel{{ $item->id }}" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon"></span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#carousel{{ $item->id }}" data-bs-slide="next">
                                                <span class="carousel-control-next-icon"></span>
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <div class="placeholder-image d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif

                                <!-- Price Badge -->
                                <div class="price-badge">
                                    <span class="badge bg-success">
                                        ${{ number_format($item->price, 0) }}
                                    </span>
                                </div>

                                <!-- Condition Badge -->
                                <div class="condition-badge">
                                    <span class="badge {{ $item->condition === 'excellent' ? 'bg-success' : ($item->condition === 'good' ? 'bg-primary' : ($item->condition === 'fair' ? 'bg-warning' : 'bg-danger')) }}">
                                        @switch($item->condition)
                                            @case('excellent')
                                                <i class="fas fa-star me-1"></i>Excellent
                                                @break
                                            @case('good')
                                                <i class="fas fa-thumbs-up me-1"></i>Good
                                                @break
                                            @case('fair')
                                                <i class="fas fa-hand-paper me-1"></i>Fair
                                                @break
                                            @case('needs_repair')
                                                <i class="fas fa-tools me-1"></i>Needs Repair
                                                @break
                                        @endswitch
                                    </span>
                                </div>
                            </div>

                            <!-- Item Content -->
                            <div class="item-content">
                                <div class="item-meta mb-2">
                                    <small class="text-muted d-flex align-items-center mb-1">
                                        <i class="fas fa-tag me-2"></i>
                                        {{ ucfirst($item->category) }}
                                    </small>
                                    <small class="text-muted d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        {{ $item->location }}
                                    </small>
                                </div>

                                <h5 class="item-title">
                                    <a href="{{ route('marketplace.show', $item) }}" class="text-decoration-none">
                                        {{ $item->title }}
                                    </a>
                                </h5>

                                <p class="item-description text-muted">
                                    {{ Str::limit($item->description, 80) }}
                                </p>

                                <!-- Seller Info -->
                                <div class="seller-info d-flex align-items-center mb-3">
                                    <div class="seller-avatar me-2">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="seller-details">
                                        <small class="text-muted">
                                            Sold by <strong>{{ $item->seller->name }}</strong>
                                        </small>
                                        @if($item->seller->role !== 'user')
                                            <div>
                                                <span class="badge bg-light text-dark">
                                                    {{ ucfirst($item->seller->role) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="item-actions d-flex gap-2">
                                    <a href="{{ route('marketplace.show', $item) }}" class="btn btn-success btn-sm flex-grow-1">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                    @if($item->seller_id !== auth()->id())
                                        <button class="btn btn-outline-primary btn-sm" onclick="contactSeller({{ $item->id }})">
                                            <i class="fas fa-envelope me-1"></i>Contact
                                        </button>
                                    @else
                                        <a href="{{ route('marketplace.edit', $item) }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                        <form method="POST" action="{{ route('marketplace.destroy', $item) }}" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm ms-1">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        {{ $items->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state text-center py-5">
                <div class="empty-icon mb-4">
                    <i class="fas fa-shopping-bag fa-4x text-muted"></i>
                </div>
                <h3 class="h4 mb-3">No Items Found</h3>
                <p class="text-muted mb-4">
                    @if(request()->hasAny(['search', 'category', 'condition']))
                        No items match your current filters. Try adjusting your search criteria.
                    @else
                        No items are currently available in the marketplace. Be the first to list something!
                    @endif
                </p>
                <div class="d-flex justify-content-center gap-3">
                    @if(request()->hasAny(['search', 'category', 'condition']))
                        <a href="{{ route('marketplace.index') }}" class="btn btn-outline-success">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                    @endif
                    <a href="{{ route('marketplace.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>List First Item
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="GET">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-filter me-2"></i>Filter Items
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                        {{ ucfirst($category) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Condition</label>
                            <select name="condition" class="form-select">
                                <option value="">All Conditions</option>
                                @foreach($conditions as $condition)
                                    <option value="{{ $condition }}" {{ request('condition') == $condition ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $condition)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Min Price</label>
                            <input type="number" name="min_price" class="form-control" value="{{ request('min_price') }}" min="0" step="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max Price</label>
                            <input type="number" name="max_price" class="form-control" value="{{ request('max_price') }}" min="0" step="1">
                        </div>
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('marketplace.index') }}" class="btn btn-outline-secondary">Clear All</a>
                    <button type="submit" class="btn btn-success">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.hero-section {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-radius: 0 0 30px 30px;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><pattern id="grain" width="100" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" fill-opacity="0.1"/></pattern></defs><rect width="100" height="20" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.stat-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 1rem;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.marketplace-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.marketplace-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.item-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.item-carousel .carousel-item img,
.placeholder-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.price-badge {
    position: absolute;
    top: 15px;
    left: 15px;
}

.condition-badge {
    position: absolute;
    top: 15px;
    right: 15px;
}

.item-content {
    padding: 1.5rem;
}

.item-title a {
    color: #2c3e50;
    transition: color 0.3s ease;
}

.item-title a:hover {
    color: #28a745;
}

.seller-avatar {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
}

.category-filters .btn {
    border-radius: 25px;
    transition: all 0.3s ease;
}

.category-filters .btn.active {
    transform: scale(1.05);
}

.search-form .input-group {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.empty-state {
    background: linear-gradient(135deg, #f8f9ff 0%, #e8f0ff 100%);
    border-radius: 20px;
    margin: 2rem 0;
}

.empty-icon {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.carousel-control-prev,
.carousel-control-next {
    width: 15%;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.marketplace-card:hover .carousel-control-prev,
.marketplace-card:hover .carousel-control-next {
    opacity: 1;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    background-color: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    width: 30px;
    height: 30px;
}

@media (max-width: 768px) {
    .hero-section {
        border-radius: 0 0 20px 20px;
    }

    .category-filters {
        justify-content: center;
    }

    .marketplace-card {
        border-radius: 15px;
    }

    .sort-options {
        margin-top: 1rem;
    }
}
</style>

<script>
function contactSeller(itemId) {
    // This could open a modal or redirect to a contact form
    alert('Contact seller feature would be implemented here. Item ID: ' + itemId);
}

// Initialize carousel hover effects
document.addEventListener('DOMContentLoaded', function() {
    const carousels = document.querySelectorAll('.carousel');
    carousels.forEach(carousel => {
        const card = carousel.closest('.marketplace-card');
        let isPlaying = false;

        card.addEventListener('mouseenter', () => {
            if (!isPlaying) {
                const carouselInstance = new bootstrap.Carousel(carousel, {
                    interval: 3000,
                    pause: false
                });
                isPlaying = true;
            }
        });

        card.addEventListener('mouseleave', () => {
            const carouselInstance = bootstrap.Carousel.getInstance(carousel);
            if (carouselInstance) {
                carouselInstance.dispose();
                isPlaying = false;
            }
        });
    });
});

<!-- Create Marketplace Item Modal -->
<div class="modal fade" id="createItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('marketplace.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>List New Item
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Item Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Item Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>

                    <div class="row">
                        <!-- Category -->
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="furniture">Furniture</option>
                                <option value="electronics">Electronics</option>
                                <option value="decorative">Decorative</option>
                                <option value="clothing">Clothing</option>
                                <option value="art">Art & Crafts</option>
                                <option value="garden">Garden Items</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Price -->
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Condition -->
                        <div class="col-md-6 mb-3">
                            <label for="condition" class="form-label">Condition</label>
                            <select class="form-select" id="condition" name="condition" required>
                                <option value="new">New</option>
                                <option value="like_new">Like New</option>
                                <option value="excellent">Excellent</option>
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                            </select>
                        </div>

                        <!-- Is Negotiable -->
                        <div class="col-md-6 mb-3">
                            <label for="is_negotiable" class="form-label">Price Negotiable?</label>
                            <select class="form-select" id="is_negotiable" name="is_negotiable">
                                <option value="0">Fixed Price</option>
                                <option value="1">Negotiable</option>
                            </select>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>

                    <!-- Materials Used -->
                    <div class="mb-3">
                        <label for="materials_used" class="form-label">Materials Used</label>
                        <input type="text" class="form-control" id="materials_used" name="materials_used" placeholder="e.g., Recycled wood, plastic bottles, etc.">
                    </div>

                    <!-- Item Images -->
                    <div class="mb-3">
                        <label for="images" class="form-label">Item Images</label>
                        <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
                        <small class="text-muted">You can upload multiple images (max 5)</small>
                    </div>

                    <!-- Additional Notes -->
                    <div class="mb-3">
                        <label for="additional_notes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="additional_notes" name="additional_notes" rows="2" placeholder="Any special instructions or additional information..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>List Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</script>
@endsection
