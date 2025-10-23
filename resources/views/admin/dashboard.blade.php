@extends('admin.layout')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Administrateur')
@section('page-description', 'Vue d\'ensemble de la plateforme Waste2Product')

@push('styles')
<style>
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }
</style>
@endpush

@section('content')
    <!-- Statistics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="stat-card bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500 animate-fade-in-up">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 uppercase font-semibold">Total Utilisateurs</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $userStats['total'] }}</p>
                    <p class="text-xs text-green-600 mt-1">+{{ $userStats['new_this_month'] }} ce mois</p>
                </div>
                <div class="bg-blue-100 p-4 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Waste Items -->
        <div class="stat-card bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500 animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 uppercase font-semibold">Articles Déchets</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $contentStats['waste_items'] }}</p>
                    <p class="text-xs text-blue-600 mt-1">{{ $contentStats['available_items'] }} disponibles</p>
                </div>
                <div class="bg-green-100 p-4 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Transformations -->
        <div class="stat-card bg-white rounded-lg shadow-lg p-6 border-l-4 border-purple-500 animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 uppercase font-semibold">Transformations</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $contentStats['transformations'] }}</p>
                    <p class="text-xs text-purple-600 mt-1">{{ $contentStats['published_transformations'] }} publiées</p>
                </div>
                <div class="bg-purple-100 p-4 rounded-full">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- CO2 Saved -->
        <div class="stat-card bg-white rounded-lg shadow-lg p-6 border-l-4 border-yellow-500 animate-fade-in-up" style="animation-delay: 0.3s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 uppercase font-semibold">CO₂ Économisé</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($environmentalStats['total_co2_saved'], 0) }} kg</p>
                    <p class="text-xs text-green-600 mt-1">Impact environnemental</p>
                </div>
                <div class="bg-yellow-100 p-4 rounded-full">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Users by Role -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Utilisateurs par Rôle</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Utilisateurs</p>
                            <p class="text-sm text-gray-600">Membres de la communauté</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-green-600">{{ $userStats['users'] }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Réparateurs</p>
                            <p class="text-sm text-gray-600">Experts en réparation</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-yellow-600">{{ $userStats['repairers'] }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Artisans</p>
                            <p class="text-sm text-gray-600">Créateurs et transformateurs</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-purple-600">{{ $userStats['artisans'] }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Administrateurs</p>
                            <p class="text-sm text-gray-600">Gestionnaires de la plateforme</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-red-600">{{ $userStats['admins'] }}</span>
                </div>
            </div>
        </div>

        <!-- Platform Activity -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Activité de la Plateforme</h3>
            <div class="space-y-4">
                <div class="border-l-4 border-blue-500 pl-4">
                    <p class="text-sm text-gray-600">Demandes de réparation</p>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-2xl font-bold text-gray-900">{{ $contentStats['repair_requests'] }}</p>
                        <span class="text-xs text-blue-600 bg-blue-100 px-3 py-1 rounded-full">{{ $contentStats['pending_repairs'] }} en attente</span>
                    </div>
                </div>

                <div class="border-l-4 border-green-500 pl-4">
                    <p class="text-sm text-gray-600">Événements communautaires</p>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-2xl font-bold text-gray-900">{{ $contentStats['community_events'] }}</p>
                        <span class="text-xs text-green-600 bg-green-100 px-3 py-1 rounded-full">{{ $contentStats['upcoming_events'] }} à venir</span>
                    </div>
                </div>

                <div class="border-l-4 border-purple-500 pl-4">
                    <p class="text-sm text-gray-600">Articles Marketplace</p>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-2xl font-bold text-gray-900">{{ $contentStats['marketplace_items'] }}</p>
                        <span class="text-xs text-purple-600 bg-purple-100 px-3 py-1 rounded-full">En vente</span>
                    </div>
                </div>

                <div class="border-l-4 border-yellow-500 pl-4">
                    <p class="text-sm text-gray-600">Déchets réduits (estimation)</p>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($environmentalStats['total_waste_reduced'], 1) }} kg</p>
                        <span class="text-xs text-yellow-600 bg-yellow-100 px-3 py-1 rounded-full">Impact positif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- User Growth Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Croissance des Utilisateurs</h3>
            <div class="chart-container">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        <!-- Marketplace Categories Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Catégories Marketplace</h3>
            <div class="chart-container">
                <canvas id="marketplaceCategoriesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Event Registrations Chart -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Inscriptions aux Événements</h3>
        <div class="chart-container">
            <canvas id="eventRegistrationsChart"></canvas>
        </div>
    </div>

    <!-- Recent Activity Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Recent Marketplace Items -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Articles Marketplace Récents</h3>
            <div class="space-y-3">
                @forelse($recentMarketplaceItems as $item)
                    <div class="flex items-center justify-between border-b pb-3">
                        <div class="flex-1">
                            <p class="font-medium text-sm text-gray-900">{{ Str::limit($item->title, 30) }}</p>
                            <p class="text-xs text-gray-500">{{ $item->seller->name }}</p>
                            <span class="text-xs {{ $item->status === 'available' ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $item->status === 'available' ? 'Disponible' : 'Vendu' }}
                            </span>
                        </div>
                        <span class="text-sm font-bold text-purple-600">{{ $item->price }}€</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucun article récent</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Transformations -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Transformations Récentes</h3>
            <div class="space-y-3">
                @forelse($recentTransformations as $transformation)
                    <div class="border-b pb-3">
                        <p class="font-medium text-sm text-gray-900">
                            {{ Str::limit($transformation->title, 30) }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $transformation->user->name }}</p>
                        <div class="flex items-center mt-1">
                            <span class="text-xs px-2 py-1 rounded-full
                                {{ $transformation->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($transformation->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucune transformation récente</p>
                @endforelse
            </div>
        </div>

        <!-- Top Artisans -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Artisans</h3>
            <div class="space-y-3">
                @forelse($topArtisans as $artisan)
                    <div class="flex items-center justify-between border-b pb-3">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                <span class="text-purple-600 font-bold text-sm">
                                    {{ substr($artisan->name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-sm text-gray-900">{{ $artisan->name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $artisan->transformations_count }} transformation(s)
                                </p>
                            </div>
                        </div>
                        <div class="text-purple-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucun artisan</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Derniers Utilisateurs Inscrits</h3>
            <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                Voir tous →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date d'inscription</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentUsers as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($user->avatar)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                                <span class="text-white font-bold text-lg">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $roleColors = [
                                        'user' => 'bg-green-100 text-green-800',
                                        'repairer' => 'bg-yellow-100 text-yellow-800',
                                        'artisan' => 'bg-purple-100 text-purple-800',
                                        'admin' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $roleColors[$user->role] }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('d/m/Y') }}
                                <div class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-900">
                                    Voir le profil
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    const userGrowthChart = new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($userGrowth->pluck('month')) !!},
            datasets: [{
                label: 'Nouveaux Utilisateurs',
                data: {!! json_encode($userGrowth->pluck('count')) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Marketplace Categories Chart
    const categoriesCtx = document.getElementById('marketplaceCategoriesChart').getContext('2d');
    const categoriesChart = new Chart(categoriesCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($marketplaceCategories->pluck('category')) !!},
            datasets: [{
                data: {!! json_encode($marketplaceCategories->pluck('count')) !!},
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(251, 191, 36, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(236, 72, 153, 0.8)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });

    // Event Registrations Chart
    const eventCtx = document.getElementById('eventRegistrationsChart').getContext('2d');
    const eventChart = new Chart(eventCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($eventRegistrations->pluck('month')) !!},
            datasets: [{
                label: 'Inscriptions',
                data: {!! json_encode($eventRegistrations->pluck('registrations')) !!},
                backgroundColor: 'rgba(139, 92, 246, 0.8)',
                borderColor: 'rgb(139, 92, 246)',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush
