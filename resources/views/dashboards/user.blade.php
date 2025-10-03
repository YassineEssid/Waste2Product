@extends('layouts.app')

@section('title', 'Mon Dashboard - Waste2Product')

@section('content')
<div class="container py-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Bonjour, {{ $user->name }} ! 👋</h1>
                    <p class="text-muted">Bienvenue sur votre tableau de bord personnel</p>
                </div>
                <div>
                    <span class="badge bg-success px-3 py-2">
                        <i class="fas fa-user me-2"></i>Membre de la communauté
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $myStats['my_waste_items'] }}</h3>
                            <p class="mb-0 small">Mes articles</p>
                        </div>
                        <i class="fas fa-recycle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $myStats['my_repair_requests'] }}</h3>
                            <p class="mb-0 small">Réparations</p>
                        </div>
                        <i class="fas fa-tools fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $myStats['my_events'] }}</h3>
                            <p class="mb-0 small">Événements</p>
                        </div>
                        <i class="fas fa-calendar-alt fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #6f42c1 0%, #9b59b6 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['marketplace_items'] }}</h3>
                            <p class="mb-0 small">Marketplace</p>
                        </div>
                        <i class="fas fa-shopping-bag fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8 mb-4">
            <!-- My Waste Items -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-recycle text-success me-2"></i>Mes articles récents</h5>
                        <a href="{{ route('waste-items.my') }}" class="btn btn-sm btn-outline-success">
                            Voir tout
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($myWasteItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Article</th>
                                        <th>Catégorie</th>
                                        <th>État</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($myWasteItems as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->title }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $item->category }}</span>
                                        </td>
                                        <td>{{ ucfirst($item->condition) }}</td>
                                        <td>
                                            <span class="badge {{ $item->status === 'available' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $item->status === 'available' ? 'Disponible' : 'Récupéré' }}
                                            </span>
                                        </td>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Vous n'avez pas encore d'articles</p>
                            <a href="{{ route('waste-items.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Ajouter un article
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Community Stats -->
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-chart-line text-info me-2"></i>Statistiques de la communauté</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded">
                                <h3 class="text-success mb-1">{{ $stats['available_items'] }}</h3>
                                <small class="text-muted">Articles disponibles</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded">
                                <h3 class="text-info mb-1">{{ $stats['total_transformations'] }}</h3>
                                <small class="text-muted">Transformations</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded">
                                <h3 class="text-warning mb-1">{{ $stats['active_events'] }}</h3>
                                <small class="text-muted">Événements actifs</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('waste-items.create') }}" class="btn btn-outline-success">
                            <i class="fas fa-plus me-2"></i>Ajouter un article
                        </a>
                        <a href="{{ route('repairs.create') }}" class="btn btn-outline-info">
                            <i class="fas fa-tools me-2"></i>Demande de réparation
                        </a>
                        <a href="{{ route('marketplace.index') }}" class="btn btn-outline-purple">
                            <i class="fas fa-shopping-bag me-2"></i>Voir le Marketplace
                        </a>
                        <a href="{{ route('events.index') }}" class="btn btn-outline-warning">
                            <i class="fas fa-calendar me-2"></i>Événements
                        </a>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt text-warning me-2"></i>Événements à venir</h5>
                </div>
                <div class="card-body">
                    @if($upcomingEvents->count() > 0)
                        @foreach($upcomingEvents as $event)
                        <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <h6 class="mb-1">{{ $event->title }}</h6>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $event->starts_at->format('d/m/Y à H:i') }}
                            </small><br>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $event->location }}
                            </small>
                        </div>
                        @endforeach
                        <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-warning w-100 mt-2">
                            Voir tous les événements
                        </a>
                    @else
                        <p class="text-muted text-center mb-0">Aucun événement à venir</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-outline-purple {
        color: #6f42c1;
        border-color: #6f42c1;
    }
    .btn-outline-purple:hover {
        background-color: #6f42c1;
        border-color: #6f42c1;
        color: white;
    }
    .text-purple {
        color: #6f42c1 !important;
    }
</style>
@endsection
