@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-5">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
                <div class="card-body p-5 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="display-4 fw-bold mb-3">
                                <i class="fas fa-trophy me-3"></i>Badge Collection
                            </h1>
                            <p class="lead mb-0">Earn badges by participating in the community and completing achievements!</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            @auth
                                <div class="badge bg-white text-primary px-4 py-3 fs-5 mb-2">
                                    <i class="fas fa-award me-2"></i>{{ $stats['earned_badges'] }}/{{ $stats['total_badges'] }} Earned
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body text-center p-4">
                    <div class="badge bg-primary bg-opacity-10 text-primary p-3 rounded-circle mb-3">
                        <i class="fas fa-trophy fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $stats['total_badges'] }}</h3>
                    <p class="text-muted mb-0">Total Badges</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body text-center p-4">
                    <div class="badge bg-success bg-opacity-10 text-success p-3 rounded-circle mb-3">
                        <i class="fas fa-check-circle fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $stats['earned_badges'] }}</h3>
                    <p class="text-muted mb-0">Earned</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body text-center p-4">
                    <div class="badge bg-warning bg-opacity-10 text-warning p-3 rounded-circle mb-3">
                        <i class="fas fa-gem fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $stats['legendary_badges'] }}</h3>
                    <p class="text-muted mb-0">Legendary</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body text-center p-4">
                    <div class="badge bg-info bg-opacity-10 text-info p-3 rounded-circle mb-3">
                        <i class="fas fa-star fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $stats['epic_badges'] }}</h3>
                    <p class="text-muted mb-0">Epic</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('badges.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-search me-2"></i>Search Badges
                    </label>
                    <input type="text" name="search" class="form-control" placeholder="Badge name or description..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-filter me-2"></i>Type
                    </label>
                    <select name="type" class="form-select">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                        <option value="event" {{ request('type') == 'event' ? 'selected' : '' }}>Event</option>
                        <option value="comment" {{ request('type') == 'comment' ? 'selected' : '' }}>Comment</option>
                        <option value="participation" {{ request('type') == 'participation' ? 'selected' : '' }}>Participation</option>
                        <option value="achievement" {{ request('type') == 'achievement' ? 'selected' : '' }}>Achievement</option>
                        <option value="special" {{ request('type') == 'special' ? 'selected' : '' }}>Special</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-layer-group me-2"></i>Rarity
                    </label>
                    <select name="rarity" class="form-select">
                        <option value="all" {{ request('rarity') == 'all' ? 'selected' : '' }}>All Rarities</option>
                        <option value="1" {{ request('rarity') == '1' ? 'selected' : '' }}>Common</option>
                        <option value="2" {{ request('rarity') == '2' ? 'selected' : '' }}>Rare</option>
                        <option value="3" {{ request('rarity') == '3' ? 'selected' : '' }}>Epic</option>
                        <option value="4" {{ request('rarity') == '4' ? 'selected' : '' }}>Legendary</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Badges Grid -->
    <div class="row g-4 mb-4">
        @forelse($badges as $badge)
            @php
                $isEarned = in_array($badge->id, $earnedBadgeIds);
                $userBadge = $userBadges->firstWhere('badge_id', $badge->id);
                $progress = $userBadge ? $userBadge->progress : 0;
            @endphp
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="{{ route('badges.show', $badge->id) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-lift badge-card {{ $isEarned ? 'badge-earned' : '' }}">
                        <div class="card-body p-4 text-center">
                            <!-- Badge Icon -->
                            <div class="badge-icon-wrapper mb-3 position-relative">
                                <div class="badge-icon rounded-circle d-inline-flex align-items-center justify-content-center"
                                     style="width: 100px; height: 100px; background: {{ $badge->color }}15; border: 4px solid {{ $badge->color }};">
                                    <i class="fas {{ $badge->icon }} fs-1" style="color: {{ $badge->color }};"></i>
                                </div>
                                @if($isEarned)
                                    <span class="position-absolute top-0 end-0 badge bg-success rounded-pill">
                                        <i class="fas fa-check"></i>
                                    </span>
                                @endif
                            </div>

                            <!-- Badge Name -->
                            <h5 class="fw-bold mb-2">{{ $badge->name }}</h5>

                            <!-- Rarity -->
                            <span class="badge mb-2" style="background-color: {{ $badge->rarity_color }};">
                                {{ $badge->rarity_name }}
                            </span>

                            <!-- Type -->
                            <div class="mb-2">
                                <span class="badge bg-secondary">{{ ucfirst($badge->type) }}</span>
                            </div>

                            <!-- Description -->
                            <p class="text-muted small mb-3">{{ Str::limit($badge->description, 80) }}</p>

                            <!-- Progress Bar (if in progress) -->
                            @if(auth()->check() && !$isEarned && $progress > 0 && $badge->required_count > 0)
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ min(($progress / $badge->required_count) * 100, 100) }}%; background-color: {{ $badge->color }};"
                                         aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="{{ $badge->required_count }}">
                                    </div>
                                </div>
                                <small class="text-muted">{{ $progress }}/{{ $badge->required_count }}</small>
                            @endif

                            <!-- Requirements -->
                            @if($badge->required_count > 0)
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-target me-1"></i>{{ $badge->required_count }} required
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-trophy fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No badges found</h5>
                        <p class="text-muted">Try adjusting your filters</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $badges->links() }}
    </div>
</div>

<style>
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.badge-card {
    transition: all 0.3s ease;
    opacity: 0.85;
}

.badge-card:hover {
    opacity: 1;
}

.badge-card.badge-earned {
    opacity: 1;
    border: 2px solid #10b981 !important;
}

.badge-icon-wrapper {
    position: relative;
}

.card {
    border-radius: 15px;
}
</style>
@endsection
