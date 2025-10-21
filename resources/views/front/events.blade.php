@extends('front.layout')

@section('title', 'Events - Waste2Product')

@section('content')
<!-- Hero -->
<section class="page-hero">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold text-white mb-4">Community Events</h1>
                <p class="lead text-white-50">
                    Join our events to learn, share and act together
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Events Grid -->
<section class="py-5">
    <div class="container">
        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-lg-8 mx-auto">
                <div class="filter-card">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <input type="text" class="form-control" placeholder="Search for an event...">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select">
                                <option selected>All events</option>
                                <option>Upcoming</option>
                                <option>Ongoing</option>
                                <option>Completed</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events List -->
        @if(isset($events) && $events->count() > 0)
            <div class="row g-4">
                @foreach($events as $event)
                <div class="col-lg-4 col-md-6">
                    <div class="event-card-public">
                        @if($event->image)
                            <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="event-img">
                        @else
                            <div class="event-img-placeholder">
                                <i class="fas fa-calendar-alt fa-4x text-white"></i>
                            </div>
                        @endif

                        <div class="event-content">
                            <div class="d-flex gap-2 mb-3">
                                <span class="badge bg-success">
                                    <i class="fas fa-calendar me-1"></i>{{ $event->starts_at->format('d/m/Y') }}
                                </span>
                                <span class="badge bg-primary">
                                    <i class="fas fa-clock me-1"></i>{{ $event->starts_at->format('H:i') }}
                                </span>
                            </div>

                            <h4 class="mb-3">{{ $event->title }}</h4>

                            <p class="text-muted mb-3">
                                {{ Str::limit($event->description, 120) }}
                            </p>

                            <div class="event-meta mb-3">
                                <div class="meta-item">
                                    <i class="fas fa-map-marker-alt text-success me-2"></i>
                                    <span>{{ $event->location ?? 'Online' }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-users text-success me-2"></i>
                                    <span>{{ $event->registrations_count ?? 0 }} participants</span>
                                </div>
                            </div>

                            @auth
                                <a href="{{ route('events.show', $event) }}" class="btn btn-success w-100">
                                    <i class="fas fa-info-circle me-2"></i>View details
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-success w-100" title="Login to participate">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login to participate
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($events->hasPages())
                <div class="mt-5 d-flex justify-content-center">
                    {{ $events->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">No events available at the moment</h4>
                <p class="text-muted">Check back soon to discover our upcoming events!</p>
            </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="text-white fw-bold mb-4">Organizing an event?</h2>
                <p class="text-white-50 mb-4">
                    Join our community and share your eco-friendly events
                </p>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-plus-circle me-2"></i>Create an event
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Join the community
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
.page-hero {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    padding: 100px 0;
}

.min-vh-50 {
    min-height: 50vh;
}

.filter-card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
}

.event-card-public {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.event-card-public:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.event-img {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

.event-img-placeholder {
    width: 100%;
    height: 250px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.event-content {
    padding: 25px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.event-content h4 {
    color: #1f2937;
    font-weight: 700;
}

.event-meta {
    border-top: 1px solid #e5e7eb;
    border-bottom: 1px solid #e5e7eb;
    padding: 15px 0;
}

.meta-item {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    color: #6b7280;
    font-size: 0.9rem;
}

.meta-item:last-child {
    margin-bottom: 0;
}

.cta-section {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    padding: 80px 0;
    margin-top: 60px;
}

.form-control, .form-select {
    border-radius: 10px;
    padding: 12px 15px;
    border: 2px solid #e5e7eb;
}

.form-control:focus, .form-select:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}
</style>
@endsection
