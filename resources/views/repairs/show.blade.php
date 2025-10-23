@extends('layouts.app')

@section('title', $repair->title . ' - Repairs')

@section('content')
<div class="container-fluid">
    <!-- Back Navigation -->
    <div class="container">
        <div class="back-navigation mb-4">
            <a href="{{ route('repairs.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>See all repair requests
            </a>
        </div>
    </div>

    <div class="container">
        <div class="row">
<!-- Image Gallery -->
<div class="col-lg-6 mb-4">
    <div class="image-gallery-container">
        @if($repair->wasteItem->images && count($repair->wasteItem->images) > 0)
            <div id="mainCarousel" class="carousel slide main-carousel mb-3" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($repair->wasteItem->images as $index => $imagePath)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <img src="{{ Storage::url($imagePath) }}" alt="{{ $repair->wasteItem->title }}" class="d-block w-100">
                        </div>
                    @endforeach
                </div>
                @if(count($repair->wasteItem->images) > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                @endif
            </div>

            <!-- Thumbnail Navigation -->
            @if(count($repair->wasteItem->images) > 1)
                <div class="thumbnail-nav">
                    @foreach($repair->wasteItem->images as $index => $imagePath)
                        <div class="thumbnail {{ $index === 0 ? 'active' : '' }}" data-bs-target="#mainCarousel" data-bs-slide-to="{{ $index }}">
                            <img src="{{ Storage::url($imagePath) }}" alt="Thumbnail">
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
                                              
                    <!-- Repair Status Tag -->
<div class="mb-4">
  <span class="badge 
    {{ $repair->status === 'completed' ? 'bg-success' : ($repair->status === 'in_progress' ? 'bg-info text-white' : 'bg-warning') }}
    p-2"
>
    @switch($repair->status)
        @case('waiting')
            <i class="fas fa-hourglass-start me-1"></i>Waiting
            @break
        @case('in_progress')
            <i class="fas fa-tools me-1"></i>In Progress
            @break
        @case('completed')
            <i class="fas fa-check-circle me-1"></i>Completed
            @break
    @endswitch
</span>

</div>
                                <h1 class="item-title">{{ $repair->title }}</h1>
                            </div>
                            <div class="item-price">
                                <span class="price-amount">{{ number_format($repair->actual_cost, 2) }}DT</span>
                            </div>
                        </div>

                        <!-- Item Meta -->
                        <div class="item-meta">
                            <div class="meta-item">
                                <i class="fas fa-clock text-muted me-2"></i>
                                <span>requested {{ $repair->created_at->diffForHumans() }}</span>
                            </div>
                            @if($repair->views_count)
                                <div class="meta-item">
                                    <i class="fas fa-eye text-muted me-2"></i>
                                    <span>{{ $repair->views_count }} views</span>
                                </div>
                            @endif
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
                        {!! nl2br(e($repair->description)) !!}
                    </div>
                </div>
            </div>
        </div>
        
      

            </div>
        </div>
  <div class="repairer-card mb-4">
    <div class="repairer-header">
        <h5 class="mb-3">
            <i class="fas fa-user-cog me-2"></i>Repairer Information
        </h5>
    </div>
    <div class="repairer-info">
        <div class="repairer-avatar me-3">
            <i class="fas fa-user fa-2x"></i>
        </div>
        <div class="repairer-details flex-grow-1">
            @if($repair->repairer)
                <h6 class="mb-1">{{ $repair->repairer->name }}</h6>
                <p class="text-muted mb-1">
                    @if($repair->repairer->role && $repair->repairer->role !== 'user')
                        {{ ucfirst($repair->repairer->role) }}
                    @else
                        Community Member
                    @endif
                </p>
                <small class="text-muted">
                    Member since {{ $repair->repairer->created_at->format('M Y') }}
                </small>
            @else
                <p class="text-muted mb-0">No repairer assigned yet</p>
            @endif
        </div>
    </div>
</div>
        
        <!-- Repairer Completion Notes -->
@if($repair->repairer_notes)
<div class="row mt-4">
    <div class="col-12">
        <div class="description-section">
            <h3 class="section-title mb-4">
                <i class="fas fa-sticky-note me-2"></i>Repairer Notes
            </h3>
            <div class="description-content">
                {!! nl2br(e($repair->repairer_notes)) !!}
            </div>
        </div>
    </div>
</div>
@endif


        <!-- Product Details -->
         <div class="row mt-4">
    <div class="col-12">
        <div class="details-section beautiful-details p-4 mb-4">
            <h3 class="section-title mb-4">
                <i class="fas fa-list-alt me-2"></i>Repair Details
            </h3>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="detail-card">
                        <span class="detail-label"><i class="fas fa-box me-2 text-success"></i>Waste Item</span>
                        <span class="badge bg-success ms-2">{{ $repair->wasteItem->title ?? 'N/A' }}</span>
                    </div>
                 
                    <div class="detail-card">
                        <span class="detail-label"><i class="fas fa-dollar-sign me-2 text-warning"></i>Budget</span>
                        <span class="fw-bold ms-2">{{ $repair->budget ? number_format($repair->budget, 2) . ' DT' : 'N/A' }}</span>
                    </div>
                      <div class="detail-card">
                        <span class="detail-label"><i class="fas fa-money-bill-wave me-2 text-success"></i>Actual Cost</span>
                        <span class="fw-bold ms-2">{{ $repair->actual_cost ? number_format($repair->actual_cost, 2) . ' DT' : 'N/A' }}</span>
                    </div>
                   
                </div>
                <div class="col-md-6">
                    <div class="detail-card">
                        <span class="detail-label"><i class="fas fa-bolt me-2 text-primary"></i>Urgency</span>
                        <span class="badge 
                            {{ $repair->urgency === 'low' ? 'bg-success' : ($repair->urgency === 'medium' ? 'bg-warning text-dark' : 'bg-danger') }} ms-2">
                            {{ ucfirst($repair->urgency) }}
                        </span>
                    </div>
                    <div class="detail-card">
                        <span class="detail-label"><i class="fas fa-tools me-2 text-info"></i>Status</span>
                        <span class="badge 
                            {{ $repair->status === 'waiting' ? 'bg-warning text-dark' : ($repair->status === 'in_progress' ? 'bg-info text-white' : 'bg-success') }} ms-2">
                            {{ ucfirst(str_replace('_', ' ', $repair->status)) }}
                        </span>
                    </div>
                    <div class="detail-card">
                        <span class="detail-label"><i class="fas fa-calendar me-2 text-dark"></i>Created</span>
                        <span class="ms-2">{{ $repair->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

        <
{{-- Repairer Action Card --}}
@if(Auth::user()->id === $repair->repairer_id && $repair->status === 'in_progress')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header Card -->
            <div class="card border-0 shadow-lg mb-4" style="border-radius: 20px; overflow: hidden;">
<div class="card-header bg-gradient-info text-white py-4 border-0">
                    <div class="d-flex align-items-center">
                        <div class="icon-box me-3">
                            <i class="fas fa-tools fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold">Repair Completion</h3>
                            <p class="mb-0 opacity-75">Add notes, upload images, and complete the repair</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('repairs.complete', $repair) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Actual Cost -->
                        <div class="mb-4">
                            <label for="actual_cost" class="form-label fw-bold">
                                <i class="fas fa-dollar-sign text-success me-2"></i>Actual Cost (€)
                                <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="actual_cost" step="0.01" id="actual_cost"
                                   class="form-control form-control-lg @error('actual_cost') is-invalid @enderror" required>
                            @error('actual_cost')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Completion Notes -->
                        <div class="mb-4">
                            <label for="repairer_notes" class="form-label fw-bold">
                                <i class="fas fa-sticky-note text-info me-2"></i>Notes (Optional)
                            </label>
                            <textarea name="repairer_notes" id="repairer_notes" rows="4"
                                      class="form-control form-control-lg @error('repairer_notes') is-invalid @enderror"
                                      placeholder="Add any notes about the repair...">{{ old('repairer_notes') }}</textarea>
                            @error('repairer_notes')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- After Images -->
                        <div class="mb-4">
                            <label for="after_images" class="form-label fw-bold">
                                <i class="fas fa-camera text-warning me-2"></i>Upload Images (Optional)
                            </label>
                            <input type="file" name="after_images[]" id="after_images" multiple
                                   class="form-control form-control-lg @error('after_images') is-invalid @enderror">
                            @error('after_images')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex gap-3 mt-4">
<button type="submit" class="btn btn-lg px-5 flex-fill text-white" 
    style="border-radius: 15px; background: linear-gradient(135deg, #17a2b8 0%, #63cdda 100%); border: none;">
    <i class="fas fa-check-circle me-2"></i>Mark as Completed
</button>
    <a href="{{ route('repairs.show', $repair) }}" class="btn btn-outline-secondary btn-lg px-4" style="border-radius: 15px;">
        <i class="fas fa-times me-2"></i>Cancel
    </a>
</div>

                    </form>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-lightbulb text-warning me-2"></i>Tips for Completing Repairs
                    </h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>Be accurate with actual costs
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>Add clear and concise notes
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>Upload photos showing repair completion
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check-circle text-success me-2"></i>Mark repair as completed only when finished
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #63cdda 100%);
}

.icon-box {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}
textarea.form-control {
    resize: vertical;
}
</style>
@endif

        
        <!-- Related Items -->
        
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if($repair->seller_id === auth()->id())
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
                <p>Are you sure you want to delete this repair-request?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. Your listing will be permanently removed.
                </div>
                <div class="item-preview">
                    <h6>{{ $repair->title }}</h6>
                    <small class="text-muted">
                        {{ number_format($repair->price, 2) }} • {{ $repair->category }} DT
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('repair.destroy', $repair) }}" class="d-inline">
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
.repairer-card {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 1.5rem;
    border: 1px solid #e9ecef;
}

.repairer-info {
    display: flex;
    align-items: center;
}

.repairer-avatar {
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


function shareItem() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $repair->title }}',
            text: '{{ Str::limit($repair->description, 100) }}',
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
