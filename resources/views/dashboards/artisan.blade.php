@extends('layouts.app')

@section('title', 'Dashboard Artisan - Waste2Product')

@section('content')
<div class="container py-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Artisan Dashboard 🎨</h1>
                    <p class="text-muted">Welcome {{ $user->name }}, transform and create here</p>
                </div>
                <div>
                    <span class="badge bg-purple px-3 py-2">
                        <i class="fas fa-palette me-2"></i>Creative Artisan
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['my_transformations'] }}</h3>
                            <p class="mb-0 small">Transformations</p>
                        </div>
                        <i class="fas fa-recycle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['completed_transformations'] }}</h3>
                            <p class="mb-0 small">Completed Transformations</p>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['marketplace_items'] }}</h3>
                            <p class="mb-0 small">Marketplace Items</p>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['total_revenue'], 2) }}€</h3>
                            <p class="mb-0 small">Total Revenue</p>
                        </div>
                        <i class="fas fa-coins fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8 mb-4">
            <!-- My Transformations -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-purple text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-magic me-2"></i>My Transformations</h5>
                        <a href="{{ route('transformations.create') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-plus me-1"></i>New Transformation
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($myTransformations->count() > 0)
                        <div class="row">
                            @foreach($myTransformations as $transformation)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-start border-purple border-4">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-1">{{ $transformation->product_title }}</h6>
                                            <span class="badge bg-{{
                                                $transformation->status === 'published' ? 'success' :
                                                ($transformation->status === 'completed' ? 'primary' :
                                                ($transformation->status === 'in_progress' ? 'warning' : 'info'))
                                            }}">
                                                @switch($transformation->status)
                                                    @case('planned') Planned @break
                                                    @case('in_progress') In Progress @break
                                                    @case('completed') Completed @break
                                                    @case('published') Published @break
                                                    @default {{ ucfirst($transformation->status) }}
                                                @endswitch
                                            </span>
                                        </div>
                                        <p class="mb-2 small text-muted">{{ Str::limit($transformation->description, 80) }}</p>

                                        @if($transformation->price)
                                        <div class="mb-2">
                                            <strong class="text-primary">{{ number_format($transformation->price, 2) }} DT</strong>
                                        </div>
                                        @endif

                                        @if($transformation->impact && (isset($transformation->impact['co2_saved']) || isset($transformation->impact['waste_reduced'])))
                                        <div class="row g-1 mb-2">
                                            @if(isset($transformation->impact['co2_saved']) && $transformation->impact['co2_saved'] > 0)
                                            <div class="col-6">
                                                <small class="text-success">
                                                    <i class="fas fa-leaf"></i> {{ $transformation->impact['co2_saved'] }}kg CO₂
                                                </small>
                                            </div>
                                            @endif
                                            @if(isset($transformation->impact['waste_reduced']) && $transformation->impact['waste_reduced'] > 0)
                                            <div class="col-6">
                                                <small class="text-info">
                                                    <i class="fas fa-recycle"></i> {{ $transformation->impact['waste_reduced'] }}kg waste
                                                </small>
                                            </div>
                                            @endif
                                        </div>
                                        @endif

                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>{{ $transformation->created_at->format('d/m/Y') }}
                                            </small>
                                            <div>
                                                <a href="{{ route('transformations.show', $transformation) }}" class="btn btn-sm btn-outline-purple me-1">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('transformations.edit', $transformation) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-magic fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune transformation pour le moment</p>
                            <a href="{{ route('transformations.create') }}" class="btn btn-purple">
                                <i class="fas fa-plus me-2"></i>Créer ma première transformation
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Available Waste Items -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-box-open me-2"></i>Available Materials for Transformation</h5>
                </div>
                <div class="card-body">
                    @if($availableWasteItems->count() > 0)
                        <div class="row">
                            @foreach($availableWasteItems as $item)
                            <div class="col-md-6 mb-3">
                                <div class="card border">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $item->title }}</h6>
                                                <small class="text-muted d-block mb-1">
                                                    <i class="fas fa-tag me-1"></i>{{ $item->category }}
                                                </small>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-info-circle me-1"></i>{{ $item->condition }}
                                                </small>
                                            </div>
                                            <div>
                                                <a href="{{ route('waste-items.show', $item) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No materials available at the moment</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-user-circle text-purple me-2"></i>My Profile</h5>
                </div>
                <div class="card-body text-center">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                             class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-purple bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 100px; height: 100px;">
                            <span class="fs-1 text-purple fw-bold">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <h5>{{ $user->name }}</h5>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <a href="{{ route('profile.show') }}" class="btn btn-outline-purple w-100 mb-2">
                        <i class="fas fa-user me-2"></i>View my profile
                    </a>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-bolt text-warning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('transformations.create') }}" class="btn btn-purple">
                            <i class="fas fa-magic me-2"></i>New Transformation
                        </a>
                        <a href="{{ route('marketplace.create') }}" class="btn btn-outline-success">
                            <i class="fas fa-plus-circle me-2"></i>Publish on Marketplace
                        </a>
                        <a href="{{ route('waste-items.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-search me-2"></i>Find Materials
                        </a>
                    </div>
                </div>
            </div>

            <!-- My Marketplace Items -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-store me-2"></i>My Items for Sale</h5>
                </div>
                <div class="card-body">
                    @if($myMarketplaceItems->count() > 0)
                        @foreach($myMarketplaceItems as $item)
                        <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $item->title }}</h6>
                                    <small class="text-success fw-bold">{{ number_format($item->price, 2) }}€</small>
                                </div>
                                <span class="badge {{ $item->status === 'sold' ? 'bg-success' : 'bg-primary' }}">
                                    {{ $item->status === 'sold' ? 'Sold' : 'Available' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center mb-0 small">No items for sale</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-purple {
    background-color: #9b59b6 !important;
}
.text-purple {
    color: #9b59b6 !important;
}
.btn-purple {
    background-color: #9b59b6;
    color: white;
    border: none;
}
.btn-purple:hover {
    background-color: #8e44ad;
    color: white;
}
.btn-outline-purple {
    color: #9b59b6;
    border-color: #9b59b6;
}
.btn-outline-purple:hover {
    background-color: #9b59b6;
    color: white;
}
.border-purple {
    border-color: #9b59b6 !important;
}
</style>
@endsection
