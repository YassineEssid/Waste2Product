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
                        <a href="{{ route('events.create') }}" class="btn btn-light btn-lg" style="z-index:9999;position:relative;">
                            <i class="fas fa-plus me-2"></i>Create Event
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
                            <!-- Event Content -->
                            <div class="event-content">
                                <div class="event-meta mb-2">
                                    <small class="text-muted d-flex align-items-center mb-1">
                                        <i class="fas fa-calendar me-2"></i>
                                        @if($event->starts_at)
                                            {{ \Carbon\Carbon::parse($event->starts_at)->format('M d, Y • g:i A') }}
                                        @else
                                            <span>—</span>
                                        @endif
                                    </small>
                                    <small class="text-muted d-flex align-items-center mb-1">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        {{ $event->location_address ?? '—' }}
                                    </small>
                                    @if($event->is_online)
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
                                    @can('delete', $event)
                                        <form method="POST" action="{{ route('events.destroy', $event) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                    @endcan
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
</style>
@endsection

