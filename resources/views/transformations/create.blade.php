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
                                <select name="waste_item_id" id="waste_item_id" class="form-select @error('waste_item_id') is-invalid @enderror" required>
                                    <option value="">Select waste...</option>
                                    @foreach($wasteItems as $wasteItem)
                                    <option value="{{ $wasteItem->id }}" {{ old('waste_item_id') == $wasteItem->id ? 'selected' : '' }}>
                                        {{ $wasteItem->title }} - {{ $wasteItem->category }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('waste_item_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <!-- AI Ideas Button -->
                                <div class="mt-3">
                                    <button type="button" id="generateIdeasBtn" class="btn btn-gradient-ai btn-sm" disabled>
                                        <i class="fas fa-magic me-2"></i>
                                        <span class="btn-text">Obtenir des idées de transformation avec l'IA</span>
                                        <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                                    </button>
                                    <small class="text-muted d-block mt-1">Sélectionnez un déchet pour activer cette fonctionnalité</small>
                                </div>
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

<!-- AI Ideas Modal -->
<div class="modal fade" id="ideasModal" tabindex="-1" aria-labelledby="ideasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-gradient-ai text-white">
                <h5 class="modal-title" id="ideasModalLabel">
                    <i class="fas fa-lightbulb me-2"></i>Idées de Transformation IA
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="ideasContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-ai {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.btn-gradient-ai {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    border: none;
    transition: all 0.3s ease;
}

.btn-gradient-ai:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(245, 87, 108, 0.3);
    color: white;
}

.btn-gradient-ai:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.idea-card {
    transition: all 0.3s ease;
    cursor: pointer;
    border-left: 4px solid #f5576c;
}

.idea-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.difficulty-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
}

.difficulty-facile {
    background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
}

.difficulty-moyen {
    background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
}

.difficulty-difficile {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const wasteSelect = document.getElementById('waste_item_id');
    const generateBtn = document.getElementById('generateIdeasBtn');
    const btnText = generateBtn.querySelector('.btn-text');
    const spinner = generateBtn.querySelector('.spinner-border');
    const modal = new bootstrap.Modal(document.getElementById('ideasModal'));

    // Enable/disable button based on waste selection
    wasteSelect.addEventListener('change', function() {
        generateBtn.disabled = !this.value;
    });

    // Generate ideas
    generateBtn.addEventListener('click', async function() {
        const wasteItemId = wasteSelect.value;

        if (!wasteItemId) {
            alert('Veuillez sélectionner un déchet d\'abord.');
            return;
        }

        // Show loading state
        generateBtn.disabled = true;
        btnText.textContent = 'Génération en cours...';
        spinner.classList.remove('d-none');

        try {
            const response = await fetch('{{ route("transformations.ai.generate-ideas") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    waste_item_id: wasteItemId,
                    count: 5
                })
            });

            const data = await response.json();

            if (data.success && data.ideas && data.ideas.length > 0) {
                displayIdeas(data.ideas, data.waste_item);
                modal.show();
            } else {
                alert(data.error || 'Impossible de générer des idées pour le moment.');
            }

        } catch (error) {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de la génération des idées.');
        } finally {
            // Reset button state
            generateBtn.disabled = false;
            btnText.textContent = 'Obtenir des idées de transformation avec l\'IA';
            spinner.classList.add('d-none');
        }
    });

    function displayIdeas(ideas, wasteItem) {
        const content = document.getElementById('ideasContent');

        let html = `
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Déchet sélectionné :</strong> ${wasteItem.title} (${wasteItem.category})
                <br>
                <small>Cliquez sur une idée pour remplir automatiquement le formulaire</small>
            </div>
            <div class="row g-3">
        `;

        ideas.forEach((idea, index) => {
            const difficultyClass = `difficulty-${idea.difficulty}`;
            html += `
                <div class="col-md-6">
                    <div class="card idea-card h-100 shadow-sm" onclick="selectIdea(${index})">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    ${idea.title}
                                </h5>
                                <span class="difficulty-badge ${difficultyClass} text-dark fw-bold">
                                    ${idea.difficulty.charAt(0).toUpperCase() + idea.difficulty.slice(1)}
                                </span>
                            </div>

                            <p class="card-text text-muted mb-3">${idea.description}</p>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-muted">
                                        <i class="fas fa-clock text-warning me-1"></i>
                                        <strong>${idea.estimated_time_hours}h</strong>
                                    </small>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">
                                        <i class="fas fa-euro-sign text-success me-1"></i>
                                        <strong>${idea.selling_price_range}</strong>
                                    </small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <small class="d-block mb-1"><strong>Matériaux nécessaires :</strong></small>
                                <div class="d-flex flex-wrap gap-1">
                                    ${idea.materials_needed.map(m =>
                                        `<span class="badge bg-secondary">${m}</span>`
                                    ).join('')}
                                </div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-leaf text-success me-1"></i>
                                    ${idea.eco_impact}
                                </small>
                            </div>

                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-users text-primary me-1"></i>
                                    ${idea.target_audience}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        content.innerHTML = html;

        // Store ideas globally for selection
        window.transformationIdeas = ideas;
    }

    window.selectIdea = function(index) {
        const idea = window.transformationIdeas[index];

        // Fill form fields
        document.querySelector('[name="product_title"]').value = idea.title;
        document.querySelector('[name="description"]').value = idea.description;
        document.querySelector('[name="time_spent_hours"]').value = idea.estimated_time_hours;

        // Extract price from range (take middle value)
        const priceMatch = idea.selling_price_range.match(/(\d+)-(\d+)/);
        if (priceMatch) {
            const avgPrice = (parseInt(priceMatch[1]) + parseInt(priceMatch[2])) / 2;
            document.querySelector('[name="price"]').value = avgPrice.toFixed(2);
        }

        // Close modal
        modal.hide();

        // Show success message
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show';
        alertDiv.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            <strong>Idée appliquée !</strong> Les champs ont été remplis avec l'idée "${idea.title}".
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('form').insertBefore(alertDiv, document.querySelector('form').firstChild);

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };
});
</script>
@endsection
