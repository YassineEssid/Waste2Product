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

        /* Message notification badge */
        #messagesIcon {
            position: relative;
            transition: all 0.3s ease;
        }
        #messagesIcon:hover {
            transform: scale(1.1);
        }
        #messagesBadge {
            animation: pulse 2s infinite;
            box-shadow: 0 0 10px rgba(220, 53, 69, 0.5);
        }
        @keyframes pulse {
            0%, 100% { transform: translate(-50%, -50%) scale(1); }
            50% { transform: translate(-50%, -50%) scale(1.1); }
        }
        #messagesBadge.new-message {
            animation: bounce 0.5s ease;
        }
        @keyframes bounce {
            0%, 100% { transform: translate(-50%, -50%) scale(1); }
            50% { transform: translate(-50%, -50%) scale(1.3); }
        }

        /* Messages Dropdown Styles */
        #messagesDropdown {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        .conversation-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .conversation-item:hover {
            background-color: #f8f9fa;
            text-decoration: none;
        }
        .conversation-item:last-child {
            border-bottom: none;
        }
        .conversation-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .conversation-unread-badge {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #dc3545;
            color: white;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .conversation-item .text-muted {
            font-size: 0.85rem;
        }
        .no-conversations {
            padding: 2rem 1rem;
            text-align: center;
            color: #6c757d;
        }

        /* Validation Error Styles */
        .invalid-feedback {
            display: block !important;
            color: #dc3545 !important;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            font-weight: 500;
        }

        .is-invalid {
            border-color: #dc3545 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .alert-danger {
            background-color: #f8d7da !important;
            border-color: #f5c6cb !important;
            color: #721c24 !important;
        }

        .alert-danger ul {
            margin-bottom: 0;
            padding-left: 1.5rem;
        }

        .alert-danger li {
            color: #dc3545 !important;
            font-weight: 500;
        }
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
                    <!-- Messages Dropdown with Notification Badge -->
                    <li class="nav-item dropdown position-relative me-3">
                        <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="messagesIcon">
                            <i class="fas fa-envelope fa-lg"></i>
                            <span id="messagesBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.65rem;">
                                0
                            </span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end shadow-lg" style="width: 350px; max-height: 500px;" id="messagesDropdown">
                            <div class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2 bg-light">
                                <h6 class="mb-0"><i class="fas fa-envelope me-2"></i>Messages</h6>
                                <span class="badge bg-primary" id="dropdownUnreadCount">0</span>
                            </div>

                            <div id="conversationsList" style="max-height: 350px; overflow-y: auto;">
                                <!-- Conversations will be loaded here via AJAX -->
                                <div class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted small mt-2 mb-0">Chargement...</p>
                                </div>
                            </div>

                            <div class="dropdown-divider my-0"></div>
                            <a class="dropdown-item text-center text-primary fw-bold py-2" href="{{ route('messages.index') }}">
                                <i class="fas fa-comments me-2"></i>Voir tous les messages
                            </a>
                        </div>
                    </li>

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
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user-edit me-2"></i>Edit Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('waste-items.my') }}"><i class="fas fa-recycle me-2"></i>My Items</a></li>
                            @if(auth()->user()->role === 'repairer')
                                <li><a class="dropdown-item" href="{{ route('repairs.index') }}"><i class="fas fa-tools me-2"></i>My Repairs</a></li>
                            @endif
                            @if(auth()->user()->role === 'artisan')
                                <li><a class="dropdown-item" href="{{ route('transformations.index') }}"><i class="fas fa-magic me-2"></i>My Transformations</a></li>
                            @endif
                            @if(auth()->user()->role === 'admin')
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="{{ route('admin.dashboard') }}"><i class="fas fa-user-shield me-2"></i>Admin Panel</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
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
                       </a>

@if(auth()->user()->role === 'user')
                        <a href="{{ route('repairs.my') }}" class="list-group-item list-group-item-action {{ request()->routeIs('repairs.my') ? 'active' : '' }}">
    <i class="fas fa-clipboard-list me-2"></i>My Repair Requests
</a>
@endif
                    @if(auth()->user()->role === 'repairer')
                        <!-- Repairer Section -->
                        <div class="list-group-item bg-light text-muted small fw-bold mt-2">
                            <i class="fas fa-tools me-2"></i>REPAIR SERVICES
                        </div>
                       <a href="{{ route('repairs.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('repairs.index') ? 'active' : '' }}">
        <i class="fas fa-wrench me-2"></i>Repair Requests
    </a>
                        <a href="{{ route('repairs.my') }}" class="list-group-item list-group-item-action {{ request()->routeIs('repairs.my') ? 'active' : '' }}">
    <i class="fas fa-clipboard-list me-2"></i>My Repairs
</a>
                    @endif

                    @if(auth()->user()->role === 'artisan')
                        <!-- Artisan Section -->
                        <div class="list-group-item bg-light text-muted small fw-bold mt-2">
                            <i class="fas fa-magic me-2"></i>TRANSFORMATIONS
                        </div>
                        <a href="{{ route('transformations.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('transformations.index') && !request()->has('my') ? 'active' : '' }}">
                            <i class="fas fa-palette me-2"></i>Browse Projects
                        </a>
                        <a href="{{ route('transformations.create') }}" class="list-group-item list-group-item-action {{ request()->routeIs('transformations.create') ? 'active' : '' }}">
                            <i class="fas fa-plus me-2"></i>New Project
                        </a>
                        <a href="{{ route('transformations.index') }}?my=1" class="list-group-item list-group-item-action {{ request()->has('my') ? 'active' : '' }}">
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

    @auth
    <!-- Message Notification and Dropdown Script -->
    <script>
        (function() {
            const messagesBadge = document.getElementById('messagesBadge');
            const messagesIcon = document.getElementById('messagesIcon');
            const conversationsList = document.getElementById('conversationsList');
            const dropdownUnreadCount = document.getElementById('dropdownUnreadCount');
            let lastUnreadCount = 0;
            let conversationsLoaded = false;

            // Function to load conversations
            function loadConversations() {
                if (conversationsLoaded) return;

                conversationsList.innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted small mt-2 mb-0">Chargement...</p>
                    </div>
                `;

                fetch('{{ route("messages.recent") }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    conversationsLoaded = true;
                    const conversations = data.conversations || [];

                    if (conversations.length === 0) {
                        conversationsList.innerHTML = `
                            <div class="no-conversations">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="mb-0">Aucune conversation</p>
                                <small class="text-muted">Commencez par contacter un vendeur</small>
                            </div>
                        `;
                        return;
                    }

                    conversationsList.innerHTML = conversations.map(conv => {
                        const avatarHtml = conv.other_user_avatar
                            ? `<img src="${conv.other_user_avatar}" alt="${conv.other_user_name}" class="conversation-avatar">`
                            : `<div class="conversation-avatar">${conv.other_user_name.charAt(0).toUpperCase()}</div>`;

                        const unreadBadge = conv.unread_count > 0
                            ? `<div class="conversation-unread-badge">${conv.unread_count}</div>`
                            : '';

                        return `
                            <a href="${conv.url}" class="conversation-item">
                                <div class="d-flex align-items-start">
                                    ${avatarHtml}
                                    <div class="ms-3 flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="fw-bold">${conv.other_user_name}</div>
                                                <small class="text-muted">${conv.item_title}</small>
                                            </div>
                                            ${unreadBadge}
                                        </div>
                                        <div class="text-muted small mt-1">${conv.last_message}</div>
                                        <small class="text-muted">${conv.last_message_time}</small>
                                    </div>
                                </div>
                            </a>
                        `;
                    }).join('');
                })
                .catch(error => {
                    console.error('Error loading conversations:', error);
                    conversationsList.innerHTML = `
                        <div class="no-conversations">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                            <p class="mb-0">Erreur de chargement</p>
                        </div>
                    `;
                });
            }

            // Load conversations when dropdown is opened
            messagesIcon.addEventListener('click', function(e) {
                setTimeout(loadConversations, 100);
            });

            // Function to update unread message count
            function updateUnreadCount() {
                fetch('{{ route("messages.unread.count") }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const unreadCount = data.unread_count || 0;

                    // Update badge
                    if (unreadCount > 0) {
                        messagesBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                        messagesBadge.style.display = 'inline-block';
                        dropdownUnreadCount.textContent = unreadCount;

                        // Add bounce animation if count increased
                        if (unreadCount > lastUnreadCount) {
                            messagesBadge.classList.add('new-message');
                            setTimeout(() => {
                                messagesBadge.classList.remove('new-message');
                            }, 500);

                            // Reload conversations if dropdown is open
                            if (messagesIcon.getAttribute('aria-expanded') === 'true') {
                                conversationsLoaded = false;
                                loadConversations();
                            }
                        }
                    } else {
                        messagesBadge.style.display = 'none';
                        dropdownUnreadCount.textContent = '0';
                    }

                    lastUnreadCount = unreadCount;
                })
                .catch(error => {
                    console.error('Error fetching unread count:', error);
                });
            }

            // Initial check
            updateUnreadCount();

            // Poll every 3 seconds
            setInterval(updateUnreadCount, 3000);

            // Also check when user returns to the page
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    updateUnreadCount();
                }
            });
        })();
    </script>
    @endauth

    @stack('scripts')
</body>
</html>
