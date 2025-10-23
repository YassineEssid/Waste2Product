@extends('layouts.app')

@section('title', 'Edit Comment')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header Card -->
            <div class="card border-0 shadow-lg mb-4" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-gradient-primary text-white py-4 border-0">
                    <div class="d-flex align-items-center">
                        <div class="icon-box me-3">
                            <i class="fas fa-edit fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold">Edit Comment</h3>
                            <p class="mb-0 opacity-75">Update your thoughts about the event</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Current Info Alert -->
                    <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 15px;">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-info-circle fa-2x me-3 text-info"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-2 fw-bold">Current Comment For:</h6>
                                <p class="mb-1">{{ $eventComment->event->title }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    Posted {{ $eventComment->time_ago }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('event-comments.update', $eventComment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Event Selection -->
                        <div class="mb-4">
                            <label for="community_event_id" class="form-label fw-bold">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>Event
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg @error('community_event_id') is-invalid @enderror"
                                    id="community_event_id"
                                    name="community_event_id"
                                    style="border-radius: 15px;">
                                <option value="">Choose an event...</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}"
                                            {{ old('community_event_id', $eventComment->community_event_id) == $event->id ? 'selected' : '' }}>
                                        {{ $event->title }} - {{ $event->starts_at->format('d M Y') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('community_event_id')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Rating -->
                        <div class="mb-4">
                            <label for="rating" class="form-label fw-bold">
                                <i class="fas fa-star text-warning me-2"></i>Rating (Optional)
                            </label>
                            <div class="rating-input-container p-3 bg-light" style="border-radius: 15px;">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    @for($i = 1; $i <= 5; $i++)
                                        <div class="form-check">
                                            <input class="form-check-input rating-radio"
                                                   type="radio"
                                                   name="rating"
                                                   id="rating{{ $i }}"
                                                   value="{{ $i }}"
                                                   {{ old('rating', $eventComment->rating) == $i ? 'checked' : '' }}>
                                            <label class="form-check-label" for="rating{{ $i }}">
                                                @for($j = 1; $j <= $i; $j++)
                                                    <i class="fas fa-star text-warning"></i>
                                                @endfor
                                                @for($j = $i + 1; $j <= 5; $j++)
                                                    <i class="far fa-star text-muted"></i>
                                                @endfor
                                            </label>
                                        </div>
                                    @endfor
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                            onclick="document.querySelectorAll('.rating-radio').forEach(r => r.checked = false)">
                                        Clear Rating
                                    </button>
                                </div>
                            </div>
                            @error('rating')
                                <div class="text-danger small mt-1">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Comment -->
                        <div class="mb-4">
                            <label for="comment" class="form-label fw-bold">
                                <i class="fas fa-comment text-primary me-2"></i>Your Comment
                                <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control form-control-lg @error('comment') is-invalid @enderror"
                                      id="comment"
                                      name="comment"
                                      rows="6"
                                      placeholder="Share your experience, thoughts, or feedback about this event..."
                                      style="border-radius: 15px;">{{ old('comment', $eventComment->comment) }}</textarea>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Minimum 10 characters, maximum 1000 characters
                            </div>
                            @error('comment')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Admin Only: Approval Status -->
                        @if(Auth::user()->is_admin)
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-shield-alt text-danger me-2"></i>Approval Status
                                    <span class="badge bg-danger ms-2">Admin Only</span>
                                </label>
                                <div class="form-check form-switch" style="font-size: 1.2rem;">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="is_approved"
                                           name="is_approved"
                                           value="1"
                                           {{ old('is_approved', $eventComment->is_approved) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_approved">
                                        Approve this comment
                                    </label>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="d-flex gap-3 mt-4 flex-wrap">
                            <button type="submit" class="btn btn-primary btn-lg px-5 flex-fill" style="border-radius: 15px;">
                                <i class="fas fa-save me-2"></i>Update Comment
                            </button>
                            <a href="{{ route('event-comments.show', $eventComment) }}" class="btn btn-outline-secondary btn-lg px-4" style="border-radius: 15px;">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-lightbulb text-warning me-2"></i>Editing Tips
                            </h6>
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Make sure your comment is constructive
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Update your rating if your opinion changed
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Be respectful to organizers and attendees
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-info-circle text-info me-2"></i>Comment Info
                            </h6>
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-2">
                                    <i class="fas fa-user text-muted me-2"></i>
                                    <strong>Author:</strong> {{ $eventComment->user->name }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-calendar text-muted me-2"></i>
                                    <strong>Posted:</strong> {{ $eventComment->formatted_date }}
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-{{ $eventComment->is_approved ? 'check-circle text-success' : 'clock text-warning' }} me-2"></i>
                                    <strong>Status:</strong> {{ $eventComment->is_approved ? 'Approved' : 'Pending' }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    }
    .icon-box {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .rating-input-container {
        border: 2px solid #e9ecef;
    }
    .rating-radio {
        cursor: pointer;
    }
    .form-check-label {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .form-check-label:hover {
        transform: scale(1.1);
    }
    textarea.form-control {
        resize: vertical;
    }
    .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
    }
</style>
@endsection
