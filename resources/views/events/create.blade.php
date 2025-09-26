@extends('layouts.app')

@section('title', 'Create Community Event')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="create-header text-center mb-5">
                <div class="create-icon mb-3">
                    <i class="fas fa-calendar-plus fa-3x text-primary"></i>
                </div>
                <h1 class="display-5 fw-bold mb-3">Create Community Event</h1>
                <p class="text-muted">Organize an eco-friendly event and bring your community together for a sustainable cause.</p>
            </div>

            <!-- Form Card -->
            <div class="form-card">
                <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data" id="eventForm">
                    @csrf
                    
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
                                       value="{{ old('title') }}" 
                                       placeholder="e.g., Community Garden Workshop"
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
                                        <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
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
                                       value="{{ old('max_participants') }}" 
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
                                          placeholder="Describe your event, what participants will do, what they'll learn..."
                                          required>{{ old('description') }}</textarea>
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
                                       value="{{ old('event_date') }}" 
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
                                       value="{{ old('end_date') }}" 
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
                                           {{ old('is_virtual') ? 'checked' : '' }}>
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
                                       value="{{ old('location') }}" 
                                       placeholder="e.g., Central Park Community Center, 123 Main St"
                                       required>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    For virtual events, you can provide meeting platform details
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Image -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fas fa-image me-2"></i>Event Image
                        </h4>
                        
                        <div class="image-upload-area" id="imageUploadArea">
                            <input type="file" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   name="image" 
                                   id="image" 
                                   accept="image/*"
                                   hidden>
                            
                            <div class="upload-placeholder" id="uploadPlaceholder">
                                <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                                <h5>Upload Event Image</h5>
                                <p class="text-muted mb-3">Click to browse or drag and drop your image here</p>
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('image').click()">
                                    Choose Image
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
                            Recommended size: 800x400px. Max file size: 2MB. Formats: JPEG, PNG, JPG, GIF
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-calendar-plus me-2"></i>Create Event
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.create-header {
    padding: 2rem 0;
    background: linear-gradient(135deg, #f8f9ff 0%, #e8f0ff 100%);
    border-radius: 20px;
    margin: 2rem 0;
}

.create-icon {
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

@media (max-width: 768px) {
    .form-card {
        padding: 1.5rem;
        border-radius: 15px;
    }
    
    .create-header {
        border-radius: 15px;
        padding: 1.5rem;
    }
    
    .form-actions .d-flex {
        flex-direction: column;
        gap: 1rem;
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
    const locationField = document.getElementById('locationField');
    const locationInput = locationField.querySelector('input');
    
    isVirtualCheckbox.addEventListener('change', function() {
        if (this.checked) {
            locationInput.placeholder = 'e.g., Zoom Meeting, Google Meet link, etc.';
        } else {
            locationInput.placeholder = 'e.g., Central Park Community Center, 123 Main St';
        }
    });
    
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