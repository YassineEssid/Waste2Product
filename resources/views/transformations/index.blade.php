@extends('layouts.app')

@section('title', 'Transformation Management')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2"><i class="fas fa-recycle me-2"></i>Transformations</h2>
                            <p class="mb-0 opacity-75">Transform waste into valuable products</p>
                        </div>
                        @if(auth()->user()->role === 'artisan')
                        <a href="{{ route('transformations.create') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-plus me-2"></i>New Transformation
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-cubes fa-2x text-primary mb-2"></i>
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    <small class="text-muted">Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-clipboard-list fa-2x text-info mb-2"></i>
                    <h3 class="mb-0">{{ $stats['planned'] }}</h3>
                    <small class="text-muted">Planned</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-spinner fa-2x text-warning mb-2"></i>
                    <h3 class="mb-0">{{ $stats['in_progress'] }}</h3>
                    <small class="text-muted">In Progress</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h3 class="mb-0">{{ $stats['completed'] }}</h3>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-store fa-2x text-primary mb-2"></i>
                    <h3 class="mb-0">{{ $stats['published'] }}</h3>
                    <small class="text-muted">Published</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100 bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-leaf fa-2x mb-2"></i>
                    <h3 class="mb-0">{{ number_format(\App\Models\Transformation::sum('impact->co2_saved'), 1) }}</h3>
                    <small>kg CO₂ saved</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('transformations.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All statuses</option>
                                <option value="planned" {{ request('status') === 'planned' ? 'selected' : '' }}>Planned</option>
                                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="sort" class="form-select">
                                <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Created Date</option>
                                <option value="price" {{ request('sort') === 'price' ? 'selected' : '' }}>Price</option>
                                <option value="views_count" {{ request('sort') === 'views_count' ? 'selected' : '' }}>Views</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <select name="order" class="form-select">
                                <option value="desc" {{ request('order') === 'desc' ? 'selected' : '' }}>↓</option>
                                <option value="asc" {{ request('order') === 'asc' ? 'selected' : '' }}>↑</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Transformations Grid -->
    <div class="row g-4">
        @forelse($transformations as $transformation)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <!-- Images -->
                <div class="position-relative">
                    @if($transformation->after_images && count($transformation->after_images) > 0)
                        <img src="{{ asset('storage/' . $transformation->after_images[0]) }}" class="card-img-top" alt="{{ $transformation->product_title }}" style="height: 250px; object-fit: cover;">
                    @elseif($transformation->before_images && count($transformation->before_images) > 0)
                        <img src="{{ asset('storage/' . $transformation->before_images[0]) }}" class="card-img-top" alt="{{ $transformation->product_title }}" style="height: 250px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    @endif

                    <!-- Status Badge -->
                    <span class="position-absolute top-0 end-0 m-2 badge bg-{{
                        $transformation->status === 'published' ? 'success' :
                        ($transformation->status === 'completed' ? 'primary' :
                        ($transformation->status === 'in_progress' ? 'warning' : 'info'))
                    }}">
                        @switch($transformation->status)
                            @case('planned') Planned @break
                            @case('in_progress') In Progress @break
                            @case('completed') Completed @break
                            @case('published') Published @break
                        @endswitch
                    </span>                    @if($transformation->is_featured)
                    <span class="position-absolute top-0 start-0 m-2 badge bg-warning">
                        <i class="fas fa-star"></i> Vedette
                    </span>
                    @endif
                </div>

                <div class="card-body">
                    <h5 class="card-title mb-2">{{ $transformation->product_title }}</h5>
                    <p class="text-muted small mb-3">
                        <i class="fas fa-user me-1"></i>{{ $transformation->user->name ?? 'Artisan' }}
                    </p>

                    <p class="card-text text-muted small mb-3">
                        {{ Str::limit($transformation->description, 100) }}
                    </p>

                    <!-- Impact Stats -->
                    @if($transformation->impact && (isset($transformation->impact['co2_saved']) || isset($transformation->impact['waste_reduced'])))
                    <div class="row g-2 mb-3">
                        @if(isset($transformation->impact['co2_saved']) && $transformation->impact['co2_saved'] > 0)
                        <div class="col-6">
                            <div class="bg-success bg-opacity-10 p-2 rounded text-center">
                                <small class="d-block text-success fw-bold">{{ $transformation->impact['co2_saved'] }} kg</small>
                                <small class="text-muted">CO₂ économisé</small>
                            </div>
                        </div>
                        @endif

                        @if(isset($transformation->impact['waste_reduced']) && $transformation->impact['waste_reduced'] > 0)
                        <div class="col-6">
                            <div class="bg-info bg-opacity-10 p-2 rounded text-center">
                                <small class="d-block text-info fw-bold">{{ $transformation->impact['waste_reduced'] }} kg</small>
                                <small class="text-muted">Déchets réduits</small>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Price & Views -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        @if($transformation->price)
                        <h4 class="text-primary mb-0">{{ number_format($transformation->price, 2) }} DT</h4>
                        @else
                        <span class="text-muted">Prix non défini</span>
                        @endif

                        <small class="text-muted">
                            <i class="fas fa-eye me-1"></i>{{ $transformation->views_count }} vues
                        </small>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('transformations.show', $transformation) }}" class="btn btn-primary flex-fill">
                            <i class="fas fa-eye me-1"></i>Voir
                        </a>

                        @if(auth()->user()->role === 'artisan' && $transformation->artisan_id === auth()->id())
                        <a href="{{ route('transformations.edit', $transformation) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endif
                    </div>
                </div>

                <div class="card-footer bg-white border-top-0">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>{{ $transformation->created_at->diffForHumans() }}
                    </small>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No transformations found</h4>
                    <p class="text-muted">Start by creating your first transformation!</p>
                    @if(auth()->user()->role === 'artisan')
                    <a href="{{ route('transformations.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>Create a transformation
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($transformations->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $transformations->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection
