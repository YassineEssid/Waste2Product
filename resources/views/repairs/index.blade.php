@extends('layouts.app')

@section('title', 'Repair Requests')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="hero-section text-white py-5 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-tools me-3"></i>Repair Requests
                    </h1>
                    <p class="lead mb-4">Manage all repair requests for your items or check your assigned tasks if you are a repairer.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('repairs.create') }}" class="btn btn-lg btn-list-item shadow-sm px-4 py-2 d-flex align-items-center">
                            <span class="icon-circle bg-white text-info d-flex align-items-center justify-content-center me-2" style="width:2.5rem;height:2.5rem;border-radius:50%;">
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
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); /* bg-info gradient */
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
        color: #17a2b8;
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

    /* Status filter buttons */
    .status-filters .btn {
        border-radius: 25px;
        transition: all 0.3s ease;
        color: #17a2b8;
        border-color: #17a2b8;
    }
    .status-filters .btn:hover {
        background-color: #17a2b8;
        color: white;
    }
    .status-filters .btn.active {
        background-color: #17a2b8;
        color: white;
        transform: scale(1.05);
    }

    /* Repair cards */
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
    .repair-meta small {
        display: block;
        margin-bottom: 0.25rem;
    }

    /* Status badges */
    .badge-waiting { background-color: #17a2b8; color: white; } /* info blue */
    .badge-in_progress { background-color: #138496; color: white; } /* darker info */
    .badge-completed { background-color: #28a745; color: white; } /* green */

    /* Search & buttons */
    .search-form .btn {
        background-color: #17a2b8;
        color: white;
        border: none;
    }
    .search-form .btn:hover {
        background-color: #138496;
    }
    .btn-view {
        background-color: #17a2b8;
        color: white;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .btn-view:hover {
        background-color: #138496;
    }

    .empty-icon { opacity: 0.5; }

    @media (max-width: 768px) {
        .hero-section { border-radius: 0 0 20px 20px; }
        .hero-section .display-4 { font-size: 2rem; }
        .status-filters { justify-content: center; }
        .repair-card { margin-bottom: 1rem; }
    }
    </style>

    <div class="container">
        <!-- Search Bar -->
        <div class="row mb-4">
            <div class="col-12">
                <form method="GET" class="search-form">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-info"></i> <!-- magnifier in info color -->
                        </span>
                        <input type="text"
                               class="form-control border-start-0 border-end-0"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search for repair requests...">
                        <button class="btn" type="submit">
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
                       class="btn {{ !request()->has('status') ? 'active' : '' }}">
                        <i class="fas fa-globe me-1"></i>All
                    </a>
                    <a href="{{ route('repairs.index', ['status' => 'waiting']) }}"
                       class="btn {{ request('status') == 'waiting' ? 'active' : '' }}">
                        <i class="fas fa-clock me-1"></i>Waiting
                    </a>
                    <a href="{{ route('repairs.index', ['status' => 'in_progress']) }}"
                       class="btn {{ request('status') == 'in_progress' ? 'active' : '' }}">
                        <i class="fas fa-play-circle me-1"></i>In Progress
                    </a>
                    <a href="{{ route('repairs.index', ['status' => 'completed']) }}"
                       class="btn {{ request('status') == 'completed' ? 'active' : '' }}">
                        <i class="fas fa-check-circle me-1"></i>Completed
                    </a>
                </div>
            </div>
        </div>

        <!-- Repair Cards -->
        @if($repairs->count() > 0)
            <div class="row">
                @foreach($repairs as $repair)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="repair-card card h-100 shadow-sm">
                            @php $firstImage = $repair->wasteItem->images[0] ?? null; @endphp
                            @if($firstImage)
                                <img src="{{ asset('storage/' . $firstImage) }}" class="card-img-top" alt="{{ $repair->wasteItem->title ?? 'Item' }}" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center" style="height: 200px; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                                    <i class="fas fa-tools fa-4x text-white opacity-50"></i>
                                </div>
                            @endif
                            <div class="card-body">
                                <!-- Status Badge -->
                                <div class="mb-2">
                                    <span class="badge 
                                        @if($repair->status == 'waiting') badge-waiting
                                        @elseif($repair->status == 'in_progress') badge-in_progress
                                        @elseif($repair->status == 'completed') badge-completed
                                        @else bg-secondary @endif">
                                        @if($repair->status == 'waiting')
                                            <i class="fas fa-clock me-1"></i>Waiting
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
    <small class="text-muted d-block">
        <i class="fas fa-user me-1"></i>
        <strong>Owner:</strong> {{ $repair->user->name }}
    </small>

    <small class="text-muted d-block">
        <i class="fas fa-user-cog me-1"></i>
        <strong>Repairer:</strong> {{ $repair->repairer->name ?? 'Not assigned' }}
    </small>

    <small class="text-muted d-block">
        <i class="fas fa-clock me-1"></i>
        {{ $repair->created_at->diffForHumans() }}
    </small>

    <small class="text-muted d-block">
        <i class="fas fa-exclamation-circle me-1"></i>
        <strong>Urgency:</strong>
        <span class="badge 
            {{ $repair->urgency === 'low' ? 'bg-success' : ($repair->urgency === 'medium' ? 'bg-warning text-dark' : 'bg-danger') }}">
            {{ ucfirst($repair->urgency ?? 'N/A') }}
        </span>
    </small>
</div>

                            </div>

                            <div class="card-footer bg-transparent border-top-0">
                                <a href="{{ route('repairs.show', $repair) }}" class="btn btn-view btn-sm">View Details</a>
                                @auth
                                    @if($repair->user_id === auth()->id() || auth()->user()->role === 'admin')
                                        <div class="mt-2">
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
                                        </div>
                                    @endif
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
                        No repair requests match your filters.
                    @else
                        No repair requests are currently available.
                    @endif
                </p>
                <div class="d-flex justify-content-center gap-3">
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('repairs.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                    @endif
                    <a href="{{ route('repairs.create') }}" class="btn btn-info">
                        <i class="fas fa-plus me-2"></i>Create First Repair Request
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
