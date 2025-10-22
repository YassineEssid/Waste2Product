@extends('layouts.app')

@section('title', 'Repair Requests')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="hero-section bg-gradient-primary text-white py-5 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-tools me-3"></i>Repair Requests
                    </h1>
                    <p class="lead mb-4">Manage all repair requests for your items or accept new tasks if you are a repairer.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('repairs.create') }}" class="btn btn-lg btn-list-item shadow-sm px-4 py-2 d-flex align-items-center">
                            <span class="icon-circle bg-white text-primary d-flex align-items-center justify-content-center me-2" style="width:2.5rem;height:2.5rem;border-radius:50%;">
                                <i class="fas fa-plus"></i>
                            </span>
                            <span class="fw-bold">Create Repair Request</span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="hero-stats">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="stat-card">
                                    <h3 class="h4 mb-1">{{ number_format($stats['total'] ?? 0) }}</h3>
                                    <small>Total Requests</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <h3 class="h4 mb-1">{{ number_format($stats['waiting'] ?? 0) }}</h3>
                                    <small>Waiting</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="stat-card">
                                    <h3 class="h4 mb-1">{{ number_format($stats['in_progress'] ?? 0) }}</h3>
                                    <small>In Progress</small>
                                </div>
                            </div>
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
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .btn-list-item {
            background: white;
            color: #007bff;
            border: none;
            border-radius: 1.5rem;
            transition: all 0.3s ease;
        }
        .btn-list-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .icon-circle {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
                               placeholder="Search for repair requests...">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Status Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="status-filters d-flex flex-wrap gap-2">
                    <a href="{{ route('repairs.index') }}"
                       class="btn btn-outline-primary {{ !request()->has('status') ? 'active' : '' }}">
                        <i class="fas fa-globe me-1"></i>All
                    </a>
                    <a href="{{ route('repairs.index', ['status' => 'waiting']) }}"
                       class="btn btn-outline-primary {{ request('status') == 'waiting' ? 'active' : '' }}">
                        <i class="fas fa-clock me-1"></i>Waiting
                    </a>
                    <a href="{{ route('repairs.index', ['status' => 'in_progress']) }}"
                       class="btn btn-outline-primary {{ request('status') == 'in_progress' ? 'active' : '' }}">
                        <i class="fas fa-play-circle me-1"></i>In Progress
                    </a>
                    <a href="{{ route('repairs.index', ['status' => 'completed']) }}"
                       class="btn btn-outline-primary {{ request('status') == 'completed' ? 'active' : '' }}">
                        <i class="fas fa-check-circle me-1"></i>Completed
                    </a>
                </div>
            </div>
        </div>

        @if($repairs->count() > 0)
            <div class="row">
                @foreach($repairs as $repair)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="repair-card card h-100 shadow-sm">
                     @php
    $images = $repair->wasteItem->images ?? null;

    // Decode only if it's a string
    if (is_string($images)) {
        $images = json_decode($images, true);
    }

    $firstImage = $images[0] ?? null;
@endphp

@if($firstImage)
    <img src="{{ asset('waste-items/' . $firstImage) }}"
         class="card-img-top"
         alt="{{ $repair->wasteItem->title ?? 'Item' }}"
         style="height: 200px; object-fit: cover;">
@else
    <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center" 
         style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <i class="fas fa-tools fa-4x text-white opacity-50"></i>
    </div>
@endif


                            <div class="card-body">
                                <!-- Status Badge -->
                                <div class="mb-2">
                                    <span class="badge 
                                        @if($repair->status == 'waiting') bg-warning
                                        @elseif($repair->status == 'assigned') bg-info
                                        @elseif($repair->status == 'in_progress') bg-primary
                                        @elseif($repair->status == 'completed') bg-success
                                        @else bg-secondary @endif">
                                        @if($repair->status == 'waiting')
                                            <i class="fas fa-clock me-1"></i>Waiting
                                        @elseif($repair->status == 'assigned')
                                            <i class="fas fa-user-cog me-1"></i>Assigned
                                        @elseif($repair->status == 'in_progress')
                                            <i class="fas fa-play me-1"></i>In Progress
                                        @elseif($repair->status == 'completed')
                                            <i class="fas fa-check me-1"></i>Completed
                                        @endif
                                    </span>
                                </div>

                                <h5 class="card-title">{{ $repair->wasteItem->title ?? 'Item' }}</h5>
                                <p class="card-text">{{ Str::limit($repair->description, 100) }}</p>
                                
                                <div class="repair-meta">
                                    <small class="text-muted"><i class="fas fa-user me-1"></i>Owner: {{ $repair->user->name }}</small>
                                    <br>
                                    <small class="text-muted"><i class="fas fa-user-cog me-1"></i>Repairer: {{ $repair->repairer->name ?? 'Not assigned' }}</small>
                                    <br>
                                    <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $repair->created_at->diffForHumans() }}</small>
                                    <br>
                                    <small class="text-muted"><i class="fas fa-exclamation-circle me-1"></i>{{ ucfirst($repair->urgency) }}</small>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent border-top-0">
                                <a href="{{ route('repairs.show', $repair) }}" class="btn btn-primary btn-sm">View Details</a>
                                @auth
                                    <div class="mt-2 d-flex gap-1">
                                        @if(Auth::id() === $repair->user_id)
                                            <a href="{{ route('repairs.edit', $repair) }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('repairs.destroy', $repair) }}" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @elseif(Auth::user()->role === 'repairer')
                                            @if($repair->status === 'waiting')
                                                <form action="{{ route('repairs.assign', $repair) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-hand-paper me-1"></i>Accept
                                                    </button>
                                                </form>
                                            @elseif($repair->status === 'assigned' && $repair->repairer_id === Auth::id())
                                                <form action="{{ route('repairs.start', $repair) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-play me-1"></i>Start
                                                    </button>
                                                </form>
                                            @elseif($repair->status === 'in_progress' && $repair->repairer_id === Auth::id())
                                                <form action="{{ route('repairs.complete', $repair) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check me-1"></i>Complete
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $repairs->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="empty-icon mb-4">
                    <i class="fas fa-tools fa-4x text-muted"></i>
                </div>
                <h3 class="h4 mb-3">No Repair Requests Found</h3>
                <p class="text-muted mb-4">
                    @if(request()->hasAny(['search', 'status']))
                        No repair requests match your current filters.
                    @else
                        No repair requests are currently available.
                    @endif
                </p>
                <div class="d-flex justify-content-center gap-3">
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('repairs.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                    @endif
                    <a href="{{ route('repairs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create First Repair Request
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.repair-card {
    border: none;
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
}

.repair-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}

.repair-card .card-img-top {
    border-radius: 15px 15px 0 0;
    transition: transform 0.3s ease;
}

.repair-card:hover .card-img-top {
    transform: scale(1.05);
}

.repair-card .card-body {
    padding: 1.5rem;
}

.repair-card .card-title {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.repair-card .card-text {
    color: #6c757d;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.repair-meta {
    margin-top: 1rem;
}

.repair-meta small {
    display: block;
    margin-bottom: 0.25rem;
}

.search-form .input-group {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.status-filters .btn {
    border-radius: 25px;
    transition: all 0.3s ease;
}

.status-filters .btn.active {
    background: #007bff;
    color: white;
    transform: scale(1.05);
}

.empty-icon {
    opacity: 0.5;
}

@media (max-width: 768px) {
    .hero-section {
        border-radius: 0 0 20px 20px;
    }
    
    .hero-section .display-4 {
        font-size: 2rem;
    }
    
    .status-filters {
        justify-content: center;
    }
    
    .repair-card {
        margin-bottom: 1rem;
    }
    
    .results-count {
        text-align: center;
        margin-bottom: 1rem;
    }
    
    .sort-options {
        width: 100%;
        display: flex;
        justify-content: center;
    }
}
</style>
@endsection
