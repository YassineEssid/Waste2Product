@extends('layouts.app')

@section('title', 'Edit ' . $marketplace->title . ' - Marketplace')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="create-header text-center mb-5">
                <div class="create-icon mb-3">
                    <i class="fas fa-edit fa-3x text-success"></i>
                </div>
                <h1 class="display-5 fw-bold mb-3">Edit Your Item</h1>
                <p class="text-muted">Update your item details and photos.</p>
            </div>

            <!-- Form Card -->
            <div class="form-card">
                <form method="POST" action="{{ route('marketplace.update', $marketplace) }}" enctype="multipart/form-data" id="itemForm">
                    @csrf
                    @method('PUT')

                    <!-- Basic Information -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fas fa-info-circle me-2"></i>Item Information
                        </h4>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label required">Item Title</label>
                                <input type="text"
                                    class="form-control @error('title') is-invalid @enderror"
                                    name="title"
                                    value="{{ old('title', $marketplace->title) }}"
                                    placeholder="e.g., Handcrafted Wooden Coffee Table"
                                    required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Category</label>
                                <select class="form-select @error('category') is-invalid @enderror" name="category" required>
                                    <option value="">Choose category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ old('category', $marketplace->category) == $category ? 'selected' : '' }}>
                                            {{ ucfirst($category) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Condition</label>
                                <select class="form-select @error('condition') is-invalid @enderror" name="condition" required>
                                    <option value="">Select condition</option>
                                    @foreach($conditions as $condition)
                                        <option value="{{ $condition }}" {{ old('condition', $marketplace->condition) == $condition ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $condition)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label required">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          name="description"
                                          rows="4"
                                          placeholder="Describe your item, its story, materials used, dimensions, special features..."
                                          required>{{ old('description', $marketplace->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fas fa-dollar-sign me-2"></i>Pricing
                        </h4>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Price (USD)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number"
                                        class="form-control @error('price') is-invalid @enderror"
                                        name="price"
                                        value="{{ old('price', $marketplace->price) }}"
                                        min="0"
                                        step="0.01"
                                        placeholder="0.00"
                                        required>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Quantity</label>
                                <input type="number"
                                    class="form-control @error('quantity') is-invalid @enderror"
                                    name="quantity"
                                    value="{{ old('quantity', $marketplace->quantity ?? 1) }}"
                                    min="1"
                                    placeholder="1">
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label required">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                    <option value="available" {{ old('status', $marketplace->status) == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="sold" {{ old('status', $marketplace->status) == 'sold' ? 'selected' : '' }}>Sold</option>
                                    <option value="reserved" {{ old('status', $marketplace->status) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Additional Options -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fas fa-cog me-2"></i>Additional Options
                        </h4>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="is_negotiable" 
                                           id="is_negotiable" 
                                           value="1"
                                           {{ old('is_negotiable', $marketplace->is_negotiable) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_negotiable">
                                        <i class="fas fa-handshake me-2"></i>Price is negotiable
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="is_featured" 
                                           id="is_featured" 
                                           value="1"
                                           {{ old('is_featured', $marketplace->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        <i class="fas fa-star me-2"></i>Feature this item
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Images -->
                    @if($marketplace->images && $marketplace->images->count() > 0)
                        <div class="form-section">
                            <h4 class="section-title">
                                <i class="fas fa-images me-2"></i>Current Photos
                            </h4>
                            <div class="current-images-grid mb-3">
                                @foreach($marketplace->images as $image)
                                    <div class="current-image-item">
                                        <img src="{{ Storage::url($image->image_path) }}" alt="Current image">
                                        <div class="image-overlay">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       name="delete_images[]"
                                                       value="{{ $image->id }}"
                                                       id="delete_{{ $image->id }}">
                                                <label class="form-check-label text-white" for="delete_{{ $image->id }}">
                                                    <i class="fas fa-trash"></i> Delete
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-text mb-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Check the boxes above to delete images. Add new images below to replace them.
                            </div>
                        </div>
                    @endif

                    <!-- Add New Images -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fas fa-plus me-2"></i>Add New Images
                        </h4>

                        <div class="image-upload-container">
                            <input type="file"
                                   class="form-control @error('images.*') is-invalid @enderror"
                                   name="images[]"
                                   id="images"
                                   accept="image/*"
                                   multiple
                                   hidden>
                            <div class="upload-area" id="uploadArea">
                                <div class="upload-placeholder text-center" id="uploadPlaceholder">
                                    <i class="fas fa-cloud-upload-alt fa-4x mb-3 text-primary"></i>
                                    <h5>Upload New Photos</h5>
                                    <p class="text-muted mb-3">Drag and drop images here, or click to browse</p>
                                    <p class="text-muted small mb-3">You can upload multiple images (Max 5 images, 2MB each)</p>
                                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('images').click()">
                                        <i class="fas fa-image me-2"></i>Choose Images
                                    </button>
                                </div>
                                <div class="image-previews" id="imagePreviews" style="display: none;">
                                    <div class="previews-grid" id="previewsGrid">
                                        <!-- New image previews will be inserted here -->
                                    </div>
                                    <div class="upload-actions text-center mt-3">
                                        <button type="button" class="btn btn-outline-primary me-2" onclick="document.getElementById('images').click()">
                                            <i class="fas fa-plus me-1"></i>Add More
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" onclick="clearAllImages()">
                                            <i class="fas fa-trash me-1"></i>Clear All
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('images.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('marketplace.show', $marketplace) }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-lg" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash me-2"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@can('delete', $marketplace)
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Delete Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this item from the marketplace?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. Your listing and all associated images will be permanently deleted.
                </div>
                <div class="item-preview">
                    <h6>{{ $marketplace->title }}</h6>
                    <small class="text-muted">
                        ${{ number_format($marketplace->price, 2) }} • {{ $marketplace->category }}
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('marketplace.destroy', $marketplace) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete Item
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan

<style>
.create-header {
    padding: 2rem 0;
    background: linear-gradient(135deg, #f0fff4 0%, #e6ffef 100%);
    border-radius: 20px;
    margin: 2rem 0;
}

.create-icon {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.upload-area {
    border: 3px dashed #dee2e6;
    border-radius: 15px;
    padding: 2rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-area:hover,
.upload-area.dragover {
    border-color: #28a745;
    background-color: #f0fff4;
}

.current-images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.current-image-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.current-image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.current-image-item:hover .image-overlay {
    opacity: 1;
}

.previews-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.preview-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-overlay {
    position: absolute;
    top: 5px;
    right: 5px;
}

.remove-image {
    background: rgba(220, 53, 69, 0.9);
    border: none;
    color: white;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.remove-image:hover {
    background: #dc3545;
    transform: scale(1.1);
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

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.6);
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
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

    .current-images-grid,
    .previews-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }
}
</style>

<script>
let selectedFiles = [];

document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('images');
    const uploadArea = document.getElementById('uploadArea');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const imagePreviews = document.getElementById('imagePreviews');
    const previewsGrid = document.getElementById('previewsGrid');

    // File input change handler
    imageInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });

    // Drag and drop handlers
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });

    // Handle file selection
    function handleFiles(files) {
        const maxFiles = 5;
        const maxSize = 2 * 1024 * 1024; // 2MB

        // Convert FileList to Array and filter
        const newFiles = Array.from(files).filter(file => {
            if (!file.type.startsWith('image/')) {
                alert('Please select only image files');
                return false;
            }
            if (file.size > maxSize) {
                alert(`File ${file.name} is too large. Max size is 2MB`);
                return false;
            }
            return true;
        });
        
        // Check total file count
        if (selectedFiles.length + newFiles.length > maxFiles) {
            alert(`You can only upload up to ${maxFiles} images`);
            return;
        }
        
        // Add new files to selection
        selectedFiles = [...selectedFiles, ...newFiles];
        updateImagePreviews();
        updateFileInput();
    }
    
    // Update preview display
    function updateImagePreviews() {
        previewsGrid.innerHTML = '';
        
        if (selectedFiles.length === 0) {
            uploadPlaceholder.style.display = 'block';
            imagePreviews.style.display = 'none';
            return;
        }
        
        uploadPlaceholder.style.display = 'none';
        imagePreviews.style.display = 'block';
        
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                previewItem.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <div class="preview-overlay">
                        <button type="button" class="remove-image" onclick="removeImage(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                previewsGrid.appendChild(previewItem);
            };
            reader.readAsDataURL(file);
        });
    }
    
    // Update file input with selected files
    function updateFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        imageInput.files = dt.files;
    }
    
    // Global functions
    window.removeImage = function(index) {
        selectedFiles.splice(index, 1);
        updateImagePreviews();
        updateFileInput();
    };
    
    window.clearAllImages = function() {
        selectedFiles = [];
        updateImagePreviews();
        updateFileInput();
    };
    
    // Form validation
    document.getElementById('itemForm').addEventListener('submit', function(e) {
        const price = parseFloat(document.querySelector('input[name="price"]').value);
        const quantity = parseInt(document.querySelector('input[name="quantity"]').value) || 1;
        
        if (price < 0) {
            e.preventDefault();
            alert('Price cannot be negative');
            return false;
        }
        
        if (quantity < 1) {
            e.preventDefault();
            alert('Quantity must be at least 1');
            return false;
        }
    });
});
</script>
@endsection