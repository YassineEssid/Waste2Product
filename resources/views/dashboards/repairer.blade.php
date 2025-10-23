@extends('layouts.app')

@section('title', 'Repairer Dashboard - Waste2Product')

@section('content')
<div class="container py-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Repairer Dashboard 🔧</h1>
                    <p class="text-muted">Welcome {{ $user->name }}, manage your repairs here</p>
                </div>
                <div>
                    <span class="badge bg-info px-3 py-2">
                        <i class="fas fa-tools me-2"></i>Expert Repairer
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['pending_repairs'] }}</h3>
                            <p class="mb-0 small">Pending Requests</p>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['my_accepted_repairs'] }}</h3>
                            <p class="mb-0 small">In Progress</p>
                        </div>
                        <i class="fas fa-cog fa-spin fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['my_completed_repairs'] }}</h3>
                            <p class="mb-0 small">Completed</p>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #6f42c1 0%, #9b59b6 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['total_earnings'], 2) }}€</h3>
                            <p class="mb-0 small">Total Earnings</p>
                        </div>
                        <i class="fas fa-euro-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - New Repair Requests & Active Repairs -->
        <div class="col-lg-8 mb-4">
            <!-- Pending Repairs -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-inbox me-2"></i>New Repair Requests</h5>
                </div>
                <div class="card-body">
                    @if($pendingRepairs->count() > 0)
                        @foreach($pendingRepairs as $repair)
                        <div class="card mb-3 border-start border-warning border-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">{{ $repair->wasteItem->title ?? 'Item' }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>{{ $repair->user->name }}
                                        </small>
                                    </div>
                                    <span class="badge bg-warning">Waiting</span>
                                </div>
                                <p class="mb-2 small">{{ Str::limit($repair->description, 100) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>{{ $repair->created_at->diffForHumans() }}
                                    </small>
                                    <div>
                                        <a href="{{ route('repairs.show', $repair) }}" class="btn btn-sm btn-outline-warning me-2">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                        <form action="{{ route('repairs.assign', $repair) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning text-white">
                                                <i class="fas fa-hand-paper me-1"></i>Accept
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-double fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No new repair requests at the moment</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- My Active Repairs -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-wrench me-2"></i>Repair Requests in Progress</h5>
                </div>
                <div class="card-body">
                    @if($myActiveRepairs->count() > 0)
                        @foreach($myActiveRepairs as $repair)
                        <div class="card mb-3 border-start border-info border-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">{{ $repair->wasteItem->title ?? 'Item' }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>{{ $repair->user->name }}
                                        </small>
                                    </div>
                                    <span class="badge bg-info text-white">In Progress</span>
                                </div>
                                <p class="mb-2 small">{{ Str::limit($repair->description, 100) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        @if($repair->estimated_cost)
                                            <strong>{{ number_format($repair->estimated_cost, 2) }}€</strong>
                                        @endif
                                    </small>
                                    <div>
                                        <a href="{{ route('repairs.show', $repair) }}" class="btn btn-sm btn-outline-info me-2">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No repair requests in progress</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Profile & Stats -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-user-circle text-info me-2"></i>My Profile</h5>
                </div>
                <div class="card-body text-center">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" 
                             class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 100px; height: 100px;">
                            <span class="fs-1 text-info fw-bold">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <h5>{{ $user->name }}</h5>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <a href="{{ route('profile.show') }}" class="btn btn-outline-info w-100 mb-2">
                        <i class="fas fa-user me-2"></i>View Profile
                    </a>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </a>
                </div>
            </div>

            <!-- Recent Completed Repairs -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activity</h5>
                </div>
                <div class="card-body">
                    @if($myCompletedRepairs->count() > 0)
                        @foreach($myCompletedRepairs as $repair)
                        <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <h6 class="mb-1">{{ $repair->wasteItem->title ?? 'Item' }}</h6>
                            <small class="text-muted d-block">
                                <i class="fas fa-check-circle text-success me-1"></i>
                                Completed {{ $repair->updated_at->diffForHumans() }}
                            </small>
                            @if($repair->actual_cost)
                            <small class="text-success fw-bold">{{ number_format($repair->actual_cost, 2) }}€</small>
                            @endif
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center mb-0 small">No completed repairs</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
