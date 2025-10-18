<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Waste2Product - Sustainable Living Platform')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand { font-weight: bold; }
        .sidebar { min-height: 100vh; box-shadow: 2px 0 5px rgba(0,0,0,.1); }
        .content-wrapper { background-color: #f8f9fa; min-height: 100vh; }
        .content-wrapper-full { background-color: #f8f9fa; min-height: 100vh; padding: 0; }
        .card { border-radius: 10px; }
        .btn-success { background-color: #28a745; border-color: #28a745; }
        .btn-success:hover { background-color: #218838; border-color: #1e7e34; }
        .stats-card { background: linear-gradient(45deg, #28a745, #20c997); color: white; }
        .stats-card .card-body { padding: 1.5rem; }
        .stats-icon { font-size: 2rem; opacity: 0.8; }
        .environmental-impact { background: linear-gradient(45deg, #17a2b8, #6f42c1); color: white; }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    <!-- Navigation (Only for authenticated users) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/waste2product_logo.png') }}" alt="Waste2Product" style="height: 35px; width: auto; margin-right: 10px;">
                Waste2Product
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('waste-items.my') ? 'active' : '' }}" href="{{ route('waste-items.my') }}">
                            <i class="fas fa-user"></i> My Items
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}" href="{{ route('events.index') }}">
                            <i class="fas fa-calendar-alt"></i> Events
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('marketplace.*') ? 'active' : '' }}" href="{{ route('marketplace.index') }}">
                            <i class="fas fa-store"></i> Marketplace
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            @if(auth()->user()->avatar)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}"
                                     class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                            @else
                                <i class="fas fa-user-circle"></i>
                            @endif
                            {{ auth()->user()->name }}
                            <span class="badge bg-light text-dark ms-1">{{ auth()->user()->role }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i>Mon profil</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user-edit me-2"></i>Modifier profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('waste-items.my') }}"><i class="fas fa-recycle me-2"></i>Mes articles</a></li>
                            @if(auth()->user()->role === 'repairer')
                                <li><a class="dropdown-item" href="{{ route('repairs.index') }}"><i class="fas fa-tools me-2"></i>Mes réparations</a></li>
                            @endif
                            @if(auth()->user()->role === 'artisan')
                                <li><a class="dropdown-item" href="{{ route('transformations.index') }}"><i class="fas fa-magic me-2"></i>Mes transformations</a></li>
                            @endif
                            @if(auth()->user()->role === 'admin')
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="{{ route('admin.dashboard') }}"><i class="fas fa-user-shield me-2"></i>Panel Admin</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (Only for authenticated users) -->
            <div class="col-md-2 bg-white sidebar p-0">
                <div class="list-group list-group-flush">
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>

                    <!-- Waste Items Section -->
                    <div class="list-group-item bg-light text-muted small fw-bold">
                        <i class="fas fa-recycle me-2"></i>WASTE MANAGEMENT
                    </div>
                    <a href="{{ route('waste-items.my') }}" class="list-group-item list-group-item-action {{ request()->routeIs('waste-items.my') ? 'active' : '' }}">
                        <i class="fas fa-user me-2"></i>My Items
                    </a>

                    <!-- Community Section -->
                    <div class="list-group-item bg-light text-muted small fw-bold mt-2">
                        <i class="fas fa-users me-2"></i>COMMUNITY
                    </div>
                    <a href="{{ route('events.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('events.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt me-2"></i>Events
                    </a>
                    <a href="{{ route('event-comments.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('event-comments.*') ? 'active' : '' }}">
                        <i class="fas fa-comments me-2"></i>Event Comments
                    </a>
                    <a href="{{ route('marketplace.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('marketplace.*') ? 'active' : '' }}">
                        <i class="fas fa-store me-2"></i>Marketplace
                    </a>

                    <!-- Gamification Section -->
                    <div class="list-group-item bg-light text-muted small fw-bold mt-2">
                        <i class="fas fa-trophy me-2"></i>GAMIFICATION
                    </div>
                    <a href="{{ route('gamification.profile') }}" class="list-group-item list-group-item-action {{ request()->routeIs('gamification.profile') ? 'active' : '' }}">
                        <i class="fas fa-user-circle me-2"></i>My Profile
                        @if(auth()->user()->total_points > 0)
                            <span class="badge bg-warning text-dark float-end">{{ number_format(auth()->user()->total_points) }} pts</span>
                        @endif
                    </a>
                    <a href="{{ route('badges.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('badges.*') ? 'active' : '' }}">
                        <i class="fas fa-award me-2"></i>Badges
                        @if(auth()->user()->earnedBadges && auth()->user()->earnedBadges()->count() > 0)
                            <span class="badge bg-success float-end">{{ auth()->user()->earnedBadges()->count() }}</span>
                        @endif
                    </a>
                    <a href="{{ route('leaderboard.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('leaderboard.*') ? 'active' : '' }}">
                        <i class="fas fa-crown me-2"></i>Leaderboard
                    </a>
                    <a href="{{ route('gamification.points-info') }}" class="list-group-item list-group-item-action {{ request()->routeIs('gamification.points-info') ? 'active' : '' }}">
                        <i class="fas fa-coins me-2"></i>Points Info
                    </a>

                    @if(auth()->user()->role === 'repairer')
                        <!-- Repairer Section -->
                        <div class="list-group-item bg-light text-muted small fw-bold mt-2">
                            <i class="fas fa-tools me-2"></i>REPAIR SERVICES
                        </div>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-wrench me-2"></i>Repair Requests
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-clipboard-list me-2"></i>My Repairs
                        </a>
                    @endif

                    @if(auth()->user()->role === 'artisan')
                        <!-- Artisan Section -->
                        <div class="list-group-item bg-light text-muted small fw-bold mt-2">
                            <i class="fas fa-magic me-2"></i>TRANSFORMATIONS
                        </div>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-palette me-2"></i>Browse Projects
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-plus me-2"></i>New Project
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-edit me-2"></i>My Projects
                        </a>
                    @endif
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 content-wrapper">
                <div class="container-fluid py-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Content for non-authenticated users (full width) -->
    <div class="content-wrapper-full">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
