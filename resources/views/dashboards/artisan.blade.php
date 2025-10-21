@extends('layouts.app')

@section('title', 'Dashboard Artisan - Waste2Product')

@section('content')
<div class="container py-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Dashboard Artisan 🎨</h1>
                    <p class="text-muted">Bienvenue {{ $user->name }}, transformez et créez ici</p>
                </div>
                <div>
                    <span class="badge bg-purple px-3 py-2">
                        <i class="fas fa-palette me-2"></i>Artisan Créateur
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['my_transformations'] }}</h3>
                            <p class="mb-0 small">Transformations</p>
                        </div>
                        <i class="fas fa-recycle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['published_items'] }}</h3>
                            <p class="mb-0 small">Articles publiés</p>
                        </div>
                        <i class="fas fa-boxes fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['sold_items'] }}</h3>
                            <p class="mb-0 small">Articles vendus</p>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['total_revenue'], 2) }}€</h3>
                            <p class="mb-0 small">Revenus totaux</p>
                        </div>
                        <i class="fas fa-coins fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8 mb-4">
            <!-- My Transformations -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-purple text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-magic me-2"></i>Mes transformations</h5>
                        <a href="{{ route('transformations.create') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-plus me-1"></i>Nouvelle transformation
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($myTransformations->count() > 0)
                        <div class="row">
                            @foreach($myTransformations as $transformation)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-start border-purple border-4">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-1">{{ $transformation->title }}</h6>
                                            <span class="badge bg-purple">
                                                {{ ucfirst($transformation->status) }}
                                            </span>
                                        </div>
                                        <p class="mb-2 small text-muted">{{ Str::limit($transformation->description, 80) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>{{ $transformation->created_at->format('d/m/Y') }}
                                            </small>
                                            <a href="{{ route('transformations.show', $transformation) }}" class="btn btn-sm btn-outline-purple">
                                                <i class="fas fa-eye me-1"></i>Voir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-magic fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune transformation pour le moment</p>
                            <a href="{{ route('transformations.create') }}" class="btn btn-purple">
                                <i class="fas fa-plus me-2"></i>Créer ma première transformation
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Available Waste Items -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-box-open me-2"></i>Matériaux disponibles pour transformation</h5>
                </div>
                <div class="card-body">
                    @if($availableWasteItems->count() > 0)
                        <div class="row">
                            @foreach($availableWasteItems as $item)
                            <div class="col-md-6 mb-3">
                                <div class="card border">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $item->title }}</h6>
                                                <small class="text-muted d-block mb-1">
                                                    <i class="fas fa-tag me-1"></i>{{ $item->category }}
                                                </small>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-info-circle me-1"></i>{{ $item->condition }}
                                                </small>
                                            </div>
                                            <div>
                                                <a href="{{ route('waste-items.show', $item) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">Aucun matériau disponible actuellement</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-user-circle text-purple me-2"></i>Mon profil</h5>
                </div>
                <div class="card-body text-center">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" 
                             class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-purple bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 100px; height: 100px;">
                            <span class="fs-1 text-purple fw-bold">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <h5>{{ $user->name }}</h5>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <a href="{{ route('profile.show') }}" class="btn btn-outline-purple w-100 mb-2">
                        <i class="fas fa-user me-2"></i>Voir mon profil
                    </a>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-bolt text-warning me-2"></i>Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('transformations.create') }}" class="btn btn-purple">
                            <i class="fas fa-magic me-2"></i>Nouvelle transformation
                        </a>
                        <a href="{{ route('marketplace.create') }}" class="btn btn-outline-success">
                            <i class="fas fa-plus-circle me-2"></i>Publier sur la marketplace
                        </a>
                        <a href="{{ route('waste-items.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-search me-2"></i>Trouver des matériaux
                        </a>
                    </div>
                </div>
            </div>

            <!-- My Marketplace Items -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-store me-2"></i>Mes articles en vente</h5>
                </div>
                <div class="card-body">
                    @if($myMarketplaceItems->count() > 0)
                        @foreach($myMarketplaceItems as $item)
                        <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $item->title }}</h6>
                                    <small class="text-success fw-bold">{{ number_format($item->price, 2) }}€</small>
                                </div>
                                <span class="badge {{ $item->status === 'sold' ? 'bg-success' : 'bg-primary' }}">
                                    {{ $item->status === 'sold' ? 'Vendu' : 'Disponible' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center mb-0 small">Aucun article en vente</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-purple {
    background-color: #9b59b6 !important;
}
.text-purple {
    color: #9b59b6 !important;
}
.btn-purple {
    background-color: #9b59b6;
    color: white;
    border: none;
}
.btn-purple:hover {
    background-color: #8e44ad;
    color: white;
}
.btn-outline-purple {
    color: #9b59b6;
    border-color: #9b59b6;
}
.btn-outline-purple:hover {
    background-color: #9b59b6;
    color: white;
}
.border-purple {
    border-color: #9b59b6 !important;
}
</style>
@endsection
