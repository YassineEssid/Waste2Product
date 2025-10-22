@extends('layouts.app')

@section('title', 'Create Repair Request')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="create-header text-center mb-5">
                <div class="create-icon mb-3">
                    <i class="fas fa-tools fa-3x text-primary"></i>
                </div>
                <h1 class="display-5 fw-bold mb-3">Create Repair Request</h1>
                <p class="text-muted">Request a repair for your waste item and help give it a second life.</p>
            </div>

            <!-- Form Card -->
            <div class="form-card">
                <!-- Display validation errors -->
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>There were some problems with your input:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Display success or error flash messages -->
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

                <form method="POST" action="{{ route('repairs.store') }}" enctype="multipart/form-data" id="repairForm">
                    @csrf
                    <!-- Title -->
<div class="form-section">
    <h4 class="section-title"><i class="fas fa-heading me-2"></i>Repair Request Title</h4>
    <div class="mb-3">
        <label class="form-label required">Title</label>
        <input type="text" name="title" 
               class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title') }}"
               placeholder="Enter repair request title"
               required>
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>


                    <!-- Waste Item Selection -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-box me-2"></i>Select Waste Item</h4>
                        <div class="mb-3">
                            <label class="form-label required">Item</label>
                            <select name="waste_item_id" class="form-select @error('waste_item_id') is-invalid @enderror" required>
                                <option value="">Select your item</option>
                                @foreach($wasteItems as $item)
                                    <option value="{{ $item->id }}" {{ old('waste_item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->title }} ({{ ucfirst($item->category) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('waste_item_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Repair Details -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-info-circle me-2"></i>Repair Details</h4>
                        <div class="mb-3">
                            <label class="form-label required">Description of Issue</label>
                            <textarea name="description" rows="4" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Explain the problem with the item" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Urgency</label>
                                <select name="urgency" class="form-select @error('urgency') is-invalid @enderror" required>
                                    <option value="">Select urgency</option>
                                    @foreach(['low', 'medium', 'high'] as $level)
                                        <option value="{{ $level }}" {{ old('urgency') == $level ? 'selected' : '' }}>
                                            {{ ucfirst($level) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('urgency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Budget (optional)</label>
                                <input type="number" name="budget" min="0"
                                       class="form-control @error('budget') is-invalid @enderror"
                                       value="{{ old('budget') }}"
                                       placeholder="Estimated cost if known">
                                @error('budget')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>


                    <!-- Form Actions -->
                    <div class="form-actions">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('repairs.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-tools me-2"></i>Create Repair Request
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Reuse the same styling from events.create for consistency */
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
}

.image-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('beforeImages');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const imagePreview = document.getElementById('imagePreview');

    imageInput.addEventListener('change', function(e) {
        imagePreview.innerHTML = '';
        if (e.target.files.length > 0) {
            Array.from(e.target.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';
                    img.style.margin = '5px';
                    img.style.borderRadius = '5px';
                    imagePreview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
            uploadPlaceholder.style.display = 'none';
            imagePreview.style.display = 'flex';
        }
    });

    // Drag and drop functionality
    const uploadArea = document.getElementById('imageUploadArea');
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.style.borderColor = '#667eea';
    });
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.style.borderColor = '#dee2e6';
    });
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.style.borderColor = '#dee2e6';
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            imageInput.files = files;
            imageInput.dispatchEvent(new Event('change'));
        }
    });
});
</script>
@endsection
