@extends('admin.layout')

@section('title', 'Mon Profil Admin - Waste2Product')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-user-shield me-2 text-primary"></i>Mon Profil Administrateur
            </h1>
            <p class="text-muted mb-0">Gérez vos informations personnelles</p>
        </div>
        <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Modifier mon profil
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Profile Information Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" 
                                 alt="{{ $user->name }}" 
                                 class="rounded-circle shadow" 
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center shadow" 
                                 style="width: 150px; height: 150px;">
                                <span class="display-3 text-primary fw-bold">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    
                    <span class="badge bg-danger px-3 py-2 mb-3">
                        <i class="fas fa-crown me-1"></i>ADMINISTRATEUR
                    </span>
                    
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted"><i class="fas fa-calendar-alt me-2"></i>Membre depuis</span>
                            <strong>{{ $stats['member_since'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted"><i class="fas fa-clock me-2"></i>Ancienneté</span>
                            <strong>{{ $stats['account_age_days'] }} jours</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Information -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i>Informations personnelles</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Nom complet</label>
                            <p class="fw-semibold">{{ $user->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Email</label>
                            <p class="fw-semibold">{{ $user->email }}</p>
                        </div>
                    </div>

                    @if($user->phone)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Téléphone</label>
                            <p class="fw-semibold">{{ $user->phone }}</p>
                        </div>
                    </div>
                    @endif

                    @if($user->address)
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="text-muted small mb-1">Adresse</label>
                            <p class="fw-semibold">{{ $user->address }}</p>
                        </div>
                    </div>
                    @endif

                    @if($user->latitude && $user->longitude)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Latitude</label>
                            <p class="fw-semibold">{{ $user->latitude }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Longitude</label>
                            <p class="fw-semibold">{{ $user->longitude }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Rôle</label>
                            <p><span class="badge bg-danger">{{ ucfirst($user->role) }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Compte créé le</label>
                            <p class="fw-semibold">{{ $user->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Statistics -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-chart-bar text-primary me-2"></i>Statistiques de la plateforme</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="p-3 bg-primary bg-opacity-10 rounded">
                                <h3 class="text-primary mb-1">{{ $platformStats['total_users'] ?? 0 }}</h3>
                                <small class="text-muted">Utilisateurs</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3 bg-success bg-opacity-10 rounded">
                                <h3 class="text-success mb-1">{{ $platformStats['total_items'] ?? 0 }}</h3>
                                <small class="text-muted">Articles</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3 bg-warning bg-opacity-10 rounded">
                                <h3 class="text-warning mb-1">{{ $platformStats['total_repairs'] ?? 0 }}</h3>
                                <small class="text-muted">Réparations</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3 bg-info bg-opacity-10 rounded">
                                <h3 class="text-info mb-1">{{ $platformStats['total_events'] ?? 0 }}</h3>
                                <small class="text-muted">Événements</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
