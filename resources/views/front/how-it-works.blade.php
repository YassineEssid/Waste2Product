@extends('front.layout')

@section('title', 'Comment ça marche - Waste2Product')

@section('content')
<!-- Hero -->
<section class="page-hero">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold text-white mb-4">Comment ça marche ?</h1>
                <p class="lead text-white-50">
                    Un guide simple pour commencer votre aventure vers le zéro déchet
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Steps -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-10 mx-auto">
                <div class="timeline">
                    <!-- Step 1 -->
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success">1</div>
                        <div class="timeline-content">
                            <h3 class="mb-3"><i class="fas fa-user-plus text-success me-2"></i>Créez votre compte</h3>
                            <p class="text-muted mb-4">
                                Inscrivez-vous gratuitement en quelques secondes. Choisissez votre rôle : 
                                utilisateur, réparateur ou artisan selon vos compétences et vos besoins.
                            </p>
                            <a href="{{ route('register') }}" class="btn btn-success">S'inscrire</a>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary">2</div>
                        <div class="timeline-content">
                            <h3 class="mb-3"><i class="fas fa-box-open text-primary me-2"></i>Ajoutez vos articles</h3>
                            <p class="text-muted mb-3">
                                Publiez les objets dont vous ne vous servez plus avec :
                            </p>
                            <ul class="text-muted">
                                <li>Photos de l'objet</li>
                                <li>Description détaillée</li>
                                <li>Catégorie et état</li>
                                <li>Localisation (optionnel)</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning">3</div>
                        <div class="timeline-content">
                            <h3 class="mb-3"><i class="fas fa-tools text-warning me-2"></i>Trouvez une solution</h3>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="solution-card">
                                        <i class="fas fa-wrench fa-2x text-info mb-3"></i>
                                        <h5>Réparation</h5>
                                        <p class="small text-muted">Demandez à un réparateur de remettre votre objet en état</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="solution-card">
                                        <i class="fas fa-palette fa-2x text-purple mb-3"></i>
                                        <h5>Transformation</h5>
                                        <p class="small text-muted">Laissez un artisan créer quelque chose d'unique</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="solution-card">
                                        <i class="fas fa-gift fa-2x text-danger mb-3"></i>
                                        <h5>Don</h5>
                                        <p class="small text-muted">Offrez l'objet à quelqu'un qui en a besoin</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info">4</div>
                        <div class="timeline-content">
                            <h3 class="mb-3"><i class="fas fa-handshake text-info me-2"></i>Connectez-vous</h3>
                            <p class="text-muted mb-3">
                                Échangez avec les membres de la communauté. Les réparateurs et artisans 
                                peuvent accepter vos demandes et vous proposer leurs services.
                            </p>
                        </div>
                    </div>

                    <!-- Step 5 -->
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success">5</div>
                        <div class="timeline-content">
                            <h3 class="mb-3"><i class="fas fa-shopping-cart text-success me-2"></i>Marketplace</h3>
                            <p class="text-muted mb-3">
                                Les artisans peuvent vendre leurs créations sur notre marketplace. 
                                Vous aussi, achetez des produits uniques issus du recyclage !
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- For different roles -->
        <div class="row mt-5 pt-5">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">Selon votre profil</h2>
                <p class="text-muted">Différentes façons d'utiliser la plateforme</p>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="role-card">
                    <div class="role-icon bg-success">
                        <i class="fas fa-user fa-2x text-white"></i>
                    </div>
                    <h4 class="mt-4 mb-3">Utilisateur</h4>
                    <ul class="text-muted text-start">
                        <li>Publiez vos objets inutilisés</li>
                        <li>Demandez des réparations</li>
                        <li>Participez aux événements</li>
                        <li>Achetez sur la marketplace</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="role-card">
                    <div class="role-icon bg-primary">
                        <i class="fas fa-tools fa-2x text-white"></i>
                    </div>
                    <h4 class="mt-4 mb-3">Réparateur</h4>
                    <ul class="text-muted text-start">
                        <li>Acceptez des missions de réparation</li>
                        <li>Fixez vos tarifs</li>
                        <li>Développez votre clientèle</li>
                        <li>Gagnez des revenus</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="role-card">
                    <div class="role-icon bg-warning">
                        <i class="fas fa-palette fa-2x text-white"></i>
                    </div>
                    <h4 class="mt-4 mb-3">Artisan</h4>
                    <ul class="text-muted text-start">
                        <li>Transformez les déchets en art</li>
                        <li>Partagez vos créations</li>
                        <li>Vendez sur la marketplace</li>
                        <li>Inspirez la communauté</li>
                    </ul>
                </div>
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

.timeline {
    position: relative;
    padding-left: 60px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, #10b981, #059669);
}

.timeline-item {
    position: relative;
    margin-bottom: 60px;
}

.timeline-marker {
    position: absolute;
    left: -60px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.timeline-content {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
}

.solution-card {
    padding: 25px;
    background: #f9fafb;
    border-radius: 12px;
    text-align: center;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.solution-card:hover {
    border-color: #10b981;
    background: white;
    transform: translateY(-5px);
}

.role-card {
    background: white;
    padding: 40px 30px;
    border-radius: 20px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.role-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.role-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.role-card ul {
    list-style: none;
    padding-left: 0;
}

.role-card ul li {
    padding: 8px 0;
    border-bottom: 1px solid #f3f4f6;
}

.role-card ul li:last-child {
    border-bottom: none;
}

.role-card ul li::before {
    content: '✓';
    color: #10b981;
    font-weight: bold;
    margin-right: 10px;
}

.text-purple {
    color: #9b59b6;
}
</style>
@endsection