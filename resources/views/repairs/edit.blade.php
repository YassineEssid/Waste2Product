@extends('layouts.app')

@section('title', 'Edit Repair - ' . $repair->title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-tools me-2"></i>Edit Repair
                        </h4>
                        <a href="{{ route('repairs.show', $repair) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to Repair
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('repairs.update', $repair) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Waste Item -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-box me-2"></i>Item Details
                                </h5>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required">Waste Item</label>
                                <select name="waste_item_id" class="form-select @error('waste_item_id') is-invalid @enderror" required>
                                    @foreach($wasteItems as $item)
                                        <option value="{{ $item->id }}" {{ old('waste_item_id', $repair->waste_item_id) == $item->id ? 'selected' : '' }}>
                                            {{ $item->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('waste_item_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-align-left me-2"></i>Description
                                </h5>
                            </div>
                            <div class="col-12 mb-3">
                                <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" required placeholder="Describe the repair...">{{ old('description', $repair->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Preferred Date & Urgency -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Preferred Date</label>
                                <input type="date" name="preferred_date" class="form-control @error('preferred_date') is-invalid @enderror"
                                       value="{{ old('preferred_date', $repair->preferred_date ? $repair->preferred_date->format('Y-m-d') : '') }}">
                                @error('preferred_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Urgency</label>
                                <select name="urgency" class="form-select @error('urgency') is-invalid @enderror" required>
                                    <option value="low" {{ old('urgency', $repair->urgency) == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('urgency', $repair->urgency) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('urgency', $repair->urgency) == 'high' ? 'selected' : '' }}>High</option>
                                </select>
                                @error('urgency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Budget -->
                        <div class="row mb-4">
                            <div class="col-12 mb-3">
                                <label class="form-label">Budget (Optional)</label>
                                <input type="number" step="0.01" min="0" name="budget" class="form-control @error('budget') is-invalid @enderror"
                                       value="{{ old('budget', $repair->budget) }}" placeholder="Enter budget if any">
                                @error('budget')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Before Images -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-camera me-2"></i>Before Images
                                </h5>
                            </div>
                            <div class="col-12 mb-3">
                                @if($repair->before_images)
                                    <div class="mb-3">
                                        <label class="form-label">Existing Images</label>
                                        <div class="d-flex gap-2 flex-wrap">
                                            @foreach(json_decode($repair->before_images, true) as $img)
                                                <img src="{{ Storage::url($img) }}" alt="Before Image" class="img-thumbnail" style="max-width: 150px; max-height: 120px;">
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <label class="form-label">{{ $repair->before_images ? 'Change Images' : 'Add Images' }}</label>
                                <input type="file" name="before_images[]" class="form-control @error('before_images.*') is-invalid @enderror" multiple accept="image/*">
                                @error('before_images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Max 2MB per image. Allowed: jpeg, png, jpg, gif</small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('repairs.show', $repair) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-info text-white">
                                        <i class="fas fa-save me-1"></i>Update Repair
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Repair -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="border-top pt-4">
                                <h6 class="text-danger mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                                </h6>
                                <form method="POST" action="{{ route('repairs.destroy', $repair) }}" onsubmit="return confirm('Are you sure you want to delete this repair? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="fas fa-trash me-1"></i>Delete Repair
                                    </button>
                                    <small class="text-muted ms-2">This will permanently delete the repair request.</small>
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
    border-color: #17a2b8;
    box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
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
@endsection
