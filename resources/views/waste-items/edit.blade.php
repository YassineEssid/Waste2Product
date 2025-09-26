@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Waste Item
                    </h4>
                    <p class="mb-0 opacity-75">Update your item information</p>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('waste-items.update', $wasteItem) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label">Item Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $wasteItem->title) }}" 
                                       placeholder="e.g., Broken Chair, Old Laptop, Torn Jeans" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('category') is-invalid @enderror" 
                                        id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="electronics" {{ old('category', $wasteItem->category) == 'electronics' ? 'selected' : '' }}>Electronics</option>
                                    <option value="furniture" {{ old('category', $wasteItem->category) == 'furniture' ? 'selected' : '' }}>Furniture</option>
                                    <option value="clothing" {{ old('category', $wasteItem->category) == 'clothing' ? 'selected' : '' }}>Clothing</option>
                                    <option value="appliances" {{ old('category', $wasteItem->category) == 'appliances' ? 'selected' : '' }}>Appliances</option>
                                    <option value="toys" {{ old('category', $wasteItem->category) == 'toys' ? 'selected' : '' }}>Toys</option>
                                    <option value="books" {{ old('category', $wasteItem->category) == 'books' ? 'selected' : '' }}>Books</option>
                                    <option value="decorative" {{ old('category', $wasteItem->category) == 'decorative' ? 'selected' : '' }}>Decorative Items</option>
                                    <option value="tools" {{ old('category', $wasteItem->category) == 'tools' ? 'selected' : '' }}>Tools</option>
                                    <option value="other" {{ old('category', $wasteItem->category) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" required
                                      placeholder="Describe the item's current condition, what's wrong with it, and any other relevant details...">{{ old('description', $wasteItem->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="condition" class="form-label">Current Condition</label>
                                <select class="form-select @error('condition') is-invalid @enderror" 
                                        id="condition" name="condition">
                                    <option value="">Select Condition</option>
                                    <option value="poor" {{ old('condition', $wasteItem->condition) == 'poor' ? 'selected' : '' }}>Poor - Severely damaged</option>
                                    <option value="fair" {{ old('condition', $wasteItem->condition) == 'fair' ? 'selected' : '' }}>Fair - Some damage/wear</option>
                                    <option value="good" {{ old('condition', $wasteItem->condition) == 'good' ? 'selected' : '' }}>Good - Minor issues</option>
                                    <option value="excellent" {{ old('condition', $wasteItem->condition) == 'excellent' ? 'selected' : '' }}>Excellent - Like new</option>
                                </select>
                                @error('condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status">
                                    <option value="available" {{ old('status', $wasteItem->status) == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="claimed" {{ old('status', $wasteItem->status) == 'claimed' ? 'selected' : '' }}>Claimed</option>
                                    <option value="completed" {{ old('status', $wasteItem->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="unavailable" {{ old('status', $wasteItem->status) == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location" value="{{ old('location', $wasteItem->location) }}" 
                                       placeholder="City, State/Country">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="dimensions" class="form-label">Dimensions (optional)</label>
                                <input type="text" class="form-control @error('dimensions') is-invalid @enderror" 
                                       id="dimensions" name="dimensions" value="{{ old('dimensions', $wasteItem->dimensions) }}" 
                                       placeholder="e.g., 30cm x 20cm x 15cm">
                                @error('dimensions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="weight" class="form-label">Weight (optional)</label>
                            <input type="text" class="form-control @error('weight') is-invalid @enderror" 
                                   id="weight" name="weight" value="{{ old('weight', $wasteItem->weight) }}" 
                                   placeholder="e.g., 2.5 kg">
                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Images -->
                        @if($wasteItem->images && count($wasteItem->images) > 0)
                            <div class="mb-3">
                                <label class="form-label">Current Photos</label>
                                <div class="row" id="current-images">
                                    @foreach($wasteItem->images as $index => $image)
                                        <div class="col-md-3 col-6 mb-3" data-image-index="{{ $index }}">
                                            <div class="position-relative">
                                                <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded" style="height: 100px; object-fit: cover; width: 100%;">
                                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" 
                                                        onclick="removeImage({{ $index }})" style="padding: 2px 6px;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <input type="hidden" name="existing_images[]" value="{{ $image }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- New Images Upload -->
                        <div class="mb-4">
                            <label for="images" class="form-label">Add New Photos</label>
                            <input type="file" class="form-control @error('images') is-invalid @enderror" 
                                   id="images" name="images[]" multiple accept="image/*">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Upload additional photos (max 2MB each). Existing photos will be kept unless removed.
                            </div>
                            @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1" 
                                       {{ old('is_available', $wasteItem->is_available) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_available">
                                    <strong>Make this item available for claiming</strong>
                                    <br>
                                    <small class="text-muted">Others can claim this item for repair or transformation</small>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <div class="d-flex gap-2">
                                <a href="{{ route('waste-items.show', $wasteItem) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Cancel
                                </a>
                                <a href="{{ route('waste-items.index') }}" class="btn btn-outline-info">
                                    <i class="fas fa-list me-2"></i>All Items
                                </a>
                            </div>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Update Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Store removed images
let removedImages = [];

function removeImage(index) {
    const imageDiv = document.querySelector(`[data-image-index="${index}"]`);
    const imageInput = imageDiv.querySelector('input[name="existing_images[]"]');
    
    if (imageInput) {
        removedImages.push(imageInput.value);
        
        // Add hidden input for removed images
        const removedInput = document.createElement('input');
        removedInput.type = 'hidden';
        removedInput.name = 'removed_images[]';
        removedInput.value = imageInput.value;
        document.querySelector('form').appendChild(removedInput);
    }
    
    imageDiv.remove();
}

// Preview new uploaded images
document.getElementById('images').addEventListener('change', function(e) {
    const files = e.target.files;
    const existingPreview = document.getElementById('new-image-preview');
    
    if (existingPreview) {
        existingPreview.remove();
    }
    
    if (files.length > 0) {
        const previewContainer = document.createElement('div');
        previewContainer.id = 'new-image-preview';
        previewContainer.className = 'row mt-3';
        
        const label = document.createElement('div');
        label.className = 'col-12 mb-2';
        label.innerHTML = '<small class="text-muted"><strong>New photos to be uploaded:</strong></small>';
        previewContainer.appendChild(label);
        
        for (let i = 0; i < Math.min(files.length, 5); i++) {
            const file = files[i];
            if (file.type.startsWith('image/')) {
                const col = document.createElement('div');
                col.className = 'col-md-3 col-6 mb-3';
                
                const img = document.createElement('img');
                img.className = 'img-fluid rounded';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.style.width = '100%';
                img.src = URL.createObjectURL(file);
                
                col.appendChild(img);
                previewContainer.appendChild(col);
            }
        }
        
        document.getElementById('images').parentNode.appendChild(previewContainer);
    }
});
</script>
@endpush
@endsection