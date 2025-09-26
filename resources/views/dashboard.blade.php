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
                
                @if(auth()->user()->isRepairer() && isset($myStats['assigned_repairs']))
                <hr>
                <div class="row">
                    <div class="col-12 text-center">
                        <h4 class="text-danger">{{ $myStats['assigned_repairs'] }}</h4>
                        <small class="text-muted">Active Repair Assignments</small>
                    </div>
                </div>
                @endif
                
                @if(auth()->user()->isArtisan() && isset($myStats['marketplace_items']))
                <hr>
                <div class="row">
                    <div class="col-12 text-center">
                        <h4 class="text-success">{{ $myStats['marketplace_items'] }}</h4>
                        <small class="text-muted">Marketplace Listings</small>
                    </div>
                </div>
                @endif
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
                @forelse($recentWasteItems ?? [] as $item)
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">{{ $item->title }}</h6>
                        <small>{{ $item->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-1">{{ Str::limit($item->description, 50) }}</p>
                    <small class="text-muted">by {{ $item->user->name }}</small>
                </div>
                @empty
                <div class="text-center p-4">
                    <p class="text-muted">No recent items</p>
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
                @forelse($recentTransformations ?? [] as $transformation)
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">{{ $transformation->title }}</h6>
                        <small>{{ $transformation->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-1">{{ Str::limit($transformation->description, 50) }}</p>
                    <small class="text-muted">by {{ $transformation->user->name }}</small>
                </div>
                @empty
                <div class="text-center p-4">
                    <p class="text-muted">No recent transformations</p>
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
                @forelse($upcomingEvents ?? [] as $event)
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">{{ $event->title }}</h6>
                        <small>{{ $event->starts_at->format('M d') }}</small>
                    </div>
                    <p class="mb-1">{{ Str::limit($event->description, 50) }}</p>
                    <small class="text-muted">by {{ $event->user->name }}</small>
                </div>
                @empty
                <div class="text-center p-4">
                    <p class="text-muted">No upcoming events</p>
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
                    @if(auth()->user()->isArtisan())
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('transformations.create') }}" class="btn btn-warning w-100">
                            <i class="fas fa-magic"></i> Start Transformation
                        </a>
                    </div>
                    @endif
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
@endsection