@extends('layouts.app')

@section('title', 'Edit Event - ' . $event->title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Edit Event
                        </h4>
                        <a href="{{ route('events.show', $event) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to Event
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('events.update', $event) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Basic Information
                                </h5>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label required">Event Title</label>
                                <input type="text" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       name="title" 
                                       value="{{ old('title', $event->title) }}" 
                                       required 
                                       placeholder="Enter event title">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label required">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          name="description" 
                                          rows="4" 
                                          required 
                                          placeholder="Describe your event...">{{ old('description', $event->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Date & Time -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-calendar-alt me-2"></i>Date & Time
                                </h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Start Date & Time</label>
                                <input type="datetime-local" 
                                       class="form-control @error('event_date') is-invalid @enderror" 
                                       name="event_date" 
                                       value="{{ old('event_date', $event->starts_at ? $event->starts_at->format('Y-m-d\TH:i') : '') }}" 
                                       required>
                                @error('event_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">End Date & Time</label>
                                <input type="datetime-local" 
                                       class="form-control @error('end_date') is-invalid @enderror" 
                                       name="end_date" 
                                       value="{{ old('end_date', $event->ends_at ? $event->ends_at->format('Y-m-d\TH:i') : '') }}" 
                                       required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Location & Details -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i>Location & Details
                                </h5>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" 
                                       class="form-control @error('location') is-invalid @enderror" 
                                       name="location" 
                                       value="{{ old('location', $event->location ?? '') }}" 
                                       placeholder="Enter event location or address">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Leave empty if location is not specified
                                </small>
                            </div>
                        </div>

                        <!-- Event Image -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-image me-2"></i>Image de l'événement
                                </h5>
                            </div>

                            <div class="col-md-12 mb-3">
                                @if($event->image)
                                <div class="mb-3">
                                    <label class="form-label">Image actuelle</label>
                                    <div class="position-relative d-inline-block">
                                        <img src="{{ Storage::url($event->image) }}" 
                                             alt="{{ $event->title }}" 
                                             class="img-thumbnail" 
                                             style="max-width: 300px; max-height: 200px;">
                                    </div>
                                </div>
                                @endif

                                <label class="form-label">{{ $event->image ? 'Changer l\'image' : 'Ajouter une image' }}</label>
                                <input type="file" 
                                       class="form-control @error('image') is-invalid @enderror" 
                                       name="image" 
                                       id="image"
                                       accept="image/*"
                                       onchange="previewImage(event)">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Formats acceptés : JPEG, PNG, JPG, GIF (Max : 2MB)
                                </small>
                                
                                <div id="imagePreview" class="mt-3" style="display: none;">
                                    <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px; max-height: 200px;">
                                </div>
                            </div>
                        </div>

                        <!-- Event Status -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-cog me-2"></i>Event Settings
                                </h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Event Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status">
                                    <option value="published" {{ old('status', $event->status) == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="draft" {{ old('status', $event->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="cancelled" {{ old('status', $event->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Maximum Participants</label>
                                <input type="number" 
                                       class="form-control @error('max_participants') is-invalid @enderror" 
                                       name="max_participants" 
                                       value="{{ old('max_participants', $event->max_participants ?? '') }}" 
                                       min="0" 
                                       placeholder="Leave empty for unlimited">
                                @error('max_participants')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Update Event
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Event Form -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="border-top pt-4">
                                <h6 class="text-danger mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                                </h6>
                                <form method="POST" action="{{ route('events.destroy', $event) }}" 
                                      onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="fas fa-trash me-1"></i>Delete Event
                                    </button>
                                    <small class="text-muted ms-2">This will permanently delete the event and all associated data.</small>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.required::after {
    content: " *";
    color: #dc3545;
}

.card {
    border: none;
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.form-control, .form-select {
    border-radius: 10px;
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn {
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.border-bottom {
    border-color: #e9ecef !important;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

@media (max-width: 768px) {
    .card-body {
        padding: 1.5rem !important;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .d-flex.gap-2 {
        flex-direction: column;
    }
}
</style>

<script>
// Preview image before upload
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = 'none';
    }
}

// Client-side validation for date times
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.querySelector('input[name="event_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    
    function validateDates() {
        if (startDateInput.value && endDateInput.value) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            
            if (endDate <= startDate) {
                endDateInput.setCustomValidity('End date must be after start date');
            } else {
                endDateInput.setCustomValidity('');
            }
        }
    }
    
    startDateInput.addEventListener('change', validateDates);
    endDateInput.addEventListener('change', validateDates);
});

// Image preview function
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('preview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endsection