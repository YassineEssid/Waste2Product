@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('badges.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Badges
        </a>
    </div>

    <!-- Badge Details Card -->
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5 text-center">
                    <!-- Badge Icon -->
                    <div class="mb-4">
                        <div class="badge-icon-large rounded-circle d-inline-flex align-items-center justify-content-center position-relative"
                             style="width: 150px; height: 150px; background: {{ $badge->color }}15; border: 6px solid {{ $badge->color }};">
                            <i class="fas {{ $badge->icon }}" style="font-size: 4rem; color: {{ $badge->color }};"></i>
                            @if($isEarned)
                                <span class="position-absolute top-0 end-0 badge bg-success rounded-pill p-2">
                                    <i class="fas fa-check fs-5"></i>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Badge Name & Rarity -->
                    <h1 class="display-5 fw-bold mb-3">{{ $badge->name }}</h1>
                    <div class="mb-3">
                        <span class="badge fs-6 px-3 py-2" style="background-color: {{ $badge->rarity_color }};">
                            <i class="fas fa-star me-1"></i>{{ $badge->rarity_name }}
                        </span>
                        <span class="badge bg-secondary fs-6 px-3 py-2 ms-2">
                            <i class="fas fa-tag me-1"></i>{{ ucfirst($badge->type) }}
                        </span>
                    </div>

                    <!-- Description -->
                    <p class="lead text-muted mb-4">{{ $badge->description }}</p>

                    <!-- Requirements -->
                    <div class="row g-3 mb-4">
                        @if($badge->required_count > 0)
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-target text-primary fs-4 mb-2"></i>
                                    <h6 class="fw-bold mb-1">Required Count</h6>
                                    <p class="mb-0">{{ $badge->required_count }} {{ Str::replace('_', ' ', $badge->requirement_type) }}</p>
                                </div>
                            </div>
                        @endif
                        @if($badge->required_points > 0)
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-coins text-warning fs-4 mb-2"></i>
                                    <h6 class="fw-bold mb-1">Required Points</h6>
                                    <p class="mb-0">{{ $badge->required_points }} points</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- User Progress -->
                    @auth
                        @if($isEarned)
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-trophy me-2"></i>
                                <strong>Congratulations!</strong> You've earned this badge!
                            </div>
                        @elseif($userProgress !== null && $badge->required_count > 0)
                            <div class="mb-3">
                                <h6 class="fw-bold mb-2">Your Progress</h6>
                                <div class="progress mb-2" style="height: 25px;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ min(($userProgress / $badge->required_count) * 100, 100) }}%; background-color: {{ $badge->color }};"
                                         aria-valuenow="{{ $userProgress }}" aria-valuemin="0" aria-valuemax="{{ $badge->required_count }}">
                                        <span class="fw-bold">{{ round(($userProgress / $badge->required_count) * 100) }}%</span>
                                    </div>
                                </div>
                                <p class="mb-0 text-muted">{{ $userProgress }}/{{ $badge->required_count }} completed</p>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-lock me-2"></i>
                                Start working towards this badge!
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Badge Holders -->
    @if($holders->count() > 0)
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-users me-2"></i>Badge Holders ({{ $holders->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            @foreach($holders as $holder)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                        <div class="avatar-circle me-3">
                                            @if($holder->avatar)
                                                <img src="{{ asset('storage/' . $holder->avatar) }}" alt="{{ $holder->name }}" class="rounded-circle" width="50" height="50">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <span class="fw-bold">{{ strtoupper(substr($holder->name, 0, 1)) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">{{ $holder->name }}</h6>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                Earned {{ \Carbon\Carbon::parse($holder->pivot->earned_at)->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Related Badges -->
    @if($relatedBadges->count() > 0)
        <div class="row mt-4">
            <div class="col-lg-8 mx-auto">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-trophy me-2"></i>Related Badges
                </h5>
                <div class="row g-3">
                    @foreach($relatedBadges as $related)
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('badges.show', $related->id) }}" class="text-decoration-none">
                                <div class="card border-0 shadow-sm h-100 hover-lift">
                                    <div class="card-body text-center p-3">
                                        <div class="badge-icon rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                             style="width: 60px; height: 60px; background: {{ $related->color }}15; border: 3px solid {{ $related->color }};">
                                            <i class="fas {{ $related->icon }} fs-4" style="color: {{ $related->color }};"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1">{{ $related->name }}</h6>
                                        <span class="badge" style="background-color: {{ $related->rarity_color }}; font-size: 0.7rem;">
                                            {{ $related->rarity_name }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection
