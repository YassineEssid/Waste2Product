@extends('layouts.app')

@section('title', 'Repair Request Details')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="hero-section bg-gradient-primary text-white py-5 mb-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-tools me-3"></i>Repair Request
                    </h1>
                    <p class="lead mb-4">
                        Track and manage your repair request’s progress and related item details.
                    </p>
                </div>
                <div class="col-lg-4">
                    <div class="hero-stats">
                        <div class="stat-card text-center text-white">
                            <h3 class="h4 mb-1">
                                <i class="fas fa-calendar me-2"></i>
                                {{ $repair->created_at->format('M d, Y') }}
                            </h3>
                            <small>Created On</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hero-section {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-radius: 0 0 30px 30px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>

    <div class="container">
        <!-- Images Gallery -->
        @php
            $images = $repair->wasteItem->images ?? [];
            if (is_string($images)) $images = json_decode($images, true);
        @endphp

        @if($images && count($images) > 0)
            <div id="repairImages" class="carousel slide mb-5 shadow-sm rounded-4 overflow-hidden" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($images as $i => $img)
                        <div class="carousel-item {{ $i == 0 ? 'active' : '' }}">
                            <img src="{{ asset('waste-items/' . $img) }}" 
                                 class="d-block w-100" 
                                 style="height: 400px; object-fit: cover;"
                                 alt="Repair Image {{ $i + 1 }}">
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#repairImages" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#repairImages" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        @else
            <div class="text-center text-muted mb-5">
                <i class="fas fa-image fa-3x mb-2"></i>
                <p>No images available for this item.</p>
            </div>
        @endif

        <div class="row g-4">
            <!-- Main Info -->
            <div class="col-lg-8">
                <div class="card repair-card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="fw-bold mb-0 text-primary">
                                <i class="fas fa-cog me-2"></i>{{ $repair->wasteItem->title ?? 'Item' }}
                            </h3>
                            <span class="badge px-3 py-2 fs-6 
                                @if($repair->status === 'waiting') bg-warning text-dark
                                @elseif($repair->status === 'assigned') bg-info
                                @elseif($repair->status === 'in_progress') bg-primary
                                @elseif($repair->status === 'completed') bg-success
                                @else bg-secondary @endif">
                                {{ ucfirst(str_replace('_', ' ', $repair->status)) }}
                            </span>
                        </div>

                        <p class="text-muted mb-4">{{ $repair->description }}</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-box p-3 rounded bg-light">
                                    <small class="text-muted d-block mb-1">
                                        <i class="fas fa-user me-2"></i>Owner
                                    </small>
                                    <span class="fw-semibold">{{ $repair->user->name }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-box p-3 rounded bg-light">
                                    <small class="text-muted d-block mb-1">
                                        <i class="fas fa-user-cog me-2"></i>Repairer
                                    </small>
                                    <span class="fw-semibold">{{ $repair->repairer->name ?? 'Not assigned' }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-box p-3 rounded bg-light">
                                    <small class="text-muted d-block mb-1">
                                        <i class="fas fa-calendar-alt me-2"></i>Created
                                    </small>
                                    <span class="fw-semibold">{{ $repair->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-box p-3 rounded bg-light">
                                    <small class="text-muted d-block mb-1">
                                        <i class="fas fa-calendar-check me-2"></i>Last Update
                                    </small>
                                    <span class="fw-semibold">{{ $repair->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex flex-wrap gap-2">
                            <a href="{{ route('repairs.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                            @auth
                                @if(Auth::id() === $repair->user_id)
                                    <a href="{{ route('repairs.edit', $repair) }}" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('repairs.destroy', $repair) }}" onsubmit="return confirm('Are you sure?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger"><i class="fas fa-trash me-2"></i>Delete</button>
                                    </form>
                                @elseif(Auth::user()->role === 'repairer')
                                    @if($repair->status === 'waiting')
                                        <form method="POST" action="{{ route('repairs.assign', $repair) }}">
                                            @csrf
                                            <button class="btn btn-success"><i class="fas fa-hand-paper me-2"></i>Accept</button>
                                        </form>
                                    @elseif($repair->status === 'assigned' && $repair->repairer_id === Auth::id())
                                        <form method="POST" action="{{ route('repairs.start', $repair) }}">
                                            @csrf
                                            <button class="btn btn-primary"><i class="fas fa-play me-2"></i>Start</button>
                                        </form>
                                    @elseif($repair->status === 'in_progress' && $repair->repairer_id === Auth::id())
                                        <form method="POST" action="{{ route('repairs.complete', $repair) }}">
                                            @csrf
                                            <button class="btn btn-success"><i class="fas fa-check me-2"></i>Complete</button>
                                        </form>
                                    @endif
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Item Details
                        </h5>
                        <p class="text-muted mb-1"><strong>Category:</strong> {{ ucfirst($repair->wasteItem->category ?? 'N/A') }}</p>
                        <p class="text-muted mb-1"><strong>Quantity:</strong> {{ $repair->wasteItem->quantity ?? 'N/A' }}</p>
                        <p class="text-muted mb-1"><strong>Location:</strong> {{ $repair->wasteItem->location ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-primary mb-3">
                            <i class="fas fa-chart-line me-2"></i>Status Progress
                        </h5>
                        <ul class="list-unstyled timeline">
                            <li><span class="badge bg-light text-dark border"><i class="fas fa-check-circle text-success me-2"></i>Created</span></li>
                            <li><span class="badge {{ in_array($repair->status, ['assigned','in_progress','completed']) ? 'bg-info text-white' : 'bg-light text-dark border' }}"><i class="fas fa-user-cog me-2"></i>Assigned</span></li>
                            <li><span class="badge {{ in_array($repair->status, ['in_progress','completed']) ? 'bg-primary text-white' : 'bg-light text-dark border' }}"><i class="fas fa-tools me-2"></i>In Progress</span></li>
                            <li><span class="badge {{ $repair->status === 'completed' ? 'bg-success text-white' : 'bg-light text-dark border' }}"><i class="fas fa-check me-2"></i>Completed</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.repair-card {
    border-radius: 15px;
    transition: all 0.3s ease;
}
.repair-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}
.info-box {
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}
.info-box:hover {
    background: #f8f9fa;
    transform: translateY(-3px);
}
.timeline .badge {
    display: inline-block;
    margin-bottom: 0.75rem;
    border-radius: 20px;
    padding: 0.6rem 1rem;
}
</style>
@endsection
