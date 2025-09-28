@extends('layouts.app')

@section('title', 'Dashboard - Waste2Product')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0">Welcome back, {{ auth()->user()->name }}!</h1>
        <p class="text-muted">Your sustainable impact dashboard</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <h5 class="card-title">{{ $stats['available_items'] ?? 0 }}</h5>
                    <p class="card-text">Available Items</p>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-recycle"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <h5 class="card-title">{{ $stats['total_transformations'] ?? 0 }}</h5>
                    <p class="card-text">Transformations</p>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-magic"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <h5 class="card-title">{{ $stats['active_events'] ?? 0 }}</h5>
                    <p class="card-text">Active Events</p>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <h5 class="card-title">{{ $stats['marketplace_items'] ?? 0 }}</h5>
                    <p class="card-text">Marketplace Items</p>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-store"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Environmental Impact -->
<div class="row mb-4">
    <div class="col-lg-6 mb-3">
        <div class="card environmental-impact">
            <div class="card-body text-center">
                <h3>{{ number_format($stats['co2_saved'] ?? 0, 1) }} kg</h3>
                <p class="mb-0">CO₂ Saved</p>
                <small>Through community upcycling</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-3">
        <div class="card environmental-impact">
            <div class="card-body text-center">
                <h3>{{ number_format($stats['waste_reduced'] ?? 0, 1) }} kg</h3>
                <p class="mb-0">Waste Reduced</p>
                <small>Through repair & transformation</small>
            </div>
        </div>
    </div>
</div>

<!-- Personal Statistics -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Your Activity</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <h4 class="text-success">{{ $myStats['waste_items'] ?? 0 }}</h4>
                        <small class="text-muted">Items Listed</small>
                    </div>
                    <div class="col-md-3 text-center">
                        <h4 class="text-primary">{{ $myStats['repair_requests'] ?? 0 }}</h4>
                        <small class="text-muted">Repair Requests</small>
                    </div>
                    <div class="col-md-3 text-center">
                        <h4 class="text-warning">{{ $myStats['transformations'] ?? 0 }}</h4>
                        <small class="text-muted">Transformations</small>
                    </div>
                    <div class="col-md-3 text-center">
                        <h4 class="text-info">{{ $myStats['event_registrations'] ?? 0 }}</h4>
                        <small class="text-muted">Event Registrations</small>
                    </div>
                </div>
                
                <!-- Supprimer les sections conditionnelles qui causent des erreurs -->
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h6 class="mb-0">Recent Waste Items</h6>
                <a href="{{ route('waste-items.index') }}" class="btn btn-sm btn-outline-success">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($recentWasteItems as $item)
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">{{ $item->title ?? 'Untitled Item' }}</h6>
                        <small>{{ $item->created_at->diffForHumans() ?? 'Recently' }}</small>
                    </div>
                    <p class="mb-1">{{ Str::limit($item->description ?? 'No description', 50) }}</p>
                    <small class="text-muted">Community Item</small>
                </div>
                @empty
                <div class="text-center p-4">
                    <i class="fas fa-recycle fa-2x text-muted mb-2"></i>
                    <p class="text-muted">No recent waste items</p>
                    <a href="{{ route('waste-items.create') }}" class="btn btn-sm btn-outline-success">
                        Add First Item
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h6 class="mb-0">Recent Transformations</h6>
                <a href="{{ route('transformations.index') }}" class="btn btn-sm btn-outline-warning">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($recentTransformations as $transformation)
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">{{ $transformation->title ?? 'Untitled Transformation' }}</h6>
                        <small>{{ $transformation->created_at->diffForHumans() ?? 'Recently' }}</small>
                    </div>
                    <p class="mb-1">{{ Str::limit($transformation->description ?? 'No description', 50) }}</p>
                    <small class="text-muted">Community Transformation</small>
                </div>
                @empty
                <div class="text-center p-4">
                    <i class="fas fa-magic fa-2x text-muted mb-2"></i>
                    <p class="text-muted">No recent transformations</p>
                    <a href="{{ route('transformations.create') }}" class="btn btn-sm btn-outline-warning">
                        Start Transforming
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h6 class="mb-0">Upcoming Events</h6>
                <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-info">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($upcomingEvents as $event)
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">{{ $event->title ?? 'Untitled Event' }}</h6>
                        <small>{{ $event->starts_at ? $event->starts_at->format('M d') : 'Date TBD' }}</small>
                    </div>
                    <p class="mb-1">{{ Str::limit($event->description ?? 'No description', 50) }}</p>
                    <small class="text-muted">by {{ $event->creator_name ?? 'Community Organizer' }}</small>
                </div>
                @empty
                <div class="text-center p-4">
                    <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                    <p class="text-muted">No upcoming events</p>
                    <a href="{{ route('events.create') }}" class="btn btn-sm btn-outline-info">
                        Create First Event
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('waste-items.create') }}" class="btn btn-success w-100">
                            <i class="fas fa-plus"></i> Add Waste Item
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('waste-items.index') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-search"></i> Browse Items
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('transformations.create') }}" class="btn btn-warning w-100">
                            <i class="fas fa-magic"></i> Start Transformation
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('events.create') }}" class="btn btn-info w-100">
                            <i class="fas fa-calendar-plus"></i> Create Event
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stats-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #28a745, #20c997);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.environmental-impact {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 15px;
}

.environmental-impact h3 {
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card-header {
    background: white;
    border-bottom: 1px solid #e9ecef;
    border-radius: 15px 15px 0 0 !important;
    font-weight: 600;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid #e9ecef;
    padding: 1rem;
}

.list-group-item:last-child {
    border-bottom: none;
}

.btn {
    border-radius: 10px;
    font-weight: 500;
}

@media (max-width: 768px) {
    .stats-card .card-body {
        flex-direction: column;
        text-align: center;
    }
    
    .stats-icon {
        margin-top: 1rem;
    }
    
    .environmental-impact h3 {
        font-size: 1.5rem;
    }
}
</style>
@endsection