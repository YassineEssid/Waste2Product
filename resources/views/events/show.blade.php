@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="container-fluid">
    <!-- Create Event button at the top -->
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('events.create') }}" class="btn btn-outline-dark">
                <i class="fas fa-plus"></i> Create Event
            </a>
        </div>
    </div>
    
    <!-- Hero Section -->
    <div class="event-hero">
        <div class="hero-background">
            @if($event->image)
                <img src="{{ Storage::url($event->image) }}" 
                     alt="{{ $event->title }}" 
                     style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <div class="hero-placeholder">
                    <i class="fas fa-calendar-alt fa-4x"></i>
                </div>
            @endif
            <div class="hero-overlay"></div>
        </div>

        <div class="hero-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Event Status -->
                        <div class="event-badges mb-3">
                            <span class="badge badge-status">
                                @if($event->starts_at && $event->starts_at->gt(now()))
                                    <i class="fas fa-clock me-1"></i>Upcoming
                                @elseif($event->starts_at && $event->ends_at && $event->starts_at->lte(now()) && $event->ends_at->gt(now()))
                                    <i class="fas fa-play me-1"></i>Ongoing
                                @else
                                    <i class="fas fa-check me-1"></i>Completed
                                @endif
                            </span>
                        </div>

                        <h1 class="hero-title">{{ $event->title }}</h1>

                        <!-- Event Meta -->
                        <div class="event-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>
                                    {{ $event->starts_at ? $event->starts_at->format('l, F j, Y') : 'Date not set' }}
                                </span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-clock"></i>
                                <span>
                                    {{ $event->starts_at ? $event->starts_at->format('g:i A') : 'Start time not set' }}
                                    -
                                    {{ $event->ends_at ? $event->ends_at->format('g:i A') : 'End time not set' }}
                                </span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $event->location }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-user"></i>
                                <span>by {{ $event->creator_name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-8">
                <!-- Event Description -->
                <div class="content-card mb-4">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>About This Event
                    </h3>
                    <div class="event-description">
                        {!! nl2br(e($event->description)) !!}
                    </div>
                </div>

                <!-- Related Events -->
                @if($relatedEvents->count() > 0)
                    <div class="content-card">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-week me-2"></i>Related Events
                        </h3>
                        <div class="row">
                            @foreach($relatedEvents as $relatedEvent)
                                <div class="col-md-4 mb-3">
                                    <div class="related-event-card">
                                        <div class="related-event-placeholder">
                                            <i class="fas fa-calendar"></i>
                                        </div>
                                        <div class="related-event-content">
                                            <h6>{{ Str::limit($relatedEvent->title, 40) }}</h6>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $relatedEvent->starts_at ? $relatedEvent->starts_at->format('M j') : '' }}
                                            </small>
                                            <a href="{{ route('events.show', $relatedEvent) }}" class="stretched-link"></a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Registration Card -->
                <div class="sidebar-card registration-card">
                    <div class="participation-status">
                        @if($event->starts_at && $event->starts_at->gt(now()))
                            <div class="status-available">
                                <i class="fas fa-calendar-plus fa-2x mb-2"></i>
                                <h5>Join This Event</h5>
                                <p class="text-muted">Be part of this amazing community event.</p>
                            </div>
                        @else
                            <div class="status-past">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                <h5>Event {{ $event->ends_at && $event->ends_at->lt(now()) ? 'Completed' : 'Ongoing' }}</h5>
                                <p class="text-muted">Registration is no longer available.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        @if($event->starts_at && $event->starts_at->gt(now()))
                            <button class="btn btn-primary btn-lg w-100 mb-2" onclick="showRegistrationInfo()">
                                <i class="fas fa-user-plus me-2"></i>Register Now
                            </button>
                        @endif

                        <!-- Edit and Delete buttons for all authenticated users (temporarily) -->
                        @auth
                            <a href="{{ route('events.edit', $event) }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-edit me-2"></i>Edit Event
                            </a>
                            
                            <form method="POST" action="{{ route('events.destroy', $event) }}" onsubmit="return confirm('Are you sure you want to delete this event?');" class="mb-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-trash me-2"></i>Delete Event
                                </button>
                            </form>
                        @endauth

                        <button class="btn btn-outline-secondary w-100" onclick="shareEvent()">
                            <i class="fas fa-share-alt me-2"></i>Share Event
                        </button>
                    </div>
                </div>

                <!-- Event Stats -->
                <div class="sidebar-card stats-card">
                    <h5>
                        <i class="fas fa-chart-bar me-2"></i>Event Statistics
                    </h5>
                    <div class="stats-list">
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $event->attendees_count }}</div>
                                <div class="stat-label">Participants</div>
                            </div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">
                                    @if($event->starts_at)
                                        {{ $event->starts_at->diffInDays(now()) }}
                                    @else
                                        N/A
                                    @endif
                                </div>
                                <div class="stat-label">
                                    Days {{ $event->starts_at && $event->starts_at->gt(now()) ? 'Until Event' : 'Since Event' }}
                                </div>
                            </div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">
                                    @if($event->starts_at && $event->ends_at)
                                        {{ $event->starts_at->diffInHours($event->ends_at) }}
                                    @else
                                        N/A
                                    @endif
                                </div>
                                <div class="stat-label">Hours Duration</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Creator Info -->
                <div class="sidebar-card creator-card">
                    <h5>
                        <i class="fas fa-user-circle me-2"></i>Event Organizer
                    </h5>
                    <div class="creator-info">
                        <div class="creator-avatar">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                        <div class="creator-details">
                            <h6>{{ $event->creator_name }}</h6>
                            <p class="text-muted mb-1">Community Organizer</p>
                            <small class="text-muted">
                                Organizing great events for the community
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.event-hero {
    position: relative;
    height: 400px;
    overflow: hidden;
    border-radius: 0 0 30px 30px;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}

