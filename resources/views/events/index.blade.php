@extends('layouts.app')

@section('title', 'Community Events')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="hero-section bg-gradient-primary text-white py-5 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-calendar-alt me-3"></i>Community Events
                    </h1>
                    <p class="lead mb-4">Join our community events, workshops, and gatherings to connect with like-minded eco-enthusiasts.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('events.create') }}" class="btn btn-lg btn-list-item shadow-sm px-4 py-2 d-flex align-items-center">
                            <span class="icon-circle bg-white text-primary d-flex align-items-center justify-content-center me-2" style="width:2.5rem;height:2.5rem;border-radius:50%;">
                                <i class="fas fa-plus"></i>
                            </span>
                            <span class="fw-bold">Create Event</span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="hero-stats">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="stat-card">
                                    <h3 class="h4 mb-1">{{ number_format($stats['total']) }}</h3>
                                    <small>Total Events</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <h3 class="h4 mb-1">{{ number_format($stats['upcoming']) }}</h3>
                                    <small>Upcoming</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="stat-card">
                                    <h3 class="h4 mb-1">{{ number_format($stats['ongoing']) }}</h3>
                                    <small>Ongoing Events</small>
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
                               placeholder="Search for events, workshops, gatherings...">
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
                    <a href="{{ route('events.index') }}"
                       class="btn btn-outline-primary {{ !request()->has('status') ? 'active' : '' }}">
                        <i class="fas fa-globe me-1"></i>All Events
                    </a>
                    <a href="{{ route('events.index', ['status' => 'upcoming']) }}"
                       class="btn btn-outline-primary {{ request('status') == 'upcoming' ? 'active' : '' }}">
                        <i class="fas fa-clock me-1"></i>Upcoming
                    </a>
                    <a href="{{ route('events.index', ['status' => 'ongoing']) }}"
                       class="btn btn-outline-primary {{ request('status') == 'ongoing' ? 'active' : '' }}">
                        <i class="fas fa-play-circle me-1"></i>Ongoing
                    </a>
                    <a href="{{ route('events.index', ['status' => 'completed']) }}"
                       class="btn btn-outline-primary {{ request('status') == 'completed' ? 'active' : '' }}">
                        <i class="fas fa-check-circle me-1"></i>Completed
                    </a>
                </div>
            </div>
        </div>

        <!-- Sort Options -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="results-count">
                        <p class="text-muted mb-0">
                            Showing {{ $events->count() }} of {{ $events->total() }} events
                            @if(request('search'))
                                for "<strong>{{ request('search') }}</strong>"
                            @endif
                        </p>
                    </div>
                    <div class="sort-options">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-sort me-2"></i>Sort By
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'date_asc']) }}">
                                    <i class="fas fa-sort-amount-up me-2"></i>Date: Earliest First
                                </a></li>
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'date_desc']) }}">
                                    <i class="fas fa-sort-amount-down me-2"></i>Date: Latest First
                                </a></li>
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'title']) }}">
                                    <i class="fas fa-sort-alpha-down me-2"></i>Title A-Z
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($events->count() > 0)
            <!-- Events Grid -->
            <div class="row">
                @foreach($events as $event)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="event-card card h-100 shadow-sm">
                            <!-- Event Image -->
                            @if($event->image)
                                <img src="{{ Storage::url($event->image) }}" 
                                     class="card-img-top" 
                                     alt="{{ $event->title }}"
                                     style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center" 
                                     style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <i class="fas fa-calendar-alt fa-4x text-white opacity-50"></i>
                                </div>
                            @endif
                            
                            <div class="card-body">
                                <!-- Event Status Badge -->
                                <div class="mb-2">
                                    <span class="badge 
                                        @if($event->is_upcoming) bg-success
                                        @elseif($event->is_ongoing) bg-primary
                                        @else bg-secondary @endif">
                                        @if($event->is_upcoming)
                                            <i class="fas fa-clock me-1"></i>Upcoming
                                        @elseif($event->is_ongoing)
                                            <i class="fas fa-play me-1"></i>Ongoing
                                        @else
                                            <i class="fas fa-check me-1"></i>Completed
                                        @endif
                                    </span>
                                </div>

                                <h5 class="card-title">{{ $event->title }}</h5>
                                <p class="card-text">{{ Str::limit($event->description, 100) }}</p>
                                
                                <div class="event-meta">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $event->starts_at->format('M j, Y g:i A') }}
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-users me-1"></i>
                                        {{ $event->attendees_count }} participants
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $event->location }}
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <a href="{{ route('events.show', $event) }}" class="btn btn-primary btn-sm">
                                    View Details
                                </a>
                                @auth
                                    <div class="mt-2">
                                        <a href="{{ route('events.edit', $event) }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('events.destroy', $event) }}" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $events->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <div class="empty-icon mb-4">
                    <i class="fas fa-calendar-times fa-4x text-muted"></i>
                </div>
                <h3 class="h4 mb-3">No Events Found</h3>
                <p class="text-muted mb-4">
                    @if(request()->hasAny(['search', 'status']))
                        No events match your current filters. Try adjusting your search criteria.
                    @else
                        No events are currently available. Be the first to create one!
                    @endif
                </p>
                <div class="d-flex justify-content-center gap-3">
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('events.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                    @endif
                    <a href="{{ route('events.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create First Event
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.event-card {
    border: none;
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}

.event-card .card-img-top {
    border-radius: 15px 15px 0 0;
    transition: transform 0.3s ease;
}

.event-card:hover .card-img-top {
    transform: scale(1.05);
}

.event-card .card-body {
    padding: 1.5rem;
}

.event-card .card-title {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.event-card .card-text {
    color: #6c757d;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.event-meta {
    margin-top: 1rem;
}

.event-meta small {
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
    
    .event-card {
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