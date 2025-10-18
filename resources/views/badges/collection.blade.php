@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="mb-5">
        <h1 class="display-5 fw-bold mb-3">
            <i class="fas fa-trophy me-3"></i>My Badge Collection
        </h1>
        <p class="lead text-muted">Track your achievements and progress</p>
    </div>

    <!-- Statistics -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="badge bg-success bg-opacity-10 text-success p-3 rounded-circle mx-auto mb-3" style="width: 70px; height: 70px;">
                    <i class="fas fa-check-circle fs-2"></i>
                </div>
                <h3 class="fw-bold">{{ $stats['earned_badges'] }}</h3>
                <p class="text-muted mb-0">Earned</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="badge bg-primary bg-opacity-10 text-primary p-3 rounded-circle mx-auto mb-3" style="width: 70px; height: 70px;">
                    <i class="fas fa-hourglass-half fs-2"></i>
                </div>
                <h3 class="fw-bold">{{ $stats['in_progress'] }}</h3>
                <p class="text-muted mb-0">In Progress</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="badge bg-secondary bg-opacity-10 text-secondary p-3 rounded-circle mx-auto mb-3" style="width: 70px; height: 70px;">
                    <i class="fas fa-lock fs-2"></i>
                </div>
                <h3 class="fw-bold">{{ $stats['locked_badges'] }}</h3>
                <p class="text-muted mb-0">Locked</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="badge bg-info bg-opacity-10 text-info p-3 rounded-circle mx-auto mb-3" style="width: 70px; height: 70px;">
                    <i class="fas fa-percentage fs-2"></i>
                </div>
                <h3 class="fw-bold">{{ $stats['completion_rate'] }}%</h3>
                <p class="text-muted mb-0">Completion</p>
            </div>
        </div>
    </div>

    <!-- Earned Badges -->
    @if($earnedBadges->count() > 0)
        <div class="mb-5">
            <h3 class="fw-bold mb-4">
                <i class="fas fa-award me-2 text-success"></i>Earned Badges ({{ $earnedBadges->count() }})
            </h3>
            <div class="row g-4">
                @foreach($earnedBadges as $badge)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <a href="{{ route('badges.show', $badge->id) }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 hover-lift badge-earned">
                                <div class="card-body p-4 text-center">
                                    <div class="position-relative mb-3">
                                        <div class="badge-icon rounded-circle d-inline-flex align-items-center justify-content-center"
                                             style="width: 90px; height: 90px; background: {{ $badge->color }}15; border: 4px solid {{ $badge->color }};">
                                            <i class="fas {{ $badge->icon }} fs-2" style="color: {{ $badge->color }};"></i>
                                        </div>
                                        <span class="position-absolute top-0 end-0 badge bg-success rounded-pill">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    </div>
                                    <h6 class="fw-bold mb-2">{{ $badge->name }}</h6>
                                    <span class="badge mb-2" style="background-color: {{ $badge->rarity_color }};">
                                        {{ $badge->rarity_name }}
                                    </span>
                                    <p class="text-muted small mb-2">{{ Str::limit($badge->description, 60) }}</p>
                                    @php $userBadge = $userBadges->get($badge->id); @endphp
                                    @if($userBadge && $userBadge->earned_at)
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            Earned {{ \Carbon\Carbon::parse($userBadge->earned_at)->diffForHumans() }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- In Progress Badges -->
    @if($inProgressBadges->count() > 0)
        <div class="mb-5">
            <h3 class="fw-bold mb-4">
                <i class="fas fa-tasks me-2 text-primary"></i>In Progress ({{ $inProgressBadges->count() }})
            </h3>
            <div class="row g-4">
                @foreach($inProgressBadges as $badge)
                    @php $userBadge = $userBadges->get($badge->id); @endphp
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <a href="{{ route('badges.show', $badge->id) }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 hover-lift">
                                <div class="card-body p-4 text-center">
                                    <div class="badge-icon rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                         style="width: 90px; height: 90px; background: {{ $badge->color }}15; border: 4px dashed {{ $badge->color }};">
                                        <i class="fas {{ $badge->icon }} fs-2" style="color: {{ $badge->color }};"></i>
                                    </div>
                                    <h6 class="fw-bold mb-2">{{ $badge->name }}</h6>
                                    <span class="badge mb-2" style="background-color: {{ $badge->rarity_color }};">
                                        {{ $badge->rarity_name }}
                                    </span>
                                    <p class="text-muted small mb-3">{{ Str::limit($badge->description, 60) }}</p>
                                    @if($userBadge && $badge->required_count > 0)
                                        <div class="mb-2">
                                            <div class="progress mb-2" style="height: 10px;">
                                                <div class="progress-bar" style="width: {{ ($userBadge->progress / $badge->required_count) * 100 }}%; background-color: {{ $badge->color }};">
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $userBadge->progress }}/{{ $badge->required_count }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Locked Badges -->
    @if($lockedBadges->count() > 0)
        <div>
            <h3 class="fw-bold mb-4">
                <i class="fas fa-lock me-2 text-secondary"></i>Locked Badges ({{ $lockedBadges->count() }})
            </h3>
            <div class="row g-4">
                @foreach($lockedBadges as $badge)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <a href="{{ route('badges.show', $badge->id) }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 hover-lift opacity-75">
                                <div class="card-body p-4 text-center">
                                    <div class="badge-icon rounded-circle d-inline-flex align-items-center justify-content-center mb-3 position-relative"
                                         style="width: 90px; height: 90px; background: {{ $badge->color }}10; border: 4px solid {{ $badge->color }}30;">
                                        <i class="fas {{ $badge->icon }} fs-2 opacity-50" style="color: {{ $badge->color }};"></i>
                                        <span class="position-absolute top-50 start-50 translate-middle">
                                            <i class="fas fa-lock fs-4 text-secondary"></i>
                                        </span>
                                    </div>
                                    <h6 class="fw-bold mb-2">{{ $badge->name }}</h6>
                                    <span class="badge mb-2" style="background-color: {{ $badge->rarity_color }};">
                                        {{ $badge->rarity_name }}
                                    </span>
                                    <p class="text-muted small mb-2">{{ Str::limit($badge->description, 60) }}</p>
                                    @if($badge->required_count > 0)
                                        <small class="text-muted">
                                            <i class="fas fa-target me-1"></i>{{ $badge->required_count }} required
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
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

.badge-earned {
    border: 2px solid #10b981 !important;
}

.card {
    border-radius: 15px;
}
</style>
@endsection
