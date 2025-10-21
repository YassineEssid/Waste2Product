@extends('layouts.app')

@section('title', 'List New Item - Marketplace')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="create-header text-center mb-5">
                <div class="create-icon mb-3">
                    <i class="fas fa-store-alt fa-3x text-success"></i>
                </div>
                <h1 class="display-5 fw-bold mb-3">List Your Item</h1>
                <p class="text-muted">Share your eco-friendly creations and sustainable products with the community.</p>
            </div>

            <!-- Form Card -->
            <div class="form-card">
                <form method="POST" action="{{ route('marketplace.store') }}" enctype="multipart/form-data" id="itemForm">
                    @csrf

                    <!-- Basic Information -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fas fa-info-circle me-2"></i>Item Information
                        </h4>

                        <!-- AI Quick Start Box -->
                        <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                            <i class="fas fa-magic fa-2x me-3"></i>
                            <div class="flex-grow-1">
                                <h6 class="alert-heading mb-1">✨ Aide IA - Détection Automatique</h6>
                                <p class="mb-2 small">Décrivez brièvement votre article et l'IA va suggérer automatiquement la catégorie, le titre optimisé, l'état et les mots-clés !</p>
                                <div class="input-group input-group-sm">
                                    <input type="text"
                                           id="aiQuickDescription"
                                           class="form-control"
                                           placeholder="Ex: vieux fauteuil en cuir marron, vase en céramique bleu, lampe vintage..."
                                           maxlength="200">
                                    <button type="button" class="btn btn-primary" id="aiDetectBtn">
                                        <i class="fas fa-wand-magic-sparkles me-1"></i> Détecter
                                    </button>
                                </div>
                                <div id="aiLoadingSpinner" class="mt-2" style="display: none;">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                        <span class="visually-hidden">Chargement...</span>
                                    </div>
                                    <small>L'IA analyse votre article...</small>
                                </div>
                            </div>
                        </div>

                        <!-- AI Results Display -->
                        <div id="aiResultsBox" class="alert alert-success" style="display: none;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="alert-heading">
                                        <i class="fas fa-check-circle me-1"></i> Suggestions IA
                                        <span id="aiConfidenceBadge" class="badge bg-success ms-2"></span>
                                    </h6>
                                    <div id="aiResultsContent"></div>
                                    <button type="button" class="btn btn-sm btn-outline-success mt-2" id="applyAISuggestions">
                                        <i class="fas fa-check me-1"></i> Appliquer ces suggestions
                                    </button>
                                </div>
                                <button type="button" class="btn-close" id="closeAIResults"></button>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label required">Item Title</label>
                                <input type="text"
                                       id="itemTitle"
                                       class="form-control @error('title') is-invalid @enderror"
                                       name="title"
                                       value="{{ old('title') }}"
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
                                        <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
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
                                        <option value="{{ $condition }}" {{ old('condition') == $condition ? 'selected' : '' }}>
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
                                          required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Tell the story of your item. What makes it special? How was it made?
                                </div>
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
                                <label class="form-label required">Price (€)</label>
                                <div class="input-group">
                                    <span class="input-group-text">DT</span>
                                    <input type="number"
                                           id="itemPrice"
                                           class="form-control @error('price') is-invalid @enderror"
                                           name="price"
                                           value="{{ old('price') }}"
                                           min="0"
                                           step="0.01"
                                           placeholder="0.00"
                                           required>
                                    <button type="button"
                                            class="btn btn-outline-info"
                                            id="aiSuggestPriceBtn"
                                            title="Suggérer un prix avec l'IA">
                                        <i class="fas fa-calculator"></i>
                                    </button>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="priceHint" class="form-text text-muted" style="display: none;">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    <span id="priceHintText"></span>
                                </div>
                            </div>

                            <!-- AI Price Suggestion Box -->
                            <div id="aiPriceBox" class="col-12" style="display: none;">
                                <div class="alert alert-success">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="alert-heading">
                                                <i class="fas fa-magic me-1"></i> Suggestion de Prix IA
                                                <span id="aiPriceConfidence" class="badge bg-success ms-2"></span>
                                            </h6>
                                            <div id="aiPriceContent">
                                                <div class="row g-3 mt-2">
                                                    <div class="col-md-4">
                                                        <div class="card bg-light">
                                                            <div class="card-body text-center p-2">
                                                                <small class="text-muted">Fourchette</small>
                                                                <h5 class="mb-0" id="aiPriceRange">-</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="card bg-success text-white">
                                                            <div class="card-body text-center p-2">
                                                                <small>Prix recommandé</small>
                                                                <h5 class="mb-0" id="aiPriceRecommended">-</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="card bg-light">
                                                            <div class="card-body text-center p-2">
                                                                <small class="text-muted">Min. négociation</small>
                                                                <h5 class="mb-0" id="aiPriceNegotiation">-</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-3" id="aiPriceDetails"></div>
                                            </div>
                                            <button type="button"
                                                    class="btn btn-sm btn-success mt-2"
                                                    id="applyAIPrice">
                                                <i class="fas fa-check me-1"></i> Appliquer le prix recommandé
                                            </button>
                                        </div>
                                        <button type="button" class="btn-close" id="closeAIPrice"></button>
                                    </div>
                                </div>
                            </div>                            <div class="col-md-6">
                                <label class="form-label">Quantity</label>
                                <input type="number"
                                       class="form-control @error('quantity') is-invalid @enderror"
                                       name="quantity"
                                       value="{{ old('quantity', 1) }}"
                                       min="1"
                                       placeholder="1">
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-cube me-1"></i>
                                    Number of items available
                                </div>
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
                                           {{ old('is_negotiable') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_negotiable">
                                        <i class="fas fa-handshake me-2"></i>Price is negotiable
                                    </label>
                                </div>
                                <div class="form-text">
                                    Allow buyers to negotiate the price
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="is_featured"
                                           id="is_featured"
                                           value="1"
                                           {{ old('is_featured') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        <i class="fas fa-star me-2"></i>Feature this item
                                    </label>
                                </div>
                                <div class="form-text">
                                    Highlight your item in featured listings
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item Images -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fas fa-images me-2"></i>Item Photos
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
                                    <h5>Upload Item Photos</h5>
                                    <p class="text-muted mb-3">Drag and drop images here, or click to browse</p>
                                    <p class="text-muted small mb-3">You can upload multiple images (Max 5 images, 2MB each)</p>
                                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('images').click()">
                                        <i class="fas fa-image me-2"></i>Choose Images
                                    </button>
                                </div>

                                <div class="image-previews" id="imagePreviews" style="display: none;">
                                    <div class="previews-grid" id="previewsGrid">
                                        <!-- Image previews will be inserted here -->
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

                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            High-quality photos help sell your item. Include different angles and details.
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('marketplace.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-store me-2"></i>List Item
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

        if (selectedFiles.length === 0) {
            const confirmNoImages = confirm('You haven\'t uploaded any images. Items with photos get more attention. Continue anyway?');
            if (!confirmNoImages) {
                e.preventDefault();
                return false;
            }
        }
    });

    // ========================================
    // AI CATEGORY DETECTION
    // ========================================
    let aiSuggestions = null;

    const aiDetectBtn = document.getElementById('aiDetectBtn');
    const aiQuickDescription = document.getElementById('aiQuickDescription');
    const aiLoadingSpinner = document.getElementById('aiLoadingSpinner');
    const aiResultsBox = document.getElementById('aiResultsBox');
    const aiResultsContent = document.getElementById('aiResultsContent');
    const aiConfidenceBadge = document.getElementById('aiConfidenceBadge');
    const applyAISuggestionsBtn = document.getElementById('applyAISuggestions');
    const closeAIResultsBtn = document.getElementById('closeAIResults');

    // Detect category with AI
    aiDetectBtn.addEventListener('click', async function() {
        const description = aiQuickDescription.value.trim();

        if (!description || description.length < 5) {
            alert('⚠️ Veuillez entrer une description d\'au moins 5 caractères');
            return;
        }

        // Show loading
        aiLoadingSpinner.style.display = 'block';
        aiDetectBtn.disabled = true;
        aiResultsBox.style.display = 'none';

        try {
            const response = await fetch('{{ route('marketplace.ai.detect-category') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ description: description })
            });

            const result = await response.json();

            if (result.success && result.data) {
                aiSuggestions = result.data;
                displayAIResults(result.data);
            } else {
                alert('❌ ' + (result.error || 'Erreur lors de la détection'));
            }
        } catch (error) {
            console.error('AI Detection Error:', error);
            alert('❌ Une erreur est survenue. Vérifiez votre connexion.');
        } finally {
            aiLoadingSpinner.style.display = 'none';
            aiDetectBtn.disabled = false;
        }
    });

    // Display AI results
    function displayAIResults(data) {
        let html = '<ul class="mb-0">';
        html += `<li><strong>📦 Catégorie:</strong> ${data.category_label} <code class="text-muted small">${data.category}</code></li>`;
        html += `<li><strong>✏️ Titre suggéré:</strong> "${data.title}"</li>`;
        html += `<li><strong>🔧 État:</strong> ${data.condition_label}</li>`;

        if (data.keywords && data.keywords.length > 0) {
            html += `<li><strong>🏷️ Mots-clés:</strong> `;
            data.keywords.forEach(keyword => {
                html += `<span class="badge bg-light text-dark me-1">${keyword}</span>`;
            });
            html += '</li>';
        }

        if (data.reasoning) {
            html += `<li class="text-muted small mt-2"><em>💡 ${data.reasoning}</em></li>`;
        }
        html += '</ul>';

        aiResultsContent.innerHTML = html;
        aiConfidenceBadge.textContent = `${data.confidence}% confiance`;
        aiResultsBox.style.display = 'block';

        // Scroll to results
        aiResultsBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Apply AI suggestions to form
    applyAISuggestionsBtn.addEventListener('click', function() {
        if (!aiSuggestions) return;

        // Set title
        document.getElementById('itemTitle').value = aiSuggestions.title;

        // Set category
        const categorySelect = document.querySelector('select[name="category"]');
        categorySelect.value = aiSuggestions.category;

        // Set condition
        const conditionSelect = document.querySelector('select[name="condition"]');
        conditionSelect.value = aiSuggestions.condition;

        // Add keywords to description if empty
        const descriptionTextarea = document.querySelector('textarea[name="description"]');
        if (!descriptionTextarea.value.trim() && aiSuggestions.keywords && aiSuggestions.keywords.length > 0) {
            descriptionTextarea.value = `Mots-clés: ${aiSuggestions.keywords.join(', ')}\n\n`;
            descriptionTextarea.focus();
        }

        // Visual feedback
        [categorySelect, conditionSelect, document.getElementById('itemTitle')].forEach(el => {
            el.classList.add('border-success');
            setTimeout(() => el.classList.remove('border-success'), 2000);
        });

        // Show success message
        const successMsg = document.createElement('div');
        successMsg.className = 'alert alert-success alert-dismissible fade show mt-2';
        successMsg.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            <strong>Suggestions appliquées avec succès !</strong> Vous pouvez maintenant modifier les champs si nécessaire.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        aiResultsBox.insertAdjacentElement('afterend', successMsg);
        setTimeout(() => successMsg.remove(), 5000);

        // Scroll to title field
        document.getElementById('itemTitle').scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    // Close AI results
    closeAIResultsBtn.addEventListener('click', function() {
        aiResultsBox.style.display = 'none';
    });

    // Allow Enter key on quick description
    aiQuickDescription.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            aiDetectBtn.click();
        }
    });

    // ========================================
    // AI PRICE SUGGESTION
    // ========================================
    let aiPriceSuggestion = null;

    const aiSuggestPriceBtn = document.getElementById('aiSuggestPriceBtn');
    const aiPriceBox = document.getElementById('aiPriceBox');
    const aiPriceContent = document.getElementById('aiPriceContent');
    const aiPriceConfidence = document.getElementById('aiPriceConfidence');
    const aiPriceRange = document.getElementById('aiPriceRange');
    const aiPriceRecommended = document.getElementById('aiPriceRecommended');
    const aiPriceNegotiation = document.getElementById('aiPriceNegotiation');
    const aiPriceDetails = document.getElementById('aiPriceDetails');
    const applyAIPriceBtn = document.getElementById('applyAIPrice');
    const closeAIPriceBtn = document.getElementById('closeAIPrice');
    const priceHint = document.getElementById('priceHint');
    const priceHintText = document.getElementById('priceHintText');

    // Suggest price with AI
    aiSuggestPriceBtn.addEventListener('click', async function() {
        const title = document.getElementById('itemTitle').value.trim();
        const description = document.querySelector('textarea[name="description"]').value.trim();
        const category = document.querySelector('select[name="category"]').value;
        const condition = document.querySelector('select[name="condition"]').value;

        // Validate inputs
        if (!title || title.length < 3) {
            alert('⚠️ Veuillez d\'abord saisir le titre de l\'article');
            document.getElementById('itemTitle').focus();
            return;
        }

        if (!description || description.length < 10) {
            alert('⚠️ Veuillez d\'abord saisir une description');
            document.querySelector('textarea[name="description"]').focus();
            return;
        }

        if (!category) {
            alert('⚠️ Veuillez d\'abord sélectionner une catégorie');
            document.querySelector('select[name="category"]').focus();
            return;
        }

        if (!condition) {
            alert('⚠️ Veuillez d\'abord sélectionner l\'état de l\'article');
            document.querySelector('select[name="condition"]').focus();
            return;
        }

        // Get keywords from AI suggestions if available
        const keywords = aiSuggestions?.keywords || [];

        // Show loading state
        aiSuggestPriceBtn.disabled = true;
        aiSuggestPriceBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Analyse...';
        aiPriceBox.style.display = 'none';

        try {
            const response = await fetch('{{ route('marketplace.ai.suggest-price') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    title: title,
                    description: description,
                    category: category,
                    condition: condition,
                    keywords: keywords
                })
            });

            const result = await response.json();

            if (result.success && result.data) {
                aiPriceSuggestion = result.data;
                displayAIPriceSuggestion(result.data);
            } else {
                alert('❌ ' + (result.error || 'Erreur lors de la suggestion de prix'));
            }
        } catch (error) {
            console.error('AI Price Suggestion Error:', error);
            alert('❌ Une erreur est survenue. Vérifiez votre connexion.');
        } finally {
            aiSuggestPriceBtn.disabled = false;
            aiSuggestPriceBtn.innerHTML = '<i class="fas fa-calculator"></i>';
        }
    });

    // Display AI price suggestion
    function displayAIPriceSuggestion(data) {
        // Display price range and recommendation
        aiPriceRange.textContent = data.price_range_formatted;
        aiPriceRecommended.textContent = data.recommended_price_formatted;
        aiPriceNegotiation.textContent = data.negotiation_min + '€';
        aiPriceConfidence.textContent = data.confidence + '% confiance';

        // Display detailed analysis
        let detailsHtml = '';

        // Market demand
        const demandEmoji = data.market_demand === 'forte' ? '📈' : (data.market_demand === 'faible' ? '📉' : '📊');
        detailsHtml += `<div class="mb-2"><strong>${demandEmoji} Demande du marché:</strong> ${data.market_demand}</div>`;

        // Factors
        if (data.factors && data.factors.length > 0) {
            detailsHtml += '<div class="mb-2"><strong>💡 Facteurs d\'évaluation:</strong><ul class="mb-0 mt-1">';
            data.factors.forEach(factor => {
                const sign = factor.impact >= 0 ? '+' : '';
                const color = factor.impact >= 0 ? 'text-success' : 'text-danger';
                detailsHtml += `<li><span class="${color}">${sign}${factor.impact}%</span> - ${factor.name}</li>`;
            });
            detailsHtml += '</ul></div>';
        }

        // Reasoning
        if (data.reasoning) {
            detailsHtml += `<div class="mb-2"><strong>📊 Analyse:</strong> ${data.reasoning}</div>`;
        }

        // Tips
        if (data.tips) {
            detailsHtml += `<div class="alert alert-info mb-0 mt-2 py-2"><i class="fas fa-lightbulb me-1"></i> <strong>Conseil:</strong> ${data.tips}</div>`;
        }

        // Fallback indicator
        if (data.is_fallback) {
            detailsHtml += '<div class="alert alert-warning mb-0 mt-2 py-2"><i class="fas fa-info-circle me-1"></i> <small>Prix calculé automatiquement (IA non disponible)</small></div>';
        }

        aiPriceDetails.innerHTML = detailsHtml;

        // Show price hint
        priceHintText.textContent = `💡 Prix suggéré: ${data.recommended_price}€ (fourchette: ${data.price_range_formatted})`;
        priceHint.style.display = 'block';

        // Show the box
        aiPriceBox.style.display = 'block';

        // Scroll to box
        aiPriceBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Apply AI price suggestion
    applyAIPriceBtn.addEventListener('click', function() {
        if (!aiPriceSuggestion) return;

        // Set the recommended price
        document.getElementById('itemPrice').value = aiPriceSuggestion.recommended_price;

        // Enable negotiable option
        document.getElementById('is_negotiable').checked = true;

        // Visual feedback
        const priceInput = document.getElementById('itemPrice');
        priceInput.classList.add('border-success');
        setTimeout(() => priceInput.classList.remove('border-success'), 2000);

        // Show success message
        const successMsg = document.createElement('div');
        successMsg.className = 'alert alert-success alert-dismissible fade show mt-2';
        successMsg.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            <strong>Prix appliqué !</strong> Recommandation: ${aiPriceSuggestion.recommended_price}€
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        aiPriceBox.insertAdjacentElement('afterend', successMsg);
        setTimeout(() => successMsg.remove(), 5000);

        // Scroll to price field
        document.getElementById('itemPrice').scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    // Close AI price box
    closeAIPriceBtn.addEventListener('click', function() {
        aiPriceBox.style.display = 'none';
    });
});
</script>
@endsection
