@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="mb-5 text-center">
        <h1 class="display-4 fw-bold mb-3">
            <i class="fas fa-coins me-3 text-warning"></i>Points System
        </h1>
        <p class="lead text-muted">Earn points by participating in the community</p>
    </div>

    <!-- Points Categories -->
    @foreach($pointsConfig as $category => $actions)
        <div class="mb-5">
            <h3 class="fw-bold mb-4">
                <i class="fas fa-tag me-2"></i>{{ $category }}
            </h3>
            <div class="row g-4">
                @foreach($actions as $action)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100 hover-lift">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="badge bg-warning bg-opacity-10 text-warning p-3 rounded-circle me-3">
                                        <i class="fas {{ $action['icon'] }} fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">{{ $action['action'] }}</h5>
                                    </div>
                                </div>
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="display-6 fw-bold text-warning">+{{ $action['points'] }}</div>
                                    <small class="text-muted">points</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <!-- Level System -->
    <div class="card border-0 shadow-lg mt-5">
        <div class="card-body p-5">
            <h3 class="fw-bold mb-4 text-center">
                <i class="fas fa-layer-group me-2"></i>Level System
            </h3>
            <p class="text-center text-muted mb-4">
                Earn 100 points per level. Your title changes as you progress!
            </p>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="text-center p-4 bg-light rounded">
                        <i class="fas fa-seedling fs-1 text-success mb-3"></i>
                        <h5 class="fw-bold">Beginner</h5>
                        <p class="text-muted mb-0">Level 1-4</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4 bg-light rounded">
                        <i class="fas fa-leaf fs-1 text-primary mb-3"></i>
                        <h5 class="fw-bold">Contributor</h5>
                        <p class="text-muted mb-0">Level 5-9</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4 bg-light rounded">
                        <i class="fas fa-tree fs-1 text-info mb-3"></i>
                        <h5 class="fw-bold">Enthusiast</h5>
                        <p class="text-muted mb-0">Level 10-19</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4 bg-light rounded">
                        <i class="fas fa-award fs-1 text-warning mb-3"></i>
                        <h5 class="fw-bold">Expert</h5>
                        <p class="text-muted mb-0">Level 20-29</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4 bg-light rounded">
                        <i class="fas fa-star fs-1 text-danger mb-3"></i>
                        <h5 class="fw-bold">Champion</h5>
                        <p class="text-muted mb-0">Level 30-39</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4 bg-light rounded">
                        <i class="fas fa-trophy fs-1 text-purple mb-3"></i>
                        <h5 class="fw-bold">Master</h5>
                        <p class="text-muted mb-0">Level 40-49</p>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="text-center p-4 bg-gradient text-white rounded" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-crown fs-1 mb-3"></i>
                        <h4 class="fw-bold">Legend</h4>
                        <p class="mb-0">Level 50+</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tips -->
    <div class="card border-0 shadow-sm mt-5">
        <div class="card-body p-5">
            <h3 class="fw-bold mb-4">
                <i class="fas fa-lightbulb me-2 text-warning"></i>Tips to Earn More Points
            </h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex">
                        <i class="fas fa-check-circle text-success fs-4 me-3 mt-1"></i>
                        <div>
                            <h6 class="fw-bold">Stay Active</h6>
                            <p class="text-muted mb-0">Participate regularly in events and discussions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex">
                        <i class="fas fa-check-circle text-success fs-4 me-3 mt-1"></i>
                        <div>
                            <h6 class="fw-bold">Quality Comments</h6>
                            <p class="text-muted mb-0">Write thoughtful comments to get approval bonuses</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex">
                        <i class="fas fa-check-circle text-success fs-4 me-3 mt-1"></i>
                        <div>
                            <h6 class="fw-bold">Create Events</h6>
                            <p class="text-muted mb-0">Organize community events for maximum points</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex">
                        <i class="fas fa-check-circle text-success fs-4 me-3 mt-1"></i>
                        <div>
                            <h6 class="fw-bold">Complete Transformations</h6>
                            <p class="text-muted mb-0">Turn waste into value for big point rewards</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="text-center mt-5">
        <a href="{{ route('gamification.profile') }}" class="btn btn-lg btn-primary">
            <i class="fas fa-user me-2"></i>View My Progress
        </a>
        <a href="{{ route('leaderboard.index') }}" class="btn btn-lg btn-outline-primary">
            <i class="fas fa-trophy me-2"></i>View Leaderboard
        </a>
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

.card {
    border-radius: 15px;
}

.text-purple {
    color: #8b5cf6;
}
</style>
@endsection
