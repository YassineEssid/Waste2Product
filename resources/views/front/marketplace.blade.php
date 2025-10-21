@extends('front.layout')

@section('title', 'Marketplace - Waste2Product')

@section('content')
<!-- Hero -->
<section class="page-hero">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold text-white mb-4">Marketplace</h1>
                <p class="lead text-white-50">
                    Discover unique creations from upcycling and recycling
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Marketplace Grid -->
<section class="py-5">
    <div class="container">
        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-lg-10 mx-auto">
                <div class="filter-card">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" id="searchInput" class="form-control" 
                                   placeholder="Search for a product..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select id="categoryFilter" class="form-select">
                                <option value="">All categories</option>
                                <option value="decoration">Decoration</option>
                                <option value="furniture">Furniture</option>
                                <option value="accessories">Accessories</option>
                                <option value="art">Art</option>
                                <option value="clothing">Clothing</option>
                                <option value="recycled">Recycled</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="sortFilter" class="form-select">
                                <option value="recent">Most Recent</option>
                                <option value="price_asc">Price: Low to High</option>
                                <option value="price_desc">Price: High to Low</option>
                                <option value="popular">Popular</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div id="productsContainer">
            @if(isset($items) && $items->count() > 0)
            <div class="row g-4">
                @foreach($items as $item)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        @if($item->images && $item->images->count() > 0)
                            <img src="{{ Storage::url($item->images->first()->image_path) }}" alt="{{ $item->title }}" class="product-img">
                        @else
                            <div class="product-img-placeholder">
                                <i class="fas fa-shopping-bag fa-3x text-white"></i>
                            </div>
                        @endif

                        <div class="product-badge">
                            <span class="badge bg-success">Available</span>
                        </div>

                        <div class="product-content">
                            <h5 class="product-title">{{ $item->title }}</h5>

                            <p class="product-desc">
                                {{ Str::limit($item->description, 60) }}
                            </p>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="product-price">
                                    <span class="price">{{ number_format($item->price, 2) }} DT</span>
                                </div>
                                <div class="product-seller">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>{{ $item->seller->name ?? 'Seller' }}
                                    </small>
                                </div>
                            </div>

                            @auth
                                <a href="{{ route('marketplace.show', $item) }}" class="btn btn-success w-100">
                                    <i class="fas fa-eye me-2"></i>View product
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-success w-100" title="Login to buy">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login to buy
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($items->hasPages())
                <div class="mt-5 d-flex justify-content-center">
                    {{ $items->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-shopping-basket fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">No products available at the moment</h4>
                <p class="text-muted">Our artisans are working on new creations!</p>
            </div>
        @endif
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="fw-bold mb-3">Why buy on our Marketplace?</h2>
                <p class="text-muted">Unique, eco-friendly products made with passion</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon bg-success">
                        <i class="fas fa-leaf text-white"></i>
                    </div>
                    <h5 class="mt-3 mb-2">100% Recycled</h5>
                    <p class="text-muted small">All our products are created from recycled materials</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon bg-primary">
                        <i class="fas fa-star text-white"></i>
                    </div>
                    <h5 class="mt-3 mb-2">Unique Pieces</h5>
                    <p class="text-muted small">Each creation is unique and handmade</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon bg-warning">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <h5 class="mt-3 mb-2">Local Artisans</h5>
                    <p class="text-muted small">Support artisans from your region</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon bg-info">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <h5 class="mt-3 mb-2">Secure Payment</h5>
                    <p class="text-muted small">Your transactions are 100% secure</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="text-white fw-bold mb-4">Are you an artisan?</h2>
                <p class="text-white-50 mb-4">
                    Sell your creations on our marketplace and reach a wide audience
                </p>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-store me-2"></i>Sell my creations
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Become a seller
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
.page-hero {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    padding: 100px 0;
}

.min-vh-50 {
    min-height: 50vh;
}

.filter-card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
}

.product-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.product-img {
    width: 100%;
    height: 220px;
    object-fit: cover;
}

.product-img-placeholder {
    width: 100%;
    height: 220px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 10;
}

.product-content {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 10px;
    min-height: 50px;
}

.product-desc {
    color: #6b7280;
    font-size: 0.85rem;
    margin-bottom: 15px;
    flex: 1;
}

.product-price {
    font-size: 1.3rem;
    font-weight: 700;
    color: #10b981;
}

.product-seller {
    font-size: 0.85rem;
}

.features-section {
    margin-top: 60px;
}

.feature-box {
    text-align: center;
    padding: 30px 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.feature-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.feature-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.feature-icon i {
    font-size: 1.8rem;
}

.cta-section {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    padding: 80px 0;
    margin-top: 60px;
}

.form-control, .form-select {
    border-radius: 10px;
    padding: 12px 15px;
    border: 2px solid #e5e7eb;
}

.form-control:focus, .form-select:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const sortFilter = document.getElementById('sortFilter');
    const productsContainer = document.getElementById('productsContainer');
    let searchTimeout;

    // Function to fetch products
    function fetchProducts() {
        const searchValue = searchInput.value;
        const categoryValue = categoryFilter.value;
        const sortValue = sortFilter.value;

        // Show loading state
        productsContainer.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Searching products...</p>
            </div>
        `;

        // Build query parameters
        const params = new URLSearchParams();
        if (searchValue) params.append('search', searchValue);
        if (categoryValue) params.append('category', categoryValue);
        if (sortValue) params.append('sort', sortValue);

        // Fetch data
        fetch(`{{ route('api.search.marketplace') }}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                displayProducts(data.items);
            })
            .catch(error => {
                console.error('Error:', error);
                productsContainer.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-4x text-danger mb-4"></i>
                        <h4 class="text-muted">Error loading products</h4>
                        <p class="text-muted">Please try again later</p>
                    </div>
                `;
            });
    }

    // Function to display products
    function displayProducts(items) {
        if (items.length === 0) {
            productsContainer.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-shopping-basket fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No products available at the moment</h4>
                    <p class="text-muted">Our artisans are working on new creations!</p>
                </div>
            `;
            return;
        }

        let html = '<div class="row g-4">';
        items.forEach(item => {
            const imageHtml = item.images && item.images.length > 0
                ? `<img src="/storage/${item.images[0].image_path}" alt="${item.title}" class="product-img">`
                : `<div class="product-img-placeholder">
                    <i class="fas fa-shopping-bag fa-3x text-white"></i>
                   </div>`;

            const description = item.description ? item.description.substring(0, 60) + '...' : '';
            const sellerName = item.seller ? item.seller.name : 'Seller';

            const loginBtn = `{{ auth()->check() ? '' : 'true' }}`;
            const actionBtn = loginBtn 
                ? `<a href="{{ route('login') }}" class="btn btn-success w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Login to buy
                   </a>`
                : `<a href="/marketplace/${item.id}" class="btn btn-success w-100">
                    <i class="fas fa-eye me-2"></i>View product
                   </a>`;

            html += `
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        ${imageHtml}
                        <div class="product-badge">
                            <span class="badge bg-success">Available</span>
                        </div>
                        <div class="product-content">
                            <h5 class="product-title">${item.title}</h5>
                            <p class="product-desc">${description}</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="product-price">
                                    <span class="price">${parseFloat(item.price).toFixed(2)} DT</span>
                                </div>
                                <div class="product-seller">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>${sellerName}
                                    </small>
                                </div>
                            </div>
                            ${actionBtn}
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        productsContainer.innerHTML = html;
    }

    // Search input with debounce
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchProducts();
        }, 500);
    });

    // Category filter
    categoryFilter.addEventListener('change', function() {
        fetchProducts();
    });

    // Sort filter
    sortFilter.addEventListener('change', function() {
        fetchProducts();
    });

    // Initial load
    fetchProducts();
});
</script>
@endsection