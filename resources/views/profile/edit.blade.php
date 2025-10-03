@extends('layouts.app')

@section('title', 'Modifier mon profil - Waste2Product')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Modifier mon profil</h1>
                    <p class="text-muted">Mettez à jour vos informations personnelles</p>
                </div>
                <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour au profil
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-triangle me-2"></i>Erreurs de validation:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Column: Profile Form -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Informations personnelles</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Avatar Section -->
                        <div class="mb-4 pb-4 border-bottom">
                            <label class="form-label fw-semibold">Photo de profil</label>
                            <div class="d-flex align-items-start gap-3">
                                <div class="flex-shrink-0">
                                    <img id="avatar-preview" 
                                         src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=128&background=28a745&color=fff' }}" 
                                         alt="{{ $user->name }}" 
                                         class="rounded-circle" 
                                         style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #28a745;">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="avatar" id="avatar-input" accept="image/*"
                                           class="form-control @error('avatar') is-invalid @enderror">
                                    <small class="form-text text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        JPG, PNG, GIF ou WEBP. Max 2MB. Dimensions: 100x100 à 2000x2000 pixels
                                    </small>
                                    @error('avatar')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @if($user->avatar)
                                        <a href="{{ route('profile.avatar.delete') }}" 
                                           class="btn btn-sm btn-outline-danger mt-2"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre avatar ?')">
                                            <i class="fas fa-trash me-1"></i>Supprimer l'avatar
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">
                                    Nom complet <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                                       class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                       class="form-control @error('email') is-invalid @enderror" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Téléphone</label>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" 
                                       class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="+33 6 12 34 56 78">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Rôle</label>
                                <input type="text" value="@if($user->role === 'admin') Administrateur
                                    @elseif($user->role === 'artisan') Artisan
                                    @elseif($user->role === 'repairer') Réparateur
                                    @else Utilisateur @endif" class="form-control" disabled>
                                <small class="text-muted">Le rôle ne peut être modifié que par un administrateur</small>
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">Adresse</label>
                                <textarea name="address" id="address" rows="2" 
                                          class="form-control @error('address') is-invalid @enderror"
                                          placeholder="Votre adresse complète">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="bio" class="form-label fw-semibold">Biographie</label>
                                <textarea name="bio" id="bio" rows="4" 
                                          class="form-control @error('bio') is-invalid @enderror"
                                          placeholder="Parlez-nous de vous, vos intérêts en matière de recyclage et de durabilité...">{{ old('bio', $user->bio) }}</textarea>
                                <small class="text-muted">Maximum 1000 caractères</small>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Geolocation -->
                        <div class="mb-4 pb-4 border-top pt-4">
                            <h6 class="mb-3"><i class="fas fa-map-marker-alt me-2 text-success"></i>Localisation (optionnel)</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="location_lat" class="form-label">Latitude</label>
                                    <input type="text" name="location_lat" id="location_lat" 
                                           value="{{ old('location_lat', $user->location_lat) }}" 
                                           class="form-control @error('location_lat') is-invalid @enderror"
                                           placeholder="Ex: 48.8566">
                                    @error('location_lat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="location_lng" class="form-label">Longitude</label>
                                    <input type="text" name="location_lng" id="location_lng" 
                                           value="{{ old('location_lng', $user->location_lng) }}" 
                                           class="form-control @error('location_lng') is-invalid @enderror"
                                           placeholder="Ex: 2.3522">
                                    @error('location_lng')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Permet de vous localiser sur la carte pour faciliter les échanges locaux
                            </small>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Security & Settings -->
        <div class="col-lg-4">
            <!-- Change Password -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Changer le mot de passe</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold">
                                Mot de passe actuel <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="current_password" id="current_password" 
                                   class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label fw-semibold">
                                Nouveau mot de passe <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="new_password" id="new_password" 
                                   class="form-control @error('new_password') is-invalid @enderror" required>
                            <small class="text-muted">Min 8 caractères, majuscules, minuscules, chiffres et symboles</small>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label fw-semibold">
                                Confirmer le mot de passe <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                                   class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-key me-2"></i>Modifier le mot de passe
                        </button>
                    </form>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations du compte</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Compte créé le</small>
                        <p class="fw-semibold mb-0">{{ $user->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Dernière modification</small>
                        <p class="fw-semibold mb-0">{{ $user->updated_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div>
                        <small class="text-muted">Rôle</small>
                        <p class="mb-0">
                            <span class="badge 
                                @if($user->role === 'admin') bg-danger
                                @elseif($user->role === 'artisan') bg-purple
                                @elseif($user->role === 'repairer') bg-info
                                @else bg-secondary @endif">
                                @if($user->role === 'admin') Administrateur
                                @elseif($user->role === 'artisan') Artisan
                                @elseif($user->role === 'repairer') Réparateur
                                @else Utilisateur @endif
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Zone dangereuse</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        La suppression de votre compte est irréversible. Toutes vos données seront définitivement supprimées.
                    </p>
                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="fas fa-trash-alt me-2"></i>Supprimer mon compte
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Supprimer le compte</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('profile.destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <strong>⚠️ Attention !</strong> Cette action est irréversible.
                    </div>
                    <p>Pour confirmer la suppression de votre compte, veuillez entrer votre mot de passe :</p>
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Mot de passe</label>
                        <input type="password" name="password" id="delete_password" class="form-control" required>
                    </div>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Toutes vos données (profil, articles, demandes) seront supprimées définitivement.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Supprimer définitivement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Preview avatar image when file is selected
    document.getElementById('avatar-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Validate password confirmation in real-time
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('new_password_confirmation');
    
    if (confirmPasswordInput && newPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            if (this.value && this.value !== newPasswordInput.value) {
                this.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                this.setCustomValidity('');
            }
        });
    }
</script>

<style>
    .bg-purple {
        background-color: #6f42c1 !important;
    }
</style>
@endsection
