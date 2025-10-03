@extends('front.layout')

@section('title', 'Waste2Product - Transformez vos déchets en ressources')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6 fade-in-up">
                <h1 class="display-3 fw-bold mb-4">
                    Transformez vos <span class="text-gradient">déchets</span> en <span class="text-gradient">ressources</span>
                </h1>
                <p class="lead mb-4">
                    Rejoignez notre communauté et donnez une seconde vie à vos objets. 
                    Réparez, transformez, partagez pour un avenir plus durable.
                </p>
                <div class="d-flex gap-3">
                    <a href="{{ route('register') }}" class="btn btn-success btn-lg px-4">
                        <i class="fas fa-rocket me-2"></i>Commencer gratuitement
                    </a>
                    <a href="#how-it-works" class="btn btn-outline-success btn-lg px-4">
                        <i class="fas fa-play-circle me-2"></i>Comment ça marche
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="row mt-5">
                    <div class="col-4">
                        <div class="stat-item">
                            <h3 class="fw-bold text-success mb-0">{{ number_format($stats['total_items']) }}+</h3>
                            <p class="text-muted small mb-0">Articles</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-item">
                            <h3 class="fw-bold text-success mb-0">{{ number_format($stats['transformations']) }}+</h3>
                            <p class="text-muted small mb-0">Transformations</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-item">
                            <h3 class="fw-bold text-success mb-0">{{ number_format($stats['community_members']) }}+</h3>
                            <p class="text-muted small mb-0">Membres</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 fade-in-up">
                <div class="hero-image-wrapper">
                    <img src="{{ asset('images/hero-bg.jpg') }}" alt="Hero" class="img-fluid rounded-4 shadow-lg">
                    <div class="floating-card card-1">
                        <i class="fas fa-recycle fa-2x text-success"></i>
                        <p class="mb-0 mt-2"><strong>Recyclez</strong></p>
                    </div>
                    <div class="floating-card card-2">
                        <i class="fas fa-tools fa-2x text-primary"></i>
                        <p class="mb-0 mt-2"><strong>Réparez</strong></p>
                    </div>
                    <div class="floating-card card-3">
                        <i class="fas fa-palette fa-2x text-warning"></i>
                        <p class="mb-0 mt-2"><strong>Créez</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5 bg-light" id="how-it-works">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Comment ça marche ?</h2>
            <p class="lead text-muted">Trois étapes simples pour commencer</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="icon-wrapper bg-success">
                        <i class="fas fa-box-open fa-2x"></i>
                    </div>
                    <h4 class="mt-4 mb-3">1. Publiez vos articles</h4>
                    <p class="text-muted">
                        Ajoutez vos objets inutilisés avec photos et description. 
                        Que ce soit pour donner, réparer ou transformer.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="icon-wrapper bg-primary">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h4 class="mt-4 mb-3">2. Connectez-vous</h4>
                    <p class="text-muted">
                        Trouvez des réparateurs, artisans ou autres membres de la communauté 
                        pour donner une nouvelle vie à vos objets.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="icon-wrapper bg-warning">
                        <i class="fas fa-magic fa-2x"></i>
                    </div>
                    <h4 class="mt-4 mb-3">3. Transformez</h4>
                    <p class="text-muted">
                        Réparez, transformez en création unique et vendez sur notre marketplace.
                        Contribuez à un monde plus durable.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Items Section -->
@if($recentItems->count() > 0)
<section class="recent-items-section py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Articles récents</h2>
                <p class="text-muted">Découvrez les derniers objets partagés</p>
            </div>
            @auth
                <a href="{{ route('waste-items.index') }}" class="btn btn-outline-success">
                    Voir tout <i class="fas fa-arrow-right ms-2"></i>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-success" title="Connectez-vous pour accéder à tous les articles">
                    Voir tout <i class="fas fa-arrow-right ms-2"></i>
                </a>
            @endauth
        </div>
        
        <div class="row g-4">
            @foreach($recentItems as $item)
            <div class="col-lg-4 col-md-6">
                <div class="item-card">
                    @if($item->images && count($item->images) > 0)
                        <img src="{{ Storage::url($item->images[0]) }}" alt="{{ $item->title }}" class="item-image">
                    @else
                        <div class="item-image-placeholder">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    @endif
                    
                    <div class="p-3">
                        <span class="badge bg-success mb-2">{{ $item->category }}</span>
                        <h5 class="mb-2">{{ $item->title }}</h5>
                        <p class="text-muted small mb-3">{{ Str::limit($item->description, 80) }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>{{ $item->user->name }}
                            </small>
                            @auth
                                <a href="{{ route('waste-items.show', $item) }}" class="btn btn-sm btn-success">
                                    Voir <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-sm btn-success" title="Connectez-vous pour voir les détails">
                                    Voir <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Transformations Section -->
@if($recentTransformations->count() > 0)
<section class="transformations-section py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Transformations inspirantes</h2>
            <p class="text-muted">Découvrez comment nos artisans transforment les déchets</p>
        </div>
        
        <div class="row g-4">
            @foreach($recentTransformations as $transformation)
            <div class="col-lg-3 col-md-6">
                <div class="transformation-card">
                    @php
                        $afterImages = json_decode($transformation->after_images, true);
                    @endphp
                    
                    @if($afterImages && count($afterImages) > 0)
                        <img src="{{ Storage::url($afterImages[0]) }}" alt="{{ $transformation->title }}">
                    @else
                        <div class="transformation-placeholder">
                            <i class="fas fa-palette fa-3x text-muted"></i>
                        </div>
                    @endif
                    
                    <div class="transformation-overlay">
                        <h5 class="text-white mb-2">{{ $transformation->title }}</h5>
                        <p class="text-white-50 small mb-3">Par {{ $transformation->user->name }}</p>
                        @auth
                            <a href="{{ route('transformations.show', $transformation) }}" class="btn btn-sm btn-light">
                                Voir détails
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-sm btn-light" title="Connectez-vous pour voir les détails">
                                Voir détails
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Events Section -->
@if($upcomingEvents->count() > 0)
<section class="events-section py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Événements à venir</h2>
                <p class="text-muted">Rejoignez notre communauté</p>
            </div>
            @auth
                <a href="{{ route('events.index') }}" class="btn btn-outline-success">
                    Tous les événements <i class="fas fa-arrow-right ms-2"></i>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-success" title="Connectez-vous pour voir tous les événements">
                    Tous les événements <i class="fas fa-arrow-right ms-2"></i>
                </a>
            @endauth
        </div>
        
        <div class="row g-4">
            @foreach($upcomingEvents as $event)
            <div class="col-lg-4">
                <div class="event-card-home">
                    @if($event->image)
                        <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}">
                    @else
                        <div class="event-placeholder">
                            <i class="fas fa-calendar-alt fa-3x text-white"></i>
                        </div>
                    @endif
                    
                    <div class="p-4">
                        <div class="d-flex gap-2 mb-3">
                            <span class="badge bg-success">
                                <i class="fas fa-calendar me-1"></i>{{ $event->starts_at->format('d/m/Y') }}
                            </span>
                            <span class="badge bg-primary">
                                <i class="fas fa-clock me-1"></i>{{ $event->starts_at->format('H:i') }}
                            </span>
                        </div>
                        
                        <h5 class="mb-3">{{ $event->title }}</h5>
                        <p class="text-muted small mb-3">
                            <i class="fas fa-map-marker-alt me-1"></i>{{ $event->location ?? 'En ligne' }}
                        </p>
                        
                        @auth
                            <a href="{{ route('events.show', $event) }}" class="btn btn-success w-100">
                                En savoir plus
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-success w-100" title="Connectez-vous pour participer">
                                En savoir plus
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="text-white fw-bold mb-3">Prêt à faire la différence ?</h2>
                <p class="text-white-50 mb-0">
                    Rejoignez des milliers de personnes qui transforment déjà leurs déchets en opportunités
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5">
                    <i class="fas fa-user-plus me-2"></i>S'inscrire gratuitement
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    position: relative;
    overflow: hidden;
}

.text-gradient {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-image-wrapper {
    position: relative;
}

.floating-card {
    position: absolute;
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    text-align: center;
    animation: float 3s ease-in-out infinite;
}

.card-1 {
    top: 10%;
    left: -50px;
}

.card-2 {
    top: 50%;
    right: -50px;
    animation-delay: 1s;
}

.card-3 {
    bottom: 10%;
    left: 20%;
    animation-delay: 2s;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

/* Feature Cards */
.feature-card {
    background: white;
    padding: 40px 30px;
    border-radius: 20px;
    text-align: center;
    transition: all 0.3s ease;
    height: 100%;
    border: 2px solid transparent;
}

.feature-card:hover {
    transform: translateY(-10px);
    border-color: #10b981;
    box-shadow: 0 20px 40px rgba(16, 185, 129, 0.1);
}

.icon-wrapper {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    color: white;
}

/* Item Cards */
.item-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    height: 100%;
}

.item-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
}

.item-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.item-image-placeholder {
    width: 100%;
    height: 200px;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Transformation Cards */
.transformation-card {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    height: 300px;
}

.transformation-card img,
.transformation-placeholder {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.transformation-placeholder {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.transformation-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%);
    padding: 30px 20px 20px;
    transform: translateY(60px);
    transition: transform 0.3s ease;
}

.transformation-card:hover .transformation-overlay {
    transform: translateY(0);
}

/* Event Cards */
.event-card-home {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    height: 100%;
}

.event-card-home:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
}

.event-card-home img,
.event-placeholder {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.event-placeholder {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* CTA Section */
.cta-section {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    padding: 80px 0;
    margin-top: 80px;
}
</style>
@endsection
