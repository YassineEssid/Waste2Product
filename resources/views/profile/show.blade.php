@extends('layouts.app')

@section('title', 'Mon Profil - Waste2Product')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Mon Profil</h1>
                    <p class="text-muted">Consultez et gérez vos informations personnelles</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="btn btn-success">
                    <i class="fas fa-edit"></i> Modifier mon profil
                </a>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Column: Profile Information -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Profile Header -->
                    <div class="d-flex align-items-center mb-4 pb-4 border-bottom">
                        <div class="position-relative">
                            @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" 
                                     class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" 
                                     style="width: 100px; height: 100px;">
                                    <span class="fs-1 text-success fw-bold">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <span class="position-absolute bottom-0 end-0 badge rounded-pill 
                                @if($user->role === 'admin') bg-danger
                                @elseif($user->role === 'artisan') bg-purple
                                @elseif($user->role === 'repairer') bg-info
                                @else bg-secondary @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                        <div class="ms-4">
                            <h3 class="mb-1">{{ $user->name }}</h3>
                            <p class="text-muted mb-2">
                                <i class="fas fa-envelope me-2"></i>{{ $user->email }}
                            </p>
                            <p class="text-muted mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>Membre depuis {{ $stats['member_since'] }}
                            </p>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <h5 class="mb-3">
                        <i class="fas fa-user-circle me-2 text-success"></i>Informations personnelles
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Téléphone</label>
                            <p class="fw-semibold">{{ $user->phone ?: 'Non renseigné' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Rôle</label>
                            <p class="fw-semibold">
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
                        <div class="col-12">
                            <label class="form-label text-muted small">Adresse</label>
                            <p class="fw-semibold">{{ $user->address ?: 'Non renseignée' }}</p>
                        </div>
                        @if($user->location_lat && $user->location_lng)
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Latitude</label>
                            <p class="fw-semibold">{{ $user->location_lat }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Longitude</label>
                            <p class="fw-semibold">{{ $user->location_lng }}</p>
                        </div>
                        @endif
                        @if($user->bio)
                        <div class="col-12">
                            <label class="form-label text-muted small">Biographie</label>
                            <p class="fw-semibold">{{ $user->bio }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Statistics -->
        <div class="col-lg-4">
            <!-- Activity Statistics -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Statistiques</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted"><i class="fas fa-recycle me-2"></i>Articles déposés</span>
                            <span class="badge bg-success">{{ $stats['waste_items'] }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted"><i class="fas fa-tools me-2"></i>Demandes réparation</span>
                            <span class="badge bg-info">{{ $stats['repair_requests'] }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted"><i class="fas fa-magic me-2"></i>Transformations</span>
                            <span class="badge bg-warning">{{ $stats['transformations'] }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted"><i class="fas fa-calendar-check me-2"></i>Événements</span>
                            <span class="badge bg-purple">{{ $stats['events_attended'] }}</span>
                        </div>
                    </div>
                    <div class="pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-clock me-2"></i>Jours d'activité</span>
                            <span class="fw-bold text-success">{{ $stats['account_age_days'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role-Specific Stats -->
            @if($user->role === 'repairer')
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-wrench me-2"></i>Réparations</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Terminées</span>
                            <span class="badge bg-success">{{ $stats['repairs_completed'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">En attente</span>
                            <span class="badge bg-warning">{{ $stats['repairs_pending'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($user->role === 'artisan')
            <div class="card shadow-sm mb-3">
                <div class="card-header" style="background: linear-gradient(45deg, #6f42c1, #9b59b6); color: white;">
                    <h5 class="mb-0"><i class="fas fa-palette me-2"></i>Marketplace</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Articles vendus</span>
                            <span class="badge bg-success">{{ $stats['transformations_sold'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Articles en vente</span>
                            <span class="badge" style="background-color: #6f42c1;">{{ $stats['marketplace_items'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-edit me-2"></i>Modifier le profil
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
                        </a>
                        @if($user->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-user-shield me-2"></i>Panel Admin
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-purple {
        background-color: #6f42c1 !important;
    }
</style>
@endsection
