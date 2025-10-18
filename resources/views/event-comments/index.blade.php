@extends('layouts.app')

@section('title', 'Event Comments')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="hero-section bg-gradient-success text-white py-5 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-comments me-3"></i>Event Comments
                    </h1>
                    <p class="lead mb-4">Browse and manage comments from community members about events.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('event-comments.create') }}" class="btn btn-lg btn-list-item shadow-sm px-4 py-2 d-flex align-items-center">
                            <span class="icon-circle bg-white text-success d-flex align-items-center justify-content-center me-2" style="width:2.5rem;height:2.5rem;border-radius:50%;">
                                <i class="fas fa-plus"></i>
                            </span>
                            <span class="fw-bold">Add Comment</span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="hero-stats">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="stat-card">
                                    <h3 class="h4 mb-1">{{ number_format($stats['total']) }}</h3>
                                    <small>Total Comments</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <h3 class="h4 mb-1">{{ number_format($stats['approved']) }}</h3>
                                    <small>Approved</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <h3 class="h4 mb-1">{{ number_format($stats['pending']) }}</h3>
                                    <small>Pending</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <h3 class="h4 mb-1">{{ $stats['average_rating'] ?? 'N/A' }}</h3>
                                    <small>Avg Rating</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Filters Section -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form action="{{ route('event-comments.index') }}" method="GET">
                    <div class="row g-3">
                        <!-- Search -->
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="search" name="search"
                                       placeholder="Search comments" value="{{ request('search') }}">
                                <label for="search"><i class="fas fa-search me-2"></i>Search</label>
                            </div>
                        </div>

                        <!-- Event Filter -->
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="event_id" name="event_id">
                                    <option value="">All Events</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                            {{ Str::limit($event->title, 30) }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="event_id"><i class="fas fa-calendar me-2"></i>Event</label>
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <select class="form-select" id="rating" name="rating">
                                    <option value="">All Ratings</option>
                                    <option value="5" {{ request('rating') == 5 ? 'selected' : '' }}>5 Stars</option>
                                    <option value="4" {{ request('rating') == 4 ? 'selected' : '' }}>4 Stars</option>
                                    <option value="3" {{ request('rating') == 3 ? 'selected' : '' }}>3 Stars</option>
                                    <option value="2" {{ request('rating') == 2 ? 'selected' : '' }}>2 Stars</option>
                                    <option value="1" {{ request('rating') == 1 ? 'selected' : '' }}>1 Star</option>
                                </select>
                                <label for="rating"><i class="fas fa-star me-2"></i>Rating</label>
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                                <label for="status"><i class="fas fa-filter me-2"></i>Status</label>
                            </div>
                        </div>

                        <!-- Sort -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <select class="form-select" id="sort" name="sort">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                    <option value="rating_high" {{ request('sort') == 'rating_high' ? 'selected' : '' }}>Highest Rating</option>
                                    <option value="rating_low" {{ request('sort') == 'rating_low' ? 'selected' : '' }}>Lowest Rating</option>
                                </select>
                                <label for="sort"><i class="fas fa-sort me-2"></i>Sort By</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-success me-2">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('event-comments.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Comments List -->
        @if($comments->count() > 0)
            <div class="row g-4">
                @foreach($comments as $comment)
                    <div class="col-md-6 col-lg-4">
                        <div class="card comment-card h-100 shadow-sm border-0 hover-lift">
                            <div class="card-body">
                                <!-- Header -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-success text-white me-3">
                                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $comment->user->name }}</h6>
                                            <small class="text-muted">{{ $comment->time_ago }}</small>
                                        </div>
                                    </div>
                                    @if($comment->is_approved)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Approved
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                    @endif
                                </div>

                                <!-- Event Info -->
                                <div class="event-info mb-3 p-2 bg-light rounded">
                                    <small class="text-muted d-block mb-1">
                                        <i class="fas fa-calendar me-1"></i>Event:
                                    </small>
                                    <a href="{{ route('events.show', $comment->event) }}" class="text-decoration-none fw-semibold text-success">
                                        {{ Str::limit($comment->event->title, 40) }}
                                    </a>
                                </div>

                                <!-- Rating -->
                                @if($comment->rating)
                                    <div class="rating mb-3">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $comment->rating)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-muted"></i>
                                            @endif
                                        @endfor
                                        <span class="ms-2 text-muted">({{ $comment->rating }}/5)</span>
                                    </div>
                                @endif

                                <!-- Comment Text -->
                                <p class="card-text text-muted mb-4">
                                    {{ Str::limit($comment->comment, 120) }}
                                </p>

                                <!-- Actions -->
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ route('event-comments.show', $comment) }}" class="btn btn-sm btn-outline-success flex-fill">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    @if(Auth::id() === $comment->user_id || Auth::user()->is_admin)
                                        <a href="{{ route('event-comments.edit', $comment) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('event-comments.destroy', $comment) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Are you sure you want to delete this comment?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-5">
                {{ $comments->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No comments found</h4>
                <p class="text-muted">Be the first to comment on an event!</p>
                <a href="{{ route('event-comments.create') }}" class="btn btn-success mt-3">
                    <i class="fas fa-plus me-2"></i>Add Comment
                </a>
            </div>
        @endif
    </div>
</div>

<style>
    .hero-section {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
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
        color: #28a745;
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
    .comment-card {
        transition: all 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
    }
    .comment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    }
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }
    .event-info {
        border-left: 3px solid #28a745;
    }
</style>
@endsection
