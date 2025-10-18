@extends('layouts.app')

@section('title', 'Comment Details')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('event-comments.index') }}" class="btn btn-outline-success">
                    <i class="fas fa-arrow-left me-2"></i>Back to Comments
                </a>
            </div>

            <!-- Main Comment Card -->
            <div class="card border-0 shadow-lg mb-4" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-gradient-success text-white py-4 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar-large me-3">
                                {{ strtoupper(substr($eventComment->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <h4 class="mb-1 fw-bold">{{ $eventComment->user->name }}</h4>
                                <p class="mb-0 opacity-75">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $eventComment->formatted_date }}
                                </p>
                            </div>
                        </div>
                        <div>
                            @if($eventComment->is_approved)
                                <span class="badge bg-white text-success px-3 py-2" style="font-size: 1rem;">
                                    <i class="fas fa-check-circle me-1"></i>Approved
                                </span>
                            @else
                                <span class="badge bg-warning px-3 py-2" style="font-size: 1rem;">
                                    <i class="fas fa-clock me-1"></i>Pending Approval
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Event Information -->
                    <div class="event-banner mb-4 p-4 rounded-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-left: 5px solid #28a745;">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <div class="icon-box-large bg-success text-white">
                                    <i class="fas fa-calendar-alt fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-2 fw-bold">Event</h5>
                                <h4 class="mb-2">
                                    <a href="{{ route('events.show', $eventComment->event) }}" class="text-decoration-none text-success">
                                        {{ $eventComment->event->title }}
                                    </a>
                                </h4>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-clock me-2"></i>
                                    {{ $eventComment->event->starts_at->format('l, F d, Y') }}
                                    at {{ $eventComment->event->starts_at->format('H:i') }}
                                </p>
                                @if($eventComment->event->location)
                                    <p class="text-muted mb-0 mt-1">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        {{ $eventComment->event->location }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Rating -->
                    @if($eventComment->rating)
                        <div class="rating-section mb-4 p-3 bg-light rounded-3">
                            <h6 class="text-muted mb-2">Rating</h6>
                            <div class="d-flex align-items-center">
                                <div class="stars me-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $eventComment->rating)
                                            <i class="fas fa-star fa-2x text-warning"></i>
                                        @else
                                            <i class="far fa-star fa-2x text-muted"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="h4 mb-0 text-warning">{{ $eventComment->rating }}/5</span>
                            </div>
                        </div>
                    @endif

                    <!-- Comment Content -->
                    <div class="comment-content mb-4">
                        <h5 class="fw-bold mb-3">Comment</h5>
                        <div class="comment-text p-4 bg-light rounded-3" style="border-left: 4px solid #28a745;">
                            <p class="mb-0 text-dark" style="font-size: 1.1rem; line-height: 1.8;">
                                {{ $eventComment->comment }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex gap-3 flex-wrap">
                        @if(Auth::id() === $eventComment->user_id || Auth::user()->is_admin)
                            <a href="{{ route('event-comments.edit', $eventComment) }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-edit me-2"></i>Edit Comment
                            </a>
                        @endif

                        @if(Auth::user()->is_admin)
                            <form action="{{ route('event-comments.toggle-approval', $eventComment) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-{{ $eventComment->is_approved ? 'warning' : 'success' }} btn-lg">
                                    <i class="fas fa-{{ $eventComment->is_approved ? 'times' : 'check' }}-circle me-2"></i>
                                    {{ $eventComment->is_approved ? 'Disapprove' : 'Approve' }}
                                </button>
                            </form>
                        @endif

                        @if(Auth::id() === $eventComment->user_id || Auth::user()->is_admin)
                            <form action="{{ route('event-comments.destroy', $eventComment) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Are you sure you want to delete this comment?')">
                                    <i class="fas fa-trash me-2"></i>Delete Comment
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Comments -->
            @if($relatedComments->count() > 0)
                <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                    <div class="card-header bg-light border-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-comments me-2 text-success"></i>
                            Other Comments on This Event
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            @foreach($relatedComments as $related)
                                <div class="col-md-6">
                                    <div class="related-comment-card p-3 border rounded-3 h-100">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar-small me-2">
                                                {{ strtoupper(substr($related->user->name, 0, 1)) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $related->user->name }}</h6>
                                                <small class="text-muted">{{ $related->time_ago }}</small>
                                            </div>
                                            @if($related->rating)
                                                <div>
                                                    @for($i = 1; $i <= $related->rating; $i++)
                                                        <i class="fas fa-star text-warning small"></i>
                                                    @endfor
                                                </div>
                                            @endif
                                        </div>
                                        <p class="text-muted mb-2 small">{{ Str::limit($related->comment, 100) }}</p>
                                        <a href="{{ route('event-comments.show', $related) }}" class="btn btn-sm btn-outline-success">
                                            Read More <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
    }
    .avatar-large {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: bold;
        border: 3px solid rgba(255, 255, 255, 0.3);
    }
    .avatar-small {
        width: 35px;
        height: 35px;
        background: #28a745;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: bold;
    }
    .icon-box-large {
        width: 70px;
        height: 70px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .related-comment-card {
        transition: all 0.3s ease;
        background: #f8f9fa;
    }
    .related-comment-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        background: white;
    }
</style>
@endsection