.hero-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 100%);
}

.hero-content {
    position: relative;
    z-index: 2;
    height: 100%;
    display: flex;
    align-items: flex-end;
    padding-bottom: 2rem;
    color: white;
}

.event-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 500;
}

.badge-status {
    background: rgba(46, 204, 113, 0.9);
}

.hero-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.event-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    font-size: 1rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.meta-item i {
    width: 20px;
    text-align: center;
}

.content-card, .sidebar-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.sidebar-card {
    margin-bottom: 1.5rem;
}

.card-title {
    color: #2c3e50;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.event-description {
    font-size: 1.1rem;
    line-height: 1.7;
    color: #555;
}

.related-event-card {
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    background: white;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.related-event-card:hover {
    transform: translateY(-5px);
}

.related-event-placeholder {
    width: 100%;
    height: 120px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.related-event-content {
    padding: 1rem;
}

.related-event-content h6 {
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.registration-card {
    text-align: center;
    background: linear-gradient(135deg, #f8f9ff 0%, #e8f0ff 100%);
    border: 2px solid #e9ecef;
}

.participation-status i {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.status-available {
    color: #007bff;
}

.status-past {
    color: #6c757d;
}

.action-buttons .btn {
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.btn-outline-primary:hover, .btn-outline-secondary:hover, .btn-outline-danger:hover {
    transform: translateY(-2px);
}

.stats-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
}

.creator-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.creator-avatar {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.creator-details h6 {
    margin-bottom: 0.25rem;
    color: #2c3e50;
}

@media (max-width: 768px) {
    .event-hero {
        height: 300px;
        border-radius: 0 0 20px 20px;
    }

    .hero-title {
        font-size: 1.8rem;
    }

    .event-meta {
        flex-direction: column;
        gap: 0.5rem;
    }

    .content-card, .sidebar-card {
        border-radius: 10px;
        padding: 1rem;
    }
    
    .stats-list {
        gap: 0.75rem;
    }
    
    .stat-item {
        gap: 0.75rem;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
    }
    
    .stat-number {
        font-size: 1.25rem;
    }
}
</style>

<script>
function shareEvent() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $event->title }}',
            text: '{{ Str::limit($event->description, 100) }}',
            url: window.location.href
        });
    } else {
        // Fallback for browsers that don't support Web Share API
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Event link copied to clipboard!');
        });
    }
}

function showRegistrationInfo() {
    alert('Registration system is temporarily disabled. This feature will be available soon!');
}
</script>
@endsection