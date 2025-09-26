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
                    <p class="lead mb-4">Join eco-friendly workshops, cleanup drives, and sustainability events in your community.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <button type="button" class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#createEventModal">
                            <i class="fas fa-plus me-2"></i>Create Event
                        </button>
                        <button class="btn btn-outline-light btn-lg" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="fas fa-filter me-2"></i>Filters
                        </button>
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
                                    <h3 class="h4 mb-1">{{ number_format($stats['participants']) }}</h3>
                                    <small>Total Participants</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                               placeholder="Search events by title, description, or location...">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="quick-filters d-flex flex-wrap gap-2">
                    <a href="{{ route('events.index') }}" 
                       class="btn btn-outline-primary {{ !request()->hasAny(['status', 'type']) ? 'active' : '' }}">
                        <i class="fas fa-globe me-1"></i>All Events
                    </a>
                    <a href="{{ route('events.index', ['status' => 'upcoming']) }}" 
                       class="btn btn-outline-success {{ request('status') == 'upcoming' ? 'active' : '' }}">
                        <i class="fas fa-clock me-1"></i>Upcoming
                    </a>
                    <a href="{{ route('events.index', ['type' => 'workshop']) }}" 
                       class="btn btn-outline-info {{ request('type') == 'workshop' ? 'active' : '' }}">
                        <i class="fas fa-tools me-1"></i>Workshops
                    </a>
                    <a href="{{ route('events.index', ['type' => 'cleanup']) }}" 
                       class="btn btn-outline-warning {{ request('type') == 'cleanup' ? 'active' : '' }}">
                        <i class="fas fa-leaf me-1"></i>Cleanups
                    </a>
                    <a href="{{ route('events.index', ['type' => 'exhibition']) }}" 
                       class="btn btn-outline-danger {{ request('type') == 'exhibition' ? 'active' : '' }}">
                        <i class="fas fa-eye me-1"></i>Exhibitions
                    </a>
                </div>
            </div>
        </div>

        @if($events->count() > 0)
            <!-- Events Grid -->
            <div class="row">
                @foreach($events as $event)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="event-card h-100">
                            <!-- Event Image -->
                            <div class="event-image">
                                @if($event->image)
                                    <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="img-fluid">
                                @else
                                    <div class="placeholder-image d-flex align-items-center justify-content-center">
                                        <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                                    </div>
                                @endif
                                
                                <!-- Event Type Badge -->
                                <div class="event-type-badge">
                                    <span class="badge bg-primary">
                                        @switch($event->type)
                                            @case('workshop')
                                                <i class="fas fa-tools me-1"></i>Workshop
                                                @break
                                            @case('cleanup')
                                                <i class="fas fa-leaf me-1"></i>Cleanup
                                                @break
                                            @case('exhibition')
                                                <i class="fas fa-eye me-1"></i>Exhibition
                                                @break
                                            @case('seminar')
                                                <i class="fas fa-graduation-cap me-1"></i>Seminar
                                                @break
                                            @case('competition')
                                                <i class="fas fa-trophy me-1"></i>Competition
                                                @break
                                            @default
                                                <i class="fas fa-calendar me-1"></i>{{ ucfirst($event->type) }}
                                        @endswitch
                                    </span>
                                </div>

                                <!-- Event Status -->
                                <div class="event-status-badge">
                                    @if($event->event_date > now())
                                        <span class="badge bg-success">Upcoming</span>
                                    @elseif($event->event_date <= now() && $event->end_date > now())
                                        <span class="badge bg-warning">Ongoing</span>
                                    @else
                                        <span class="badge bg-secondary">Completed</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Event Content -->
                            <div class="event-content">
                                <div class="event-meta mb-2">
                                    <small class="text-muted d-flex align-items-center mb-1">
                                        <i class="fas fa-calendar me-2"></i>
                                        {{ $event->event_date->format('M d, Y • g:i A') }}
                                    </small>
                                    <small class="text-muted d-flex align-items-center mb-1">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        {{ $event->location }}
                                    </small>
                                    @if($event->is_virtual)
                                        <small class="text-info d-flex align-items-center">
                                            <i class="fas fa-video me-2"></i>Virtual Event
                                        </small>
                                    @endif
                                </div>

                                <h5 class="event-title">
                                    <a href="{{ route('events.show', $event) }}" class="text-decoration-none">
                                        {{ $event->title }}
                                    </a>
                                </h5>

                                <p class="event-description text-muted">
                                    {{ Str::limit($event->description, 120) }}
                                </p>

                                <!-- Event Stats -->
                                <div class="event-stats d-flex justify-content-between align-items-center mb-3">
                                    <div class="participants">
                                        <small class="text-muted">
                                            <i class="fas fa-users me-1"></i>
                                            {{ $event->attendees->count() }}
                                            @if($event->max_participants)
                                                / {{ $event->max_participants }}
                                            @endif
                                            participants
                                        </small>
                                    </div>
                                    <div class="creator">
                                        <small class="text-muted">
                                            by <strong>{{ $event->creator->name }}</strong>
                                        </small>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="event-actions d-flex gap-2">
                                    <a href="{{ route('events.show', $event) }}" class="btn btn-primary btn-sm flex-grow-1">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                    @if($event->event_date > now())
                                        @if(!$event->attendees->contains(auth()->id()))
                                            @if(!$event->max_participants || $event->attendees->count() < $event->max_participants)
                                                <form method="POST" action="{{ route('events.register', $event) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-user-plus me-1"></i>Join
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <span class="btn btn-outline-success btn-sm">
                                                <i class="fas fa-check me-1"></i>Registered
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        {{ $events->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state text-center py-5">
                <div class="empty-icon mb-4">
                    <i class="fas fa-calendar-times fa-4x text-muted"></i>
                </div>
                <h3 class="h4 mb-3">No Events Found</h3>
                <p class="text-muted mb-4">
                    @if(request()->hasAny(['search', 'status', 'type']))
                        No events match your current filters. Try adjusting your search criteria.
                    @else
                        There are no community events yet. Be the first to create one!
                    @endif
                </p>
                <div class="d-flex justify-content-center gap-3">
                    @if(request()->hasAny(['search', 'status', 'type']))
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

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="GET">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-filter me-2"></i>Filter Events
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Event Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Event Type</label>
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="workshop" {{ request('type') == 'workshop' ? 'selected' : '' }}>Workshop</option>
                                <option value="cleanup" {{ request('type') == 'cleanup' ? 'selected' : '' }}>Cleanup</option>
                                <option value="exhibition" {{ request('type') == 'exhibition' ? 'selected' : '' }}>Exhibition</option>
                                <option value="seminar" {{ request('type') == 'seminar' ? 'selected' : '' }}>Seminar</option>
                                <option value="competition" {{ request('type') == 'competition' ? 'selected' : '' }}>Competition</option>
                                <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">Clear All</a>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 0 0 30px 30px;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><pattern id="grain" width="100" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" fill-opacity="0.1"/></pattern></defs><rect width="100" height="20" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.stat-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 1rem;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.event-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.event-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.event-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.event-image img,
.placeholder-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.event-type-badge {
    position: absolute;
    top: 15px;
    left: 15px;
}

.event-status-badge {
    position: absolute;
    top: 15px;
    right: 15px;
}

.event-content {
    padding: 1.5rem;
}

.event-title a {
    color: #2c3e50;
    transition: color 0.3s ease;
}

.event-title a:hover {
    color: #3498db;
}

.event-description {
    font-size: 0.9rem;
    line-height: 1.5;
}

.quick-filters .btn {
    border-radius: 25px;
    transition: all 0.3s ease;
}

.quick-filters .btn.active {
    transform: scale(1.05);
}

.search-form .input-group {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.empty-state {
    background: linear-gradient(135deg, #f8f9ff 0%, #e8f0ff 100%);
    border-radius: 20px;
    margin: 2rem 0;
}

.empty-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

@media (max-width: 768px) {
    .hero-section {
        border-radius: 0 0 20px 20px;
    }
    
    .quick-filters {
        justify-content: center;
    }
    
    .event-card {
        border-radius: 15px;
    }
}

<!-- Create Event Modal -->
<div class="modal fade" id="createEventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Create New Event
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Event Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Event Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>

                    <div class="row">
                        <!-- Event Type -->
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Event Type</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="workshop">Workshop</option>
                                <option value="cleanup">Cleanup Drive</option>
                                <option value="exhibition">Exhibition</option>
                                <option value="seminar">Seminar</option>
                                <option value="competition">Competition</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Max Participants -->
                        <div class="col-md-6 mb-3">
                            <label for="max_participants" class="form-label">Max Participants</label>
                            <input type="number" class="form-control" id="max_participants" name="max_participants" min="1">
                        </div>
                    </div>

                    <div class="row">
                        <!-- Event Date -->
                        <div class="col-md-6 mb-3">
                            <label for="event_date" class="form-label">Event Date</label>
                            <input type="date" class="form-control" id="event_date" name="event_date" required>
                        </div>

                        <!-- Start Time -->
                        <div class="col-md-6 mb-3">
                            <label for="start_time" class="form-label">Start Time</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Duration -->
                        <div class="col-md-6 mb-3">
                            <label for="duration" class="form-label">Duration (hours)</label>
                            <input type="number" class="form-control" id="duration" name="duration" step="0.5" min="0.5">
                        </div>

                        <!-- Fee -->
                        <div class="col-md-6 mb-3">
                            <label for="fee" class="form-label">Registration Fee</label>
                            <input type="number" class="form-control" id="fee" name="fee" step="0.01" min="0" placeholder="0.00">
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>

                    <!-- Requirements -->
                    <div class="mb-3">
                        <label for="requirements" class="form-label">Requirements</label>
                        <textarea class="form-control" id="requirements" name="requirements" rows="2" placeholder="What should participants bring or prepare?"></textarea>
                    </div>

                    <!-- Event Image -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Event Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="text-muted">Optional: Upload an image for your event</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Create Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</style>
@endsection