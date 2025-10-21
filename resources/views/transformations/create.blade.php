@extends('layouts.app')

@section('title', 'New Transformation')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body bg-gradient-primary text-white p-4">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('transformations.index') }}" class="btn btn-light btn-sm me-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h2 class="mb-1"><i class="fas fa-plus-circle me-2"></i>New Transformation</h2>
                            <p class="mb-0 opacity-75">Transform waste into valuable product</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('transformations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Basic Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Source Waste <span class="text-danger">*</span></label>
                                <select name="waste_item_id" class="form-select @error('waste_item_id') is-invalid @enderror" required>
                                    <option value="">Select waste...</option>
                                    @foreach($wasteItems as $wasteItem)
                                    <option value="{{ $wasteItem->id }}" {{ old('waste_item_id') == $wasteItem->id ? 'selected' : '' }}>
                                        {{ $wasteItem->type }} - {{ $wasteItem->quantity }} {{ $wasteItem->unit }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('waste_item_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Product Title <span class="text-danger">*</span></label>
                                <input type="text" name="product_title" class="form-control @error('product_title') is-invalid @enderror" value="{{ old('product_title') }}" placeholder="Ex: Decorative vase from recycled glass" required>
                                @error('product_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Describe the transformation process and final product..." required>{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Price (DT)</label>
                                <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" placeholder="0.00">
                                @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave empty if price is not defined yet</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="planned" {{ old('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Environmental Impact -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-leaf me-2 text-success"></i>Environmental Impact</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-cloud text-success me-1"></i>CO₂ Saved (kg)
                                </label>
                                <input type="number" step="0.01" name="co2_saved" class="form-control @error('co2_saved') is-invalid @enderror" value="{{ old('co2_saved', 0) }}" placeholder="0.00">
                                @error('co2_saved')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-trash-alt text-info me-1"></i>Waste Reduced (kg)
                                </label>
                                <input type="number" step="0.01" name="waste_reduced" class="form-control @error('waste_reduced') is-invalid @enderror" value="{{ old('waste_reduced', 0) }}" placeholder="0.00">
                                @error('waste_reduced')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Process Details -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-cogs me-2 text-warning"></i>Process Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Time Spent (hours)</label>
                                <input type="number" name="time_spent_hours" class="form-control @error('time_spent_hours') is-invalid @enderror" value="{{ old('time_spent_hours') }}" placeholder="0">
                                @error('time_spent_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Materials Cost (DT)</label>
                                <input type="number" step="0.01" name="materials_cost" class="form-control @error('materials_cost') is-invalid @enderror" value="{{ old('materials_cost', 0) }}" placeholder="0.00">
                                @error('materials_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-images me-2 text-info"></i>Images</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Before Photos</label>
                                <input type="file" name="before_images[]" class="form-control @error('before_images.*') is-invalid @enderror" accept="image/*" multiple>
                                @error('before_images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Multiple images accepted</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Process Photos</label>
                                <input type="file" name="process_images[]" class="form-control @error('process_images.*') is-invalid @enderror" accept="image/*" multiple>
                                @error('process_images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Multiple images accepted</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">After Photos</label>
                                <input type="file" name="after_images[]" class="form-control @error('after_images.*') is-invalid @enderror" accept="image/*" multiple>
                                @error('after_images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Multiple images accepted</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('transformations.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-check me-2"></i>Create Transformation
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection
