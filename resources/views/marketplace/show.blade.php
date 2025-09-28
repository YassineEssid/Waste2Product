@extends('layouts.app')

@section('title', $marketplaceItem->title . ' - Marketplace')

@section('content')
<div class="container-fluid">
    <!-- Back Navigation -->
    <div class="container">
        <div class="back-navigation mb-4">
            <a href="{{ route('marketplace.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Marketplace
            </a>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Image Gallery -->
            <div class="col-lg-6 mb-4">
                <div class="image-gallery-container">
                    @if($marketplaceItem->images && $marketplaceItem->images->count() > 0)
                        <div id="mainCarousel" class="carousel slide main-carousel mb-3" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($marketplaceItem->images as $index => $image)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ Storage::url($image->path) }}" alt="{{ $marketplaceItem->title }}" class="d-block w-100">
                                    </div>
                                @endforeach
                            </div>
                            @if($marketplaceItem->images->count() > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            @endif
                        </div>

                        <!-- Thumbnail Navigation -->
                        @if($marketplaceItem->images->count() > 1)
                            <div class="thumbnail-nav">
                                @foreach($marketplaceItem->images as $index => $image)
                                    <div class="thumbnail {{ $index === 0 ? 'active' : '' }}" data-bs-target="#mainCarousel" data-bs-slide-to="{{ $index }}">
                                        <img src="{{ Storage::url($image->path) }}" alt="Thumbnail">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="no-image-placeholder">
                            <i class="fas fa-image fa-4x mb-3"></i>
                            <p class="text-muted">No images available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Item Details -->
            <div class="col-lg-6">
                <div class="item-details">
                    <!-- Item Header -->
                    <div class="item-header mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <div class="item-badges mb-2">
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-tag me-1"></i>{{ ucfirst($marketplaceItem->category) }}
                                    </span>
                                    <span class="badge {{ $marketplaceItem->condition === 'excellent' ? 'bg-success' : ($marketplaceItem->condition === 'good' ? 'bg-primary' : ($marketplaceItem->condition === 'fair' ? 'bg-warning' : 'bg-danger')) }}">
                                        @switch($marketplaceItem->condition)
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
                                <h1 class="item-title">{{ $marketplaceItem->title }}</h1>
                            </div>
                            <div class="item-price">
                                <span class="price-amount">${{ number_format($marketplaceItem->price, 2) }}</span>
                            </div>
                        </div>

                        <!-- Item Meta -->
                        <div class="item-meta">
                            <div class="meta-item">
                                <i class="fas fa-clock text-muted me-2"></i>
                                <span>Listed {{ $marketplaceItem->created_at->diffForHumans() }}</span>
                            </div>
                            @if($marketplaceItem->views_count)
                                <div class="meta-item">
                                    <i class="fas fa-eye text-muted me-2"></i>
                                    <span>{{ $marketplaceItem->views_count }} views</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Seller Information -->
                    <div class="seller-card mb-4">
                        <div class="seller-header">
                            <h5 class="mb-3">
                                <i class="fas fa-user-circle me-2"></i>Seller Information
                            </h5>
                        </div>
                        <div class="seller-info">
                            <div class="seller-avatar me-3">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                            <div class="seller-details flex-grow-1">
                                <h6 class="mb-1">{{ $marketplaceItem->seller->name }}</h6>
                                <p class="text-muted mb-1">
                                    @if($marketplaceItem->seller->role && $marketplaceItem->seller->role !== 'user')
                                        {{ ucfirst($marketplaceItem->seller->role) }}
                                    @else
                                        Community Member
                                    @endif
                                </p>
                                <small class="text-muted">
                                    Member since {{ $marketplaceItem->seller->created_at->format('M Y') }}
                                </small>
                            </div>
                            @if($marketplaceItem->seller_id !== auth()->id())
                                <div class="seller-actions">
                                    <button class="btn btn-primary" onclick="contactSeller()">
                                        <i class="fas fa-envelope me-1"></i>Contact
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons mb-4">
                        @if($marketplaceItem->seller_id === auth()->id())
                            <!-- Owner Actions -->
                            <div class="d-flex gap-2 mb-3">
                                <a href="{{ route('marketplace.edit', $marketplaceItem) }}" class="btn btn-outline-primary flex-grow-1">
                                    <i class="fas fa-edit me-2"></i>Edit Item
                                </a>
                                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash me-2"></i>Delete
                                </button>
                            </div>
                            <div class="availability-toggle">
                                <form method="POST" action="{{ route('marketplace.update', $marketplaceItem) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="{{ $marketplaceItem->status === 'available' ? 'sold' : 'available' }}">
                                    <button type="submit" class="btn {{ $marketplaceItem->status === 'available' ? 'btn-warning' : 'btn-success' }} w-100">
                                        @if($marketplaceItem->status === 'available')
                                            <i class="fas fa-eye-slash me-2"></i>Mark as Sold
                                        @else
                                            <i class="fas fa-eye me-2"></i>Mark as Available
                                        @endif
                                    </button>
                                </form>
                            </div>
                        @else
                            <!-- Buyer Actions -->
                            @if($marketplaceItem->status === 'available')
                                <button class="btn btn-success btn-lg w-100 mb-3" onclick="contactSeller()">
                                    <i class="fas fa-shopping-cart me-2"></i>Buy Now
                                </button>
                            @else
                                <button class="btn btn-secondary btn-lg w-100 mb-3" disabled>
                                    <i class="fas fa-ban me-2"></i>Item Sold
                                </button>
                            @endif
                        @endif

                        <!-- Share Button -->
                        <button class="btn btn-outline-secondary w-100" onclick="shareItem()">
                            <i class="fas fa-share-alt me-2"></i>Share This Item
                        </button>
                    </div>

                    <!-- Safety Tips -->
                    <div class="safety-tips">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-shield-alt me-2"></i>Safety Tips
                        </h6>
                        <ul class="text-muted small">
                            <li>Meet in a public place for exchanges</li>
                            <li>Inspect items before purchasing</li>
                            <li>Trust your instincts about sellers</li>
                            <li>Report suspicious listings</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Item Description -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="description-section">
                    <h3 class="section-title mb-4">
                        <i class="fas fa-align-left me-2"></i>Description
                    </h3>
                    <div class="description-content">
                        {!! nl2br(e($marketplaceItem->description)) !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="details-section beautiful-details p-4 mb-4">
                    <h3 class="section-title mb-4">
                        <i class="fas fa-list-alt me-2"></i>Product Details
                    </h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-card">
                                <span class="detail-label"><i class="fas fa-tag me-2 text-success"></i>Category</span>
                                <span class="badge bg-success ms-2">{{ ucfirst($marketplaceItem->category) }}</span>
                            </div>
                            <div class="detail-card">
                                <span class="detail-label"><i class="fas fa-cube me-2 text-primary"></i>Condition</span>
                                <span class="badge {{ $marketplaceItem->condition === 'excellent' ? 'bg-success' : ($marketplaceItem->condition === 'good' ? 'bg-primary' : ($marketplaceItem->condition === 'fair' ? 'bg-warning' : 'bg-danger')) }} ms-2">{{ ucfirst($marketplaceItem->condition) }}</span>
                            </div>
                            <div class="detail-card">
                                <span class="detail-label"><i class="fas fa-dollar-sign me-2 text-warning"></i>Price</span>
                                <span class="fw-bold ms-2">${{ number_format($marketplaceItem->price, 2) }}</span>
                            </div>
                            <div class="detail-card">
                                <span class="detail-label"><i class="fas fa-sort-numeric-up me-2 text-info"></i>Quantity</span>
                                <span class="badge bg-info ms-2">{{ $marketplaceItem->quantity ?? '1' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-card">
                                <span class="detail-label"><i class="fas fa-info-circle me-2 text-primary"></i>Status</span>
                                <span class="badge {{ $marketplaceItem->status === 'available' ? 'bg-success' : 'bg-secondary' }} ms-2">{{ ucfirst($marketplaceItem->status) }}</span>
                            </div>
                            <div class="detail-card">
                                <span class="detail-label"><i class="fas fa-eye me-2 text-info"></i>Views</span>
                                <span class="badge bg-info ms-2">{{ $marketplaceItem->views_count ?? 0 }}</span>
                            </div>
                            <div class="detail-card">
                                <span class="detail-label"><i class="fas fa-handshake me-2 text-secondary"></i>Negotiable</span>
                                <span class="badge {{ $marketplaceItem->is_negotiable ? 'bg-success' : 'bg-secondary' }} ms-2">{{ $marketplaceItem->is_negotiable ? 'Yes' : 'No' }}</span>
                            </div>
                            <div class="detail-card">
                                <span class="detail-label"><i class="fas fa-calendar me-2 text-dark"></i>Listed</span>
                                <span class="ms-2">{{ $marketplaceItem->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Items -->
        @if($relatedItems->count() > 0)
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="section-title mb-4">
                        <i class="fas fa-box me-2"></i>Similar Items
                    </h3>
                    <div class="row">
                        @foreach($relatedItems as $item)
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="related-item-card">
                                    <div class="related-item-image">
                                        @if($item->images && $item->images->count() > 0)
                                            <img src="{{ Storage::url($item->images->first()->path) }}" alt="{{ $item->title }}">
                                        @else
                                            <div class="related-placeholder">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                        <div class="related-price">
                                            <span class="badge bg-success">${{ number_format($item->price, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="related-item-content">
                                        <h6>{{ Str::limit($item->title, 40) }}</h6>
                                        <small class="text-muted">{{ $item->category }}</small>
                                        <a href="{{ route('marketplace.show', $item) }}" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if($marketplaceItem->seller_id === auth()->id())
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Delete Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this item from the marketplace?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. Your listing will be permanently removed.
                </div>
                <div class="item-preview">
                    <h6>{{ $marketplaceItem->title }}</h6>
                    <small class="text-muted">
                        ${{ number_format($marketplaceItem->price, 2) }} • {{ $marketplaceItem->category }}
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('marketplace.destroy', $marketplaceItem) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete Item
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<style>
.back-navigation {
    padding: 1rem 0;
}

.main-carousel {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.main-carousel img {
    height: 400px;
    object-fit: cover;
}

.thumbnail-nav {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    padding: 0.5rem 0;
}

.thumbnail {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.3s ease;
}

.thumbnail.active {
    border-color: #28a745;
}

.thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image-placeholder {
    height: 400px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.item-details {
    padding: 1rem;
}

.item-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.item-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0;
}

.price-amount {
    font-size: 2.5rem;
    font-weight: 700;
    color: #28a745;
}

.item-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    color: #6c757d;
}

.seller-card {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 1.5rem;
    border: 1px solid #e9ecef;
}

.seller-info {
    display: flex;
    align-items: center;
}

.seller-avatar {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.action-buttons {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 1.5rem;
}

.safety-tips {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 10px;
    padding: 1rem;
}

.safety-tips ul {
    margin-bottom: 0;
    padding-left: 1.2rem;
}

.description-section {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.section-title {
    color: #2c3e50;
    font-weight: 600;
}

.description-content {
    font-size: 1.1rem;
    line-height: 1.7;
    color: #555;
}

.related-item-card {
    position: relative;
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.related-item-card:hover {
    transform: translateY(-5px);
}

.related-item-image {
    position: relative;
    height: 150px;
}

.related-item-image img,
.related-placeholder {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-placeholder {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.related-price {
    position: absolute;
    top: 10px;
    right: 10px;
}

.related-item-content {
    padding: 1rem;
}

.item-preview {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
}

.beautiful-details {
    background: linear-gradient(135deg, #f8f9fa 0%, #e6ffef 100%);
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(40, 167, 69, 0.08);
}

.detail-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.05);
}

.detail-label {
    font-weight: 500;
    color: #2c3e50;
    font-size: 1rem;
}

.badge {
    font-size: 0.9rem;
    padding: 0.4em 0.8em;
    border-radius: 20px;
}

@media (max-width: 768px) {
    .item-title {
        font-size: 1.5rem;
    }

    .price-amount {
        font-size: 2rem;
    }

    .main-carousel img {
        height: 250px;
    }

    .seller-info {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }

    .description-section,
    .action-buttons,
    .seller-card {
        padding: 1rem;
        border-radius: 10px;
    }

    .beautiful-details {
        padding: 1rem;
        border-radius: 12px;
    }

    .detail-card {
        flex-direction: column;
        align-items: flex-start;
        padding: 0.75rem 0.5rem;
    }
}
</style>

<script>
// Thumbnail navigation
document.addEventListener('DOMContentLoaded', function() {
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
});

function contactSeller() {
    alert('Contact seller functionality would be implemented here. This could open a contact form, messaging system, or show seller contact details.');
}

function shareItem() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $marketplaceItem->title }}',
            text: '{{ Str::limit($marketplaceItem->description, 100) }}',
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Item link copied to clipboard!');
        });
    }
}
</script>
@endsection