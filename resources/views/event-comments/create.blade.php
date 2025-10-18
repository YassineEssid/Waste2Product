@extends('layouts.app')

@section('title', 'Add Comment')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header Card -->
            <div class="card border-0 shadow-lg mb-4" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-gradient-success text-white py-4 border-0">
                    <div class="d-flex align-items-center">
                        <div class="icon-box me-3">
                            <i class="fas fa-comment-dots fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold">Add New Comment</h3>
                            <p class="mb-0 opacity-75">Share your thoughts about an event</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if($event)
                        <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 15px;">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-info-circle fa-2x me-3 text-info"></i>
                                <div>
                                    <h6 class="mb-2 fw-bold">Commenting on:</h6>
                                    <p class="mb-0">{{ $event->title }}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $event->starts_at->format('d M Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('event-comments.store') }}" method="POST">
                        @csrf

                        <!-- Event Selection -->
                        <div class="mb-4">
                            <label for="community_event_id" class="form-label fw-bold">
                                <i class="fas fa-calendar-alt text-success me-2"></i>Select Event
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg @error('community_event_id') is-invalid @enderror"
                                    id="community_event_id"
                                    name="community_event_id"
                                    {{ $event ? 'readonly' : '' }}
                                    style="border-radius: 15px;">
                                <option value="">Choose an event...</option>
                                @foreach($events as $eventOption)
                                    <option value="{{ $eventOption->id }}"
                                            {{ (old('community_event_id', $event->id ?? null) == $eventOption->id) ? 'selected' : '' }}>
                                        {{ $eventOption->title }}
                                        - {{ $eventOption->starts_at->format('d M Y') }}
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
                                <div class="d-flex align-items-center gap-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <div class="form-check">
                                            <input class="form-check-input rating-radio"
                                                   type="radio"
                                                   name="rating"
                                                   id="rating{{ $i }}"
                                                   value="{{ $i }}"
                                                   {{ old('rating') == $i ? 'checked' : '' }}>
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
                                        Clear
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
                                <i class="fas fa-comment text-success me-2"></i>Your Comment
                                <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control form-control-lg @error('comment') is-invalid @enderror"
                                      id="comment"
                                      name="comment"
                                      rows="6"
                                      placeholder="Share your experience, thoughts, or feedback about this event..."
                                      style="border-radius: 15px;">{{ old('comment') }}</textarea>
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

                        <!-- Action Buttons -->
                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-success btn-lg px-5 flex-fill" style="border-radius: 15px;">
                                <i class="fas fa-paper-plane me-2"></i>Submit Comment
                            </button>
                            <a href="{{ route('event-comments.index') }}" class="btn btn-outline-secondary btn-lg px-4" style="border-radius: 15px;">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-lightbulb text-warning me-2"></i>Tips for Great Comments
                    </h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Be specific about what you liked or disliked
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Share constructive feedback to help improve future events
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Be respectful and considerate of others
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Rate honestly based on your experience
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
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
</style>
@endsection
