@extends('front.layout')

@section('title', 'How it works - Waste2Product')

@section('content')
<!-- Hero -->
<section class="page-hero">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold text-white mb-4">How does it work?</h1>
                <p class="lead text-white-50">
                    A simple guide to start your zero waste adventure
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
                            <h3 class="mb-3"><i class="fas fa-user-plus text-success me-2"></i>Create your account</h3>
                            <p class="text-muted mb-4">
                                Sign up for free in just a few seconds. Choose your role:
                                user, repairer or artisan according to your skills and needs.
                            </p>
                            <a href="{{ route('register') }}" class="btn btn-success">Sign up</a>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary">2</div>
                        <div class="timeline-content">
                            <h3 class="mb-3"><i class="fas fa-box-open text-primary me-2"></i>Add your items</h3>
                            <p class="text-muted mb-3">
                                Publish the items you no longer use with:
                            </p>
                            <ul class="text-muted">
                                <li>Photos of the item</li>
                                <li>Detailed description</li>
                                <li>Category and condition</li>
                                <li>Location (optional)</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning">3</div>
                        <div class="timeline-content">
                            <h3 class="mb-3"><i class="fas fa-tools text-warning me-2"></i>Find a solution</h3>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="solution-card">
                                        <i class="fas fa-wrench fa-2x text-info mb-3"></i>
                                        <h5>Repair</h5>
                                        <p class="small text-muted">Ask a repairer to restore your item</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="solution-card">
                                        <i class="fas fa-palette fa-2x text-purple mb-3"></i>
                                        <h5>Transform</h5>
                                        <p class="small text-muted">Let an artisan create something unique</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="solution-card">
                                        <i class="fas fa-gift fa-2x text-danger mb-3"></i>
                                        <h5>Donate</h5>
                                        <p class="small text-muted">Give the item to someone who needs it</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info">4</div>
                        <div class="timeline-content">
                            <h3 class="mb-3"><i class="fas fa-handshake text-info me-2"></i>Connect</h3>
                            <p class="text-muted mb-3">
                                Exchange with community members. Repairers and artisans
                                can accept your requests and offer their services.
                            </p>
                        </div>
                    </div>

                    <!-- Step 5 -->
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success">5</div>
                        <div class="timeline-content">
                            <h3 class="mb-3"><i class="fas fa-shopping-cart text-success me-2"></i>Marketplace</h3>
                            <p class="text-muted mb-3">
                                Artisans can sell their creations on our marketplace.
                                You too can buy unique products from recycling!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- For different roles -->
        <div class="row mt-5 pt-5">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">According to your profile</h2>
                <p class="text-muted">Different ways to use the platform</p>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="role-card">
                    <div class="role-icon bg-success">
                        <i class="fas fa-user fa-2x text-white"></i>
                    </div>
                    <h4 class="mt-4 mb-3">User</h4>
                    <ul class="text-muted text-start">
                        <li>Publish your unused items</li>
                        <li>Request repairs</li>
                        <li>Participate in events</li>
                        <li>Buy on the marketplace</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="role-card">
                    <div class="role-icon bg-primary">
                        <i class="fas fa-tools fa-2x text-white"></i>
                    </div>
                    <h4 class="mt-4 mb-3">Repairer</h4>
                    <ul class="text-muted text-start">
                        <li>Accept repair missions</li>
                        <li>Set your rates</li>
                        <li>Grow your clientele</li>
                        <li>Earn income</li>
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
                        <li>Transform waste into art</li>
                        <li>Share your creations</li>
                        <li>Sell on the marketplace</li>
                        <li>Inspire the community</li>
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
