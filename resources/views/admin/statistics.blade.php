@extends('admin.layout')

@section('title', 'Statistiques')
@section('page-title', 'Statistiques Avancées')
@section('page-description', 'Analyses détaillées et métriques de performance')

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
        height: 350px;
    }
    .metric-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
        transition: transform 0.3s ease;
    }
    .metric-card:hover {
        transform: scale(1.05);
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
    <!-- Quick Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg shadow-lg p-6 text-white animate-fade-in-up">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase opacity-80">Total Utilisateurs</p>
                    <p class="text-4xl font-bold mt-2">{{ array_sum($usersByRole) }}</p>
                    <p class="text-xs mt-1 opacity-75">Tous rôles confondus</p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Waste Items -->
        <div class="stat-card bg-gradient-to-br from-green-500 to-green-700 rounded-lg shadow-lg p-6 text-white animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase opacity-80">Articles Déchets</p>
                    <p class="text-4xl font-bold mt-2">{{ $activityStats['waste_items_this_month'] }}</p>
                    <p class="text-xs mt-1 opacity-75">Ce mois-ci</p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Repairs -->
        <div class="stat-card bg-gradient-to-br from-yellow-500 to-orange-600 rounded-lg shadow-lg p-6 text-white animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase opacity-80">Réparations</p>
                    <p class="text-4xl font-bold mt-2">{{ $activityStats['repairs_this_month'] }}</p>
                    <p class="text-xs mt-1 opacity-75">Ce mois-ci</p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- CO2 Saved -->
        <div class="stat-card bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg shadow-lg p-6 text-white animate-fade-in-up" style="animation-delay: 0.3s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase opacity-80">CO₂ Économisé</p>
                    <p class="text-4xl font-bold mt-2">{{ number_format($environmentalImpact['total_co2_saved'], 1) }}</p>
                    <p class="text-xs mt-1 opacity-75">Kilogrammes</p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- User Registration Trend -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    Croissance des Inscriptions (12 mois)
                </span>
            </h3>
            <div class="chart-container">
                <canvas id="userRegistrationChart"></canvas>
            </div>
        </div>

        <!-- Users by Role -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                    </svg>
                    Répartition par Rôle
                </span>
            </h3>
            <div class="chart-container">
                <canvas id="usersByRoleChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Activity Comparison -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <span class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Comparaison Mensuelle d'Activité
            </span>
        </h3>
        <div class="chart-container">
            <canvas id="activityComparisonChart"></canvas>
        </div>
    </div>

    <!-- Environmental Impact Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="metric-card">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-semibold">Impact CO₂</h4>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold mb-2">{{ number_format($environmentalImpact['total_co2_saved'], 1) }} kg</p>
            <p class="text-sm opacity-80">CO₂ total économisé</p>
            <div class="mt-4 bg-white bg-opacity-20 rounded-lg p-3">
                <p class="text-xs font-semibold">Équivalent à :</p>
                <p class="text-sm">{{ number_format($environmentalImpact['total_co2_saved'] / 4.6, 0) }} km en voiture</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-semibold">Déchets Réduits</h4>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <p class="text-3xl font-bold mb-2">{{ number_format($environmentalImpact['waste_reduced_kg'], 1) }} kg</p>
            <p class="text-sm opacity-80">Déchets détournés</p>
            <div class="mt-4 bg-white bg-opacity-20 rounded-lg p-3">
                <p class="text-xs font-semibold">Performance :</p>
                <p class="text-sm">{{ $environmentalImpact['items_recycled'] }} articles recyclés</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-semibold">Taux de Recyclage</h4>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            @php
                $totalItems = $activityStats['waste_items_this_month'] + $activityStats['waste_items_last_month'];
                $recyclingRate = $totalItems > 0 ? ($environmentalImpact['items_recycled'] / $totalItems) * 100 : 0;
            @endphp
            <p class="text-3xl font-bold mb-2">{{ number_format($recyclingRate, 1) }}%</p>
            <p class="text-sm opacity-80">Articles transformés</p>
            <div class="mt-4 bg-white bg-opacity-20 rounded-lg p-3">
                <div class="w-full bg-white bg-opacity-20 rounded-full h-2">
                    <div class="bg-white rounded-full h-2" style="width: {{ min($recyclingRate, 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Contributors -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">
            <span class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                Top 10 Contributeurs
            </span>
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Articles</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Réparations</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($topContributors as $index => $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($index < 3)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                            {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $index === 1 ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $index === 2 ? 'bg-orange-100 text-orange-800' : '' }}
                                            font-bold text-sm">
                                            {{ $index + 1 }}
                                        </span>
                                    @else
                                        <span class="text-gray-600 font-semibold">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                            </td>
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
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-semibold text-gray-900">{{ $user->waste_items_count }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-semibold text-gray-900">{{ $user->repair_requests_count }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-lg font-bold text-blue-600">
                                    {{ $user->waste_items_count + $user->repair_requests_count }}
                                </span>
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
    // User Registration Trend Chart
    const userRegistrationCtx = document.getElementById('userRegistrationChart').getContext('2d');
    const userRegistrationChart = new Chart(userRegistrationCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($userRegistrationTrend->pluck('month')) !!},
            datasets: [{
                label: 'Inscriptions',
                data: {!! json_encode($userRegistrationTrend->pluck('count')) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
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

    // Users by Role Chart
    const usersByRoleCtx = document.getElementById('usersByRoleChart').getContext('2d');
    const roleData = {!! json_encode($usersByRole) !!};
    const roleLabels = Object.keys(roleData).map(role => {
        const labels = {
            'user': 'Utilisateurs',
            'repairer': 'Réparateurs',
            'artisan': 'Artisans',
            'admin': 'Administrateurs'
        };
        return labels[role] || role;
    });
    const roleValues = Object.values(roleData);

    const usersByRoleChart = new Chart(usersByRoleCtx, {
        type: 'doughnut',
        data: {
            labels: roleLabels,
            datasets: [{
                data: roleValues,
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',   // Green for users
                    'rgba(251, 191, 36, 0.8)',  // Yellow for repairers
                    'rgba(139, 92, 246, 0.8)',  // Purple for artisans
                    'rgba(239, 68, 68, 0.8)'    // Red for admins
                ],
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Activity Comparison Chart
    const activityComparisonCtx = document.getElementById('activityComparisonChart').getContext('2d');
    const activityComparisonChart = new Chart(activityComparisonCtx, {
        type: 'bar',
        data: {
            labels: ['Articles Déchets', 'Demandes de Réparation'],
            datasets: [{
                label: 'Mois Dernier',
                data: [
                    {{ $activityStats['waste_items_last_month'] }},
                    {{ $activityStats['repairs_last_month'] }}
                ],
                backgroundColor: 'rgba(156, 163, 175, 0.7)',
                borderColor: 'rgb(156, 163, 175)',
                borderWidth: 2,
                borderRadius: 8
            }, {
                label: 'Ce Mois-ci',
                data: [
                    {{ $activityStats['waste_items_this_month'] }},
                    {{ $activityStats['repairs_this_month'] }}
                ],
                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                borderColor: 'rgb(34, 197, 94)',
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
