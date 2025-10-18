@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-5">
    <!-- Hero Section with User Stats -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
                <div class="card-body p-5 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle border border-4 border-white" width="120" height="120">
                            @else
                                <div class="rounded-circle bg-white text-primary d-inline-flex align-items-center justify-content-center border border-4 border-white" style="width: 120px; height: 120px;">
                                    <span class="display-4 fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-7">
                            <h1 class="display-5 fw-bold mb-2">{{ $user->name }}</h1>
                            <p class="lead mb-3">
                                <i class="fas fa-certificate me-2"></i>{{ $stats['level_title'] }}
                            </p>
                            <div class="d-flex gap-4">
                                <div>
                                    <div class="fs-4 fw-bold">{{ number_format($stats['total_points']) }}</div>
                                    <small class="opacity-75">Total Points</small>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold">Level {{ $stats['current_level'] }}</div>
                                    <small class="opacity-75">Current Level</small>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold">#{{ $stats['rank'] }}</div>
                                    <small class="opacity-75">Global Rank</small>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold">{{ $stats['badges_earned'] }}/{{ $stats['total_badges'] }}</div>
                                    <small class="opacity-75">Badges</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <div class="d-flex justify-content-between mb-2">
                                    <small>Level Progress</small>
                                    <small>{{ round($stats['progress_to_next_level']) }}%</small>
                                </div>
                                <div class="progress" style="height: 25px; background-color: rgba(255,255,255,0.3);">
                                    <div class="progress-bar bg-warning fw-bold" role="progressbar" style="width: {{ $stats['progress_to_next_level'] }}%">
                                        {{ round($stats['progress_to_next_level']) }}%
                                    </div>
                                </div>
                            </div>
                            <small class="opacity-75">
                                {{ $stats['points_to_next_level'] }} points to Level {{ $stats['current_level'] + 1 }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Points Breakdown -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Points Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    @if($pointsBreakdown->count() > 0)
                        <div class="row g-3">
                            @foreach($pointsBreakdown as $item)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">{{ ucfirst(str_replace('_', ' ', $item->type)) }}</h6>
                                            <small class="text-muted">{{ $item->count }} actions</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fs-4 fw-bold text-warning">{{ number_format($item->total_points) }}</div>
                                            <small class="text-muted">points</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No points earned yet. Start participating!</p>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-history me-2"></i>Recent Activity
                    </h5>
                    <a href="{{ route('gamification.activity') }}" class="btn btn-sm btn-outline-primary">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentTransactions->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentTransactions as $transaction)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="badge p-3 me-3" style="background-color: {{ $transaction->color }}15;">
                                            <i class="fas {{ $transaction->icon }} fs-5" style="color: {{ $transaction->color }};"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $transaction->description }}</h6>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>{{ $transaction->time_ago }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fs-5 fw-bold" style="color: {{ $transaction->color }};">
                                                {{ $transaction->formatted_points }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-5">No activity yet</p>
                    @endif
                </div>
            </div>

            <!-- In Progress Badges -->
            @if($inProgressBadges->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-tasks me-2"></i>Badges In Progress
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($inProgressBadges as $userBadge)
                                @php $badge = $userBadge->badge; @endphp
                                <div class="col-md-6">
                                    <a href="{{ route('badges.show', $badge->id) }}" class="text-decoration-none">
                                        <div class="card border-0 bg-light h-100 hover-lift">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="badge-icon-sm rounded-circle d-flex align-items-center justify-content-center me-3"
                                                         style="width: 50px; height: 50px; background: {{ $badge->color }}15; border: 3px solid {{ $badge->color }};">
                                                        <i class="fas {{ $badge->icon }}" style="color: {{ $badge->color }};"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="fw-bold mb-1">{{ $badge->name }}</h6>
                                                        <small class="text-muted">{{ $userBadge->progress }}/{{ $badge->required_count }}</small>
                                                    </div>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar" style="width: {{ $userBadge->progress_percentage }}%; background-color: {{ $badge->color }};">
                                                    </div>
                                                </div>
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

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Earned Badges -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-trophy me-2"></i>Recent Badges
                    </h5>
                    <a href="{{ route('badges.collection') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @if($earnedBadges->count() > 0)
                        <div class="row g-2">
                            @foreach($earnedBadges as $badge)
                                <div class="col-6">
                                    <a href="{{ route('badges.show', $badge->id) }}" class="text-decoration-none">
                                        <div class="card border-0 bg-light text-center p-3 hover-lift">
                                            <div class="badge-icon rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-2"
                                                 style="width: 60px; height: 60px; background: {{ $badge->color }}15; border: 3px solid {{ $badge->color }};">
                                                <i class="fas {{ $badge->icon }} fs-5" style="color: {{ $badge->color }};"></i>
                                            </div>
                                            <small class="fw-bold">{{ $badge->name }}</small>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No badges earned yet</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('events.create') }}" class="btn btn-primary">
                            <i class="fas fa-calendar-plus me-2"></i>Create Event (50 pts)
                        </a>
                        <a href="{{ route('events.index') }}" class="btn btn-success">
                            <i class="fas fa-users me-2"></i>Attend Event (30 pts)
                        </a>
                        <a href="{{ route('waste-items.create') }}" class="btn btn-info text-white">
                            <i class="fas fa-recycle me-2"></i>Post Waste Item (15 pts)
                        </a>
                        <a href="{{ route('gamification.points-info') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-info-circle me-2"></i>View All Points
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.card {
    border-radius: 15px;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>
@endsection
