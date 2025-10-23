@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Add New Waste Item
                    </h4>
                    <p class="mb-0 opacity-75">Share an item that can be repaired or transformed</p>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('waste-items.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label">Item Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" 
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
                                    <option value="electronics" {{ old('category') == 'electronics' ? 'selected' : '' }}>Electronics</option>
                                    <option value="furniture" {{ old('category') == 'furniture' ? 'selected' : '' }}>Furniture</option>
                                    <option value="clothing" {{ old('category') == 'clothing' ? 'selected' : '' }}>Clothing</option>
                                    <option value="appliances" {{ old('category') == 'appliances' ? 'selected' : '' }}>Appliances</option>
                                    <option value="toys" {{ old('category') == 'toys' ? 'selected' : '' }}>Toys</option>
                                    <option value="books" {{ old('category') == 'books' ? 'selected' : '' }}>Books</option>
                                    <option value="decorative" {{ old('category') == 'decorative' ? 'selected' : '' }}>Decorative Items</option>
                                    <option value="tools" {{ old('category') == 'tools' ? 'selected' : '' }}>Tools</option>
                                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
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
                                      placeholder="Describe the item's current condition, what's wrong with it, and any other relevant details...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                       id="quantity" name="quantity" min="1" value="{{ old('quantity', 1) }}" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="condition" class="form-label">Current Condition <span class="text-danger">*</span></label>
                                <select class="form-select @error('condition') is-invalid @enderror" 
                                        id="condition" name="condition" required>
                                    <option value="">Select Condition</option>
                                    <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>Poor - Severely damaged</option>
                                    <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Fair - Some damage/wear</option>
                                    <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Good - Minor issues</option>
                                    <option value="excellent" {{ old('condition') == 'excellent' ? 'selected' : '' }}>Excellent - Like new</option>
                                </select>
                                @error('condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="location_address" class="form-label">Location (Address)</label>
                            <input type="text" class="form-control @error('location_address') is-invalid @enderror" 
                                   id="location_address" name="location_address" 
                                   value="{{ old('location_address') }}" placeholder="City, State, or Country">
                            @error('location_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Optional hidden fields for lat/lng --}}
                        <input type="hidden" name="location_lat" id="location_lat" value="{{ old('location_lat') }}">
                        <input type="hidden" name="location_lng" id="location_lng" value="{{ old('location_lng') }}">

                        <div class="mb-4">
                            <label for="images" class="form-label">Photos</label>
                            <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                   id="images" name="images[]" multiple accept="image/*">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Upload up to 5 photos (JPG, PNG, GIF — max 2MB each)
                            </div>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1" checked>
                                <label class="form-check-label" for="is_available">
                                    <strong>Make this item available for claiming</strong><br>
                                    <small class="text-muted">Others can claim this item for repair or transformation</small>
                                </label>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-lightbulb me-2"></i>
                            <strong>Tips for better results:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Take clear photos from multiple angles</li>
                                <li>Describe the issue in detail</li>
                                <li>Mention if you have any spare parts or accessories</li>
                                <li>Be honest about the item's condition</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('waste-items.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-save me-2"></i>Share Item
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
// Limit image upload preview to 5
document.getElementById('images').addEventListener('change', function(e) {
    const files = e.target.files;
    const preview = document.getElementById('image-preview');
    
    if (preview) preview.remove();
    
    if (files.length > 0) {
        if (files.length > 5) {
            alert('You can upload a maximum of 5 images.');
        }

        const previewContainer = document.createElement('div');
        previewContainer.id = 'image-preview';
        previewContainer.className = 'row mt-3';
        
        for (let i = 0; i < Math.min(files.length, 5); i++) {
            const file = files[i];
            if (file.type.startsWith('image/')) {
                const col = document.createElement('div');
                col.className = 'col-md-3 col-6 mb-3';
                const img = document.createElement('img');
                img.className = 'img-fluid rounded';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.src = URL.createObjectURL(file);
                col.appendChild(img);
                previewContainer.appendChild(col);
            }
        }
        document.getElementById('images').parentNode.appendChild(previewContainer);
    }
});

// Auto-fill user address if available
@if(auth()->user()->address)
    document.getElementById('location_address').value = '{{ auth()->user()->address }}';
@endif
</script>
@endpush
@endsection
