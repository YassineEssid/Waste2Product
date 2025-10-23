@extends('layouts.app')

@section('title', $transformation->product_title)

@section('content')
<div class="container py-4">
    <!-- Back button -->
    <div class="mb-3">
        <a href="{{ route('transformations.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to list
        </a>
    </div>

    <div class="row g-4">
        <!-- Left Column - Images -->
        <div class="col-lg-8">
            <!-- Main Images Gallery -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @php
                                $allImages = array_merge(
                                    $transformation->before_images ?? [],
                                    $transformation->process_images ?? [],
                                    $transformation->after_images ?? []
                                );
                            @endphp

                            @forelse($allImages as $index => $image)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $image) }}" class="d-block w-100" alt="Transformation image" style="height: 500px; object-fit: cover;">
                            </div>
                            @empty
                            <div class="carousel-item active">
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 500px;">
                                    <i class="fas fa-image fa-5x text-muted"></i>
                                </div>
                            </div>
                            @endforelse
                        </div>

                        @if(count($allImages) > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Image Categories Tabs -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        @if($transformation->before_images && count($transformation->before_images) > 0)
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#before">
                                <i class="fas fa-history me-1"></i>Before ({{ count($transformation->before_images) }})
                            </a>
                        </li>
                        @endif

                        @if($transformation->process_images && count($transformation->process_images) > 0)
                        <li class="nav-item">
                            <a class="nav-link {{ !$transformation->before_images ? 'active' : '' }}" data-bs-toggle="tab" href="#process">
                                <i class="fas fa-cogs me-1"></i>Process ({{ count($transformation->process_images) }})
                            </a>
                        </li>
                        @endif

                        @if($transformation->after_images && count($transformation->after_images) > 0)
                        <li class="nav-item">
                            <a class="nav-link {{ !$transformation->before_images && !$transformation->process_images ? 'active' : '' }}" data-bs-toggle="tab" href="#after">
                                <i class="fas fa-check-circle me-1"></i>After ({{ count($transformation->after_images) }})
                            </a>
                        </li>
                        @endif
                    </ul>

                    <div class="tab-content">
                        @if($transformation->before_images && count($transformation->before_images) > 0)
                        <div class="tab-pane fade show active" id="before">
                            <div class="row g-2">
                                @foreach($transformation->before_images as $image)
                                <div class="col-md-3">
                                    <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded" alt="Before">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($transformation->process_images && count($transformation->process_images) > 0)
                        <div class="tab-pane fade {{ !$transformation->before_images ? 'show active' : '' }}" id="process">
                            <div class="row g-2">
                                @foreach($transformation->process_images as $image)
                                <div class="col-md-3">
                                    <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded" alt="Process">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($transformation->after_images && count($transformation->after_images) > 0)
                        <div class="tab-pane fade {{ !$transformation->before_images && !$transformation->process_images ? 'show active' : '' }}" id="after">
                            <div class="row g-2">
                                @foreach($transformation->after_images as $image)
                                <div class="col-md-3">
                                    <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded" alt="After">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2 text-primary"></i>Description</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">{{ $transformation->description }}</p>
                </div>
            </div>
        </div>

        <!-- Right Column - Details -->
        <div class="col-lg-4">
            <!-- Title & Price Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="mb-3">{{ $transformation->product_title }}</h3>

                    @if($transformation->price)
                    <h2 class="text-primary mb-3">{{ number_format($transformation->price, 2) }} DT</h2>
                    @endif

                    <!-- Status Badge -->
                    <span class="badge bg-{{
                        $transformation->status === 'published' ? 'success' :
                        ($transformation->status === 'completed' ? 'primary' :
                        ($transformation->status === 'in_progress' ? 'warning' : 'info'))
                    }} mb-3">
                        @switch($transformation->status)
                            @case('planned') <i class="fas fa-clipboard-list"></i> Planned @break
                            @case('in_progress') <i class="fas fa-spinner"></i> In Progress @break
                            @case('completed') <i class="fas fa-check-circle"></i> Completed @break
                            @case('published') <i class="fas fa-store"></i> Published @break
                        @endswitch
                    </span>

                    @if($transformation->is_featured)
                    <span class="badge bg-warning ms-2 mb-3">
                        <i class="fas fa-star"></i> Featured
                    </span>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">
                            <i class="fas fa-eye me-1"></i>{{ $transformation->views_count }} views
                        </small>
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>{{ $transformation->created_at->format('d/m/Y') }}
                        </small>
                    </div>

                    <!-- Actions -->
                    @if(auth()->user()->role === 'artisan' && $transformation->artisan_id === auth()->id())
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('transformations.edit', $transformation) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>

                        @if($transformation->status === 'completed')
                        <form action="{{ route('transformations.publish', $transformation) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-paper-plane me-2"></i>Publish on Marketplace
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('transformations.destroy', $transformation) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this transformation?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-2"></i>Delete
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Environmental Impact -->
            @if($transformation->impact && (isset($transformation->impact['co2_saved']) || isset($transformation->impact['waste_reduced'])))
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-leaf me-2"></i>Environmental Impact</h6>
                </div>
                <div class="card-body">
                    @if(isset($transformation->impact['co2_saved']) && $transformation->impact['co2_saved'] > 0)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-cloud fa-2x text-success"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-success">{{ $transformation->impact['co2_saved'] }} kg</h4>
                            <small class="text-muted">CO₂ saved</small>
                        </div>
                    </div>
                    @endif

                    @if(isset($transformation->impact['waste_reduced']) && $transformation->impact['waste_reduced'] > 0)
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-trash-alt fa-2x text-info"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-info">{{ $transformation->impact['waste_reduced'] }} kg</h4>
                            <small class="text-muted">Waste reduced</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Process Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-cogs me-2 text-warning"></i>Process Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Artisan</small>
                        <strong>{{ $transformation->user->name ?? 'N/A' }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Source waste</small>
                        <strong>{{ $transformation->wasteItem->type ?? 'N/A' }}</strong>
                    </div>

                    @if($transformation->time_spent_hours)
                    <div class="mb-3">
                        <small class="text-muted d-block">Time spent</small>
                        <strong>{{ $transformation->time_spent_hours }} hours</strong>
                    </div>
                    @endif

                    @if($transformation->materials_cost)
                    <div class="mb-3">
                        <small class="text-muted d-block">Materials cost</small>
                        <strong>{{ number_format($transformation->materials_cost, 2) }} DT</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.carousel-control-prev-icon,
.carousel-control-next-icon {
    filter: invert(1);
}
</style>
@endsection
