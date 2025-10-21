@extends('front.layout')

@section('title', 'Marketplace - Waste2Product')

@section('content')
<!-- Hero -->
<section class="page-hero">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold text-white mb-4">Marketplace</h1>
                <p class="lead text-white-50">
                    Découvrez des créations uniques issues de l'upcycling et du recyclage
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Marketplace Grid -->
<section class="py-5">
    <div class="container">
        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-lg-10 mx-auto">
                <div class="filter-card">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Rechercher un produit...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select">
                                <option selected>Toutes catégories</option>
                                <option>Décoration</option>
                                <option>Mobilier</option>
                                <option>Accessoires</option>
                                <option>Art</option>
                                <option>Autres</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select">
                                <option selected>Trier par</option>
                                <option>Prix croissant</option>
                                <option>Prix décroissant</option>
                                <option>Plus récent</option>
                                <option>Populaire</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        @if(isset($items) && $items->count() > 0)
            <div class="row g-4">
                @foreach($items as $item)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        @php
                            $images = $item->images ?? [];
                            $firstImage = is_array($images) && count($images) > 0 ? $images[0] : null;
                        @endphp
                        
                        @if($firstImage)
                            <img src="{{ Storage::url($firstImage->path ?? $firstImage) }}" alt="{{ $item->title }}" class="product-img">
                        @else
                            <div class="product-img-placeholder">
                                <i class="fas fa-shopping-bag fa-3x text-white"></i>
                            </div>
                        @endif
                        
                        <div class="product-badge">
                            <span class="badge bg-success">Disponible</span>
                        </div>
                        
                        <div class="product-content">
                            <h5 class="product-title">{{ $item->title }}</h5>
                            
                            <p class="product-desc">
                                {{ Str::limit($item->description, 60) }}
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="product-price">
                                    <span class="price">{{ number_format($item->price, 2) }} DT</span>
                                </div>
                                <div class="product-seller">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>{{ $item->seller->name ?? 'Vendeur' }}
                                    </small>
                                </div>
                            </div>
                            
                            @auth
                                <a href="{{ route('marketplace.show', $item) }}" class="btn btn-success w-100">
                                    <i class="fas fa-eye me-2"></i>Voir le produit
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-success w-100" title="Connectez-vous pour acheter">
                                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter pour acheter
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($items->hasPages())
                <div class="mt-5 d-flex justify-content-center">
                    {{ $items->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-shopping-basket fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">Aucun produit disponible pour le moment</h4>
                <p class="text-muted">Nos artisans travaillent sur de nouvelles créations !</p>
            </div>
        @endif
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="fw-bold mb-3">Pourquoi acheter sur notre Marketplace ?</h2>
                <p class="text-muted">Des produits uniques, éco-responsables et faits avec passion</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon bg-success">
                        <i class="fas fa-leaf text-white"></i>
                    </div>
                    <h5 class="mt-3 mb-2">100% Recyclé</h5>
                    <p class="text-muted small">Tous nos produits sont créés à partir de matériaux recyclés</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon bg-primary">
                        <i class="fas fa-star text-white"></i>
                    </div>
                    <h5 class="mt-3 mb-2">Pièces Uniques</h5>
                    <p class="text-muted small">Chaque création est unique et faite main</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon bg-warning">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <h5 class="mt-3 mb-2">Artisans Locaux</h5>
                    <p class="text-muted small">Soutenez les artisans de votre région</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon bg-info">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <h5 class="mt-3 mb-2">Paiement Sécurisé</h5>
                    <p class="text-muted small">Vos transactions sont 100% sécurisées</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="text-white fw-bold mb-4">Vous êtes artisan ?</h2>
                <p class="text-white-50 mb-4">
                    Vendez vos créations sur notre marketplace et touchez une large audience
                </p>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-store me-2"></i>Vendre mes créations
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Devenir vendeur
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
.page-hero {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    padding: 100px 0;
}

.min-vh-50 {
    min-height: 50vh;
}

.filter-card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
}

.product-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.product-img {
    width: 100%;
    height: 220px;
    object-fit: cover;
}

.product-img-placeholder {
    width: 100%;
    height: 220px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 10;
}

.product-content {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 10px;
    min-height: 50px;
}

.product-desc {
    color: #6b7280;
    font-size: 0.85rem;
    margin-bottom: 15px;
    flex: 1;
}

.product-price {
    font-size: 1.3rem;
    font-weight: 700;
    color: #10b981;
}

.product-seller {
    font-size: 0.85rem;
}

.features-section {
    margin-top: 60px;
}

.feature-box {
    text-align: center;
    padding: 30px 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.feature-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.feature-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.feature-icon i {
    font-size: 1.8rem;
}

.cta-section {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    padding: 80px 0;
    margin-top: 60px;
}

.form-control, .form-select {
    border-radius: 10px;
    padding: 12px 15px;
    border: 2px solid #e5e7eb;
}

.form-control:focus, .form-select:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}
</style>
@endsection
