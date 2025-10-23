@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-5">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 20px;">
                <div class="card-body p-5 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="display-4 fw-bold mb-3">
                                <i class="fas fa-crown me-3"></i>Leaderboard
                            </h1>
                            <p class="lead mb-0">Compete with the community and climb to the top!</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            @auth
                                <div class="badge bg-white text-primary px-4 py-3 fs-5">
                                    <i class="fas fa-medal me-2"></i>Your Rank: #{{ $userRank ?? 'N/A' }}
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
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="badge bg-primary bg-opacity-10 text-primary p-3 rounded-circle mb-3">
                        <i class="fas fa-users fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $stats['total_users'] }}</h3>
                    <p class="text-muted mb-0">Total Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="badge bg-success bg-opacity-10 text-success p-3 rounded-circle mb-3">
                        <i class="fas fa-chart-line fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $stats['active_this_week'] }}</h3>
                    <p class="text-muted mb-0">Active This Week</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="badge bg-warning bg-opacity-10 text-warning p-3 rounded-circle mb-3">
                        <i class="fas fa-coins fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-1">{{ number_format($stats['total_points_awarded']) }}</h3>
                    <p class="text-muted mb-0">Points Awarded</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="badge bg-info bg-opacity-10 text-info p-3 rounded-circle mb-3">
                        <i class="fas fa-layer-group fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $stats['avg_user_level'] }}</h3>
                    <p class="text-muted mb-0">Avg Level</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 3 Weekly Performers -->
    @if($weeklyTop->count() >= 3)
        <div class="row mb-5">
            <div class="col-12">
                <h4 class="fw-bold mb-3">
                    <i class="fas fa-fire me-2 text-danger"></i>Top Performers This Week
                </h4>
            </div>
            <!-- 2nd Place -->
            <div class="col-md-4 order-md-1">
                <div class="card border-0 shadow-sm podium-card" style="margin-top: 40px;">
                    <div class="card-body text-center p-4">
                        <div class="podium-number bg-secondary text-white">2</div>
                        @if($weeklyTop[1]->avatar)
                            <img src="{{ asset('storage/' . $weeklyTop[1]->avatar) }}" alt="{{ $weeklyTop[1]->name }}" class="rounded-circle mb-3" width="80" height="80">
                        @else
                            <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <span class="fs-2 fw-bold">{{ strtoupper(substr($weeklyTop[1]->name, 0, 1)) }}</span>
                            </div>
                        @endif
                        <h5 class="fw-bold">{{ $weeklyTop[1]->name }}</h5>
                        <p class="text-muted mb-2">Level {{ $weeklyTop[1]->current_level }}</p>
                        <div class="badge bg-secondary px-3 py-2">
                            <i class="fas fa-coins me-1"></i>{{ $weeklyTop[1]->point_transactions_sum_points ?? 0 }} pts this week
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1st Place -->
            <div class="col-md-4 order-md-2">
                <div class="card border-0 shadow-lg podium-card podium-winner">
                    <div class="card-body text-center p-4">
                        <div class="podium-number bg-warning text-white">
                            <i class="fas fa-crown"></i>
                        </div>
                        @if($weeklyTop[0]->avatar)
                            <img src="{{ asset('storage/' . $weeklyTop[0]->avatar) }}" alt="{{ $weeklyTop[0]->name }}" class="rounded-circle mb-3 border border-4 border-warning" width="100" height="100">
                        @else
                            <div class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center mb-3 border border-4 border-warning" style="width: 100px; height: 100px;">
                                <span class="fs-1 fw-bold">{{ strtoupper(substr($weeklyTop[0]->name, 0, 1)) }}</span>
                            </div>
                        @endif
                        <h4 class="fw-bold">{{ $weeklyTop[0]->name }}</h4>
                        <p class="text-muted mb-2">Level {{ $weeklyTop[0]->current_level }}</p>
                        <div class="badge bg-warning px-3 py-2">
                            <i class="fas fa-coins me-1"></i>{{ $weeklyTop[0]->point_transactions_sum_points ?? 0 }} pts this week
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3rd Place -->
            <div class="col-md-4 order-md-3">
                <div class="card border-0 shadow-sm podium-card" style="margin-top: 60px;">
                    <div class="card-body text-center p-4">
                        <div class="podium-number bg-danger text-white">3</div>
                        @if($weeklyTop[2]->avatar)
                            <img src="{{ asset('storage/' . $weeklyTop[2]->avatar) }}" alt="{{ $weeklyTop[2]->name }}" class="rounded-circle mb-3" width="70" height="70">
                        @else
                            <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                                <span class="fs-3 fw-bold">{{ strtoupper(substr($weeklyTop[2]->name, 0, 1)) }}</span>
                            </div>
                        @endif
                        <h6 class="fw-bold">{{ $weeklyTop[2]->name }}</h6>
                        <p class="text-muted mb-2 small">Level {{ $weeklyTop[2]->current_level }}</p>
                        <div class="badge bg-danger px-3 py-2">
                            <i class="fas fa-coins me-1"></i>{{ $weeklyTop[2]->point_transactions_sum_points ?? 0 }} pts this week
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('leaderboard.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-calendar me-2"></i>Time Period
                    </label>
                    <select name="period" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ $period == 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>This Week</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-filter me-2"></i>Category
                    </label>
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="overall" {{ $category == 'overall' ? 'selected' : '' }}>Overall</option>
                        <option value="events" {{ $category == 'events' ? 'selected' : '' }}>Events</option>
                        <option value="comments" {{ $category == 'comments' ? 'selected' : '' }}>Comments</option>
                        <option value="marketplace" {{ $category == 'marketplace' ? 'selected' : '' }}>Marketplace</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Leaderboard Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0">
                <i class="fas fa-list-ol me-2"></i>Rankings
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Rank</th>
                            <th class="py-3">User</th>
                            <th class="py-3">Level</th>
                            <th class="py-3">Points</th>
                            <th class="py-3">Badges</th>
                            <th class="py-3">Title</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                            <tr class="{{ auth()->check() && $user->id == auth()->id() ? 'table-primary' : '' }}">
                                <td class="px-4 py-3">
                                    @if($user->rank <= 3)
                                        <span class="badge {{ $user->rank == 1 ? 'bg-warning' : ($user->rank == 2 ? 'bg-secondary' : 'bg-danger') }} fs-6">
                                            #{{ $user->rank }}
                                        </span>
                                    @else
                                        <span class="text-muted fw-bold">#{{ $user->rank }}</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        @if($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle me-3" width="40" height="40">
                                        @else
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <span class="fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $user->name }}</div>
                                            @if(auth()->check() && $user->id == auth()->id())
                                                <small class="text-primary">You</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-info px-3 py-2">
                                        <i class="fas fa-layer-group me-1"></i>{{ $user->current_level }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-warning text-dark px-3 py-2">
                                        <i class="fas fa-coins me-1"></i>
                                        @if($period != 'all' && isset($user->period_points))
                                            {{ number_format($user->period_points) }}
                                        @else
                                            {{ number_format($user->total_points) }}
                                        @endif
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-trophy me-1"></i>{{ $user->earnedBadges->count() }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="text-muted">{{ $user->title ?? $user->level_title }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-users fs-1 text-muted mb-3"></i>
                                    <p class="text-muted">No users found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.podium-card {
    position: relative;
    transition: all 0.3s ease;
}

.podium-card:hover {
    transform: translateY(-10px);
}

.podium-winner {
    border: 3px solid #fbbf24 !important;
}

.podium-number {
    position: absolute;
    top: -20px;
    left: 50%;
    transform: translateX(-50%);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}
</style>
@endsection
