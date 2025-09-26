@extends('layouts.app')

@section('title', 'Edit Event: ' . $event->title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="edit-header text-center mb-5">
                <div class="edit-icon mb-3">
                    <i class="fas fa-edit fa-3x text-primary"></i>
                </div>
                <h1 class="display-5 fw-bold mb-3">Edit Event</h1>
                <p class="text-muted">Update your community event details and settings.</p>
            </div>

            <!-- Form Card -->
            <div class="form-card">
                <form method="POST" action="{{ route('events.update', $event) }}" enctype="multipart/form-data" id="eventForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fas fa-info-circle me-2"></i>Basic Information
                        </h4>
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label required">Event Title</label>
                                <input type="text" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       name="title" 
                                       value="{{ old('title', $event->title) }}" 
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Event Type</label>
                                <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                                    <option value="">Choose event type</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" {{ old('type', $event->type) == $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Maximum Participants</label>
                                <input type="number" 
                                       class="form-control @error('max_participants') is-invalid @enderror" 
                                       name="max_participants" 
                                       value="{{ old('max_participants', $event->max_participants) }}" 
                                       min="1"
                                       placeholder="Leave empty for unlimited">
                                @error('max_participants')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label required">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          name="description" 
                                          rows="4" 
                                          required>{{ old('description', $event->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Date & Location -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fas fa-calendar-clock me-2"></i>Date & Location
                        </h4>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Start Date & Time</label>
                                <input type="datetime-local" 
                                       class="form-control @error('event_date') is-invalid @enderror" 
                                       name="event_date" 
                                       value="{{ old('event_date', $event->event_date->format('Y-m-d\TH:i')) }}" 
                                       required>
                                @error('event_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">End Date & Time</label>
                                <input type="datetime-local" 
                                       class="form-control @error('end_date') is-invalid @enderror" 
                                       name="end_date" 
                                       value="{{ old('end_date', $event->end_date->format('Y-m-d\TH:i')) }}" 
                                       required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="is_virtual" 
                                           id="is_virtual" 
                                           value="1" 
                                           {{ old('is_virtual', $event->is_virtual) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_virtual">
                                        <i class="fas fa-video me-2"></i>This is a virtual event
                                    </label>
                                </div>
                            </div>

                            <div class="col-12" id="locationField">
                                <label class="form-label required">Location</label>
                                <input type="text" 
                                       class="form-control @error('location') is-invalid @enderror" 
                                       name="location" 
                                       value="{{ old('location', $event->location) }}" 
                                       required>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Event Image -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fas fa-image me-2"></i>Event Image
                        </h4>
                        
                        <!-- Current Image -->
                        @if($event->image)
                            <div class="current-image-section mb-3">
                                <label class="form-label">Current Image</label>
                                <div class="current-image">
                                    <img src="{{ Storage::url($event->image) }}" alt="Current event image" class="img-fluid rounded">
                                    <div class="image-overlay">
                                        <span class="badge bg-primary">Current Image</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="image-upload-area" id="imageUploadArea">
                            <input type="file" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   name="image" 
                                   id="image" 
                                   accept="image/*"
                                   hidden>
                            
                            <div class="upload-placeholder" id="uploadPlaceholder">
                                <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                                <h5>{{ $event->image ? 'Upload New Image' : 'Upload Event Image' }}</h5>
                                <p class="text-muted mb-3">Click to browse or drag and drop your image here</p>
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('image').click()">
                                    Choose {{ $event->image ? 'New ' : '' }}Image
                                </button>
                            </div>
                            
                            <div class="image-preview" id="imagePreview" style="display: none;">
                                <img id="previewImg" src="" alt="Preview">
                                <div class="image-overlay">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeImage()">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Leave empty to keep current image. Upload new image to replace.
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex gap-2">
                                <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Event
                                </a>
                                @can('delete', $event)
                                    <button type="button" class="btn btn-outline-danger btn-lg" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </button>
                                @endcan
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@can('delete', $event)
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Delete Event
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this event?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. All event data and participant registrations will be permanently deleted.
                </div>
                <div class="event-preview">
                    <h6>{{ $event->title }}</h6>
                    <small class="text-muted">
                        {{ $event->event_date->format('M j, Y • g:i A') }} • {{ $event->attendees->count() }} participants
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('events.destroy', $event) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete Event
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan

<style>
.edit-header {
    padding: 2rem 0;
    background: linear-gradient(135deg, #f8f9ff 0%, #e8f0ff 100%);
    border-radius: 20px;
    margin: 2rem 0;
}

.edit-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.form-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    margin-bottom: 2rem;
}

.form-section {
    margin-bottom: 2.5rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #eee;
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.section-title {
    color: #2c3e50;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.form-label.required::after {
    content: ' *';
    color: #e74c3c;
}

.form-control, .form-select {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.current-image-section {
    margin-bottom: 1.5rem;
}

.current-image {
    position: relative;
    max-width: 400px;
    margin: 0.5rem 0;
}

.current-image img {
    max-height: 200px;
    width: 100%;
    object-fit: cover;
}

.image-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
}

.image-upload-area:hover {
    border-color: #667eea;
    background-color: #f8f9ff;
}

.image-preview {
    position: relative;
    max-width: 400px;
    margin: 0 auto;
}

.image-preview img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 10px;
}

.image-overlay {
    position: absolute;
    top: 10px;
    right: 10px;
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.form-actions {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #eee;
}

.btn-lg {
    padding: 0.75rem 2rem;
    border-radius: 25px;
    font-weight: 600;
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

.event-preview {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .form-card {
        padding: 1.5rem;
        border-radius: 15px;
    }
    
    .edit-header {
        border-radius: 15px;
        padding: 1.5rem;
    }
    
    .form-actions .d-flex {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-actions .d-flex:first-child {
        flex-direction: row;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview functionality
    const imageInput = document.getElementById('image');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                uploadPlaceholder.style.display = 'none';
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Virtual event toggle
    const isVirtualCheckbox = document.getElementById('is_virtual');
    const locationInput = document.querySelector('input[name="location"]');
    
    function updateLocationPlaceholder() {
        if (isVirtualCheckbox.checked) {
            locationInput.placeholder = 'e.g., Zoom Meeting, Google Meet link, etc.';
        } else {
            locationInput.placeholder = 'e.g., Central Park Community Center, 123 Main St';
        }
    }
    
    isVirtualCheckbox.addEventListener('change', updateLocationPlaceholder);
    updateLocationPlaceholder(); // Set initial placeholder
    
    // Form validation
    document.getElementById('eventForm').addEventListener('submit', function(e) {
        const startDate = new Date(document.querySelector('input[name="event_date"]').value);
        const endDate = new Date(document.querySelector('input[name="end_date"]').value);
        
        if (endDate <= startDate) {
            e.preventDefault();
            alert('End date must be after start date');
            return false;
        }
    });
});

function removeImage() {
    document.getElementById('image').value = '';
    document.getElementById('uploadPlaceholder').style.display = 'block';
    document.getElementById('imagePreview').style.display = 'none';
}

// Drag and drop functionality
const uploadArea = document.getElementById('imageUploadArea');

uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    uploadArea.style.borderColor = '#667eea';
    uploadArea.style.backgroundColor = '#f8f9ff';
});

uploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    uploadArea.style.borderColor = '#dee2e6';
    uploadArea.style.backgroundColor = '';
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    uploadArea.style.borderColor = '#dee2e6';
    uploadArea.style.backgroundColor = '';
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('image').files = files;
        document.getElementById('image').dispatchEvent(new Event('change'));
    }
});
</script>
@endsection