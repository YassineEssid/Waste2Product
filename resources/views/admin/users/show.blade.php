@extends('admin.layout')

@section('title', 'Profil Utilisateur')
@section('page-title', 'Profil Utilisateur')
@section('page-description', $user->name)

@section('page-actions')
    <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition inline-flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Modifier
    </a>
    @if($user->id !== auth()->id())
        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Supprimer
            </button>
        </form>
    @endif
    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
        Retour à la liste
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Profile Header -->
                <div class="bg-gradient-to-br from-blue-500 to-purple-600 h-32"></div>
                
                <div class="px-6 pb-6">
                    <!-- Avatar -->
                    <div class="-mt-16 mb-4">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" 
                                 class="w-32 h-32 rounded-full border-4 border-white object-cover mx-auto">
                        @else
                            <div class="w-32 h-32 rounded-full border-4 border-white bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center mx-auto">
                                <span class="text-white font-bold text-5xl">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- User Info -->
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $user->name }}</h2>
                        <p class="text-gray-600 mb-3">{{ $user->email }}</p>
                        
                        @php
                            $roleColors = [
                                'user' => 'bg-green-100 text-green-800',
                                'repairer' => 'bg-yellow-100 text-yellow-800',
                                'artisan' => 'bg-purple-100 text-purple-800',
                                'admin' => 'bg-red-100 text-red-800',
                            ];
                            $roleLabels = [
                                'user' => 'Utilisateur',
                                'repairer' => 'Réparateur',
                                'artisan' => 'Artisan',
                                'admin' => 'Administrateur',
                            ];
                        @endphp
                        <span class="inline-block px-4 py-1 rounded-full text-sm font-semibold {{ $roleColors[$user->role] }}">
                            {{ $roleLabels[$user->role] }}
                        </span>
                    </div>

                    <!-- Change Role -->
                    @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.toggle-role', $user) }}" method="POST" class="mb-6">
                            @csrf
                            <label class="block text-sm font-medium text-gray-700 mb-2">Changer le rôle</label>
                            <div class="flex space-x-2">
                                <select name="role" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>Utilisateur</option>
                                    <option value="repairer" {{ $user->role == 'repairer' ? 'selected' : '' }}>Réparateur</option>
                                    <option value="artisan" {{ $user->role == 'artisan' ? 'selected' : '' }}>Artisan</option>
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    Modifier
                                </button>
                            </div>
                        </form>
                    @endif

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-6 text-center">
                        <div>
                            <p class="text-2xl font-bold text-blue-600">{{ $stats['waste_items'] }}</p>
                            <p class="text-xs text-gray-600">Déchets</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-green-600">{{ $stats['repair_requests'] }}</p>
                            <p class="text-xs text-gray-600">Réparations</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-purple-600">{{ $stats['account_age_days'] }}</p>
                            <p class="text-xs text-gray-600">Jours</p>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="space-y-3">
                        <div class="flex items-center text-sm">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-gray-700">{{ $user->email }}</span>
                        </div>

                        @if($user->phone)
                            <div class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span class="text-gray-700">{{ $user->phone }}</span>
                            </div>
                        @endif

                        @if($user->address)
                            <div class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-gray-700">{{ $user->address }}</span>
                            </div>
                        @endif

                        <div class="flex items-center text-sm">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-gray-700">Inscrit le {{ $user->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Biography -->
            @if($user->bio)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Biographie
                    </h3>
                    <p class="text-gray-700">{{ $user->bio }}</p>
                </div>
            @endif

            <!-- Location -->
            @if($user->location_lat && $user->location_lng)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        Localisation
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Latitude</p>
                            <p class="font-mono text-gray-900">{{ $user->location_lat }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Longitude</p>
                            <p class="font-mono text-gray-900">{{ $user->location_lng }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Activité Récente
                </h3>

                <!-- Waste Items -->
                @if($user->wasteItems->count() > 0)
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Déchets postés ({{ $user->wasteItems->count() }})</h4>
                        <div class="space-y-2">
                            @foreach($user->wasteItems->take(5) as $item)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $item->title }}</p>
                                        <p class="text-sm text-gray-600">{{ $item->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $item->status == 'available' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Repair Requests -->
                @if($user->repairRequests->count() > 0)
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Demandes de réparation ({{ $user->repairRequests->count() }})</h4>
                        <div class="space-y-2">
                            @foreach($user->repairRequests->take(5) as $repair)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $repair->item_name }}</p>
                                        <p class="text-sm text-gray-600">{{ $repair->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($repair->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($user->wasteItems->count() == 0 && $user->repairRequests->count() == 0)
                    <p class="text-gray-500 text-center py-8">Aucune activité récente</p>
                @endif
            </div>

            <!-- Account Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Informations du compte
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">ID Utilisateur</span>
                        <span class="font-mono text-gray-900">{{ $user->id }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Dernière mise à jour</span>
                        <span class="text-gray-900">{{ $user->updated_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Email vérifié</span>
                        @if($user->email_verified_at)
                            <span class="text-green-600 flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Vérifié
                            </span>
                        @else
                            <span class="text-red-600">Non vérifié</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
