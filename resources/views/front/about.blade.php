@extends('front.layout')

@section('title', 'About - Waste2Product')

@section('content')
<!-- Hero Section -->
<section class="page-hero">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold text-white mb-4">Our Mission</h1>
                <p class="lead text-white-50">
                    Create a world where every waste becomes an opportunity
                </p>
            </div>
        </div>
    </div>
</section>

<!-- About Content -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="{{ asset('images/community.jpg') }}" alt="Community" class="img-fluid rounded-4 shadow">
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Who are we?</h2>
                <p class="text-muted mb-3">
                    <strong>Waste2Product</strong> is an innovative platform born from the conviction that our waste
                    is not an end in itself, but the beginning of a new adventure.
                </p>
                <p class="text-muted mb-3">
                    We believe in the circular economy, where every object can have multiple lives.
                    Our platform connects citizens, repairers and artisans to create
                    a community committed to reducing waste and valorizing resources.
                </p>
                <p class="text-muted">
                    Together, we transform mindsets and practices to build
                    a more sustainable and responsible future.
                </p>
            </div>
        </div>

        <!-- Values -->
        <div class="row mt-5 pt-5">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">Our Values</h2>
                <p class="text-muted">What guides us every day</p>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="value-card text-center">
                    <div class="value-icon bg-success">
                        <i class="fas fa-leaf fa-2x text-white"></i>
                    </div>
                    <h5 class="mt-3 mb-2">Sustainability</h5>
                    <p class="text-muted small">
                        Preserve our planet for future generations
                    </p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="value-card text-center">
                    <div class="value-icon bg-primary">
                        <i class="fas fa-users fa-2x text-white"></i>
                    </div>
                    <h5 class="mt-3 mb-2">Community</h5>
                    <p class="text-muted small">
                        Create connections and share knowledge
                    </p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="value-card text-center">
                    <div class="value-icon bg-warning">
                        <i class="fas fa-lightbulb fa-2x text-white"></i>
                    </div>
                    <h5 class="mt-3 mb-2">Innovation</h5>
                    <p class="text-muted small">
                        Encourage creativity and new solutions
                    </p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="value-card text-center">
                    <div class="value-icon bg-danger">
                        <i class="fas fa-heart fa-2x text-white"></i>
                    </div>
                    <h5 class="mt-3 mb-2">Commitment</h5>
                    <p class="text-muted small">
                        Act concretely for a positive impact
                    </p>
                </div>
            </div>
        </div>

        <!-- Team -->
        <div class="row mt-5 pt-5 bg-light rounded-4 p-5">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">Our Team</h2>
                <p class="text-muted">The passionate people behind Waste2Product</p>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="team-card text-center">
                    <div class="team-avatar mx-auto mb-3">
                        <i class="fas fa-user fa-3x text-success"></i>
                    </div>
                    <h5 class="mb-1">Yassine Essid</h5>
                    <p class="text-muted small mb-2">Founder & CEO</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="team-card text-center">
                    <div class="team-avatar mx-auto mb-3">
                        <i class="fas fa-user fa-3x text-primary"></i>
                    </div>
                    <h5 class="mb-1">Ghaith</h5>
                    <p class="text-muted small mb-2">Lead Developer</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-github"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="team-card text-center">
                    <div class="team-avatar mx-auto mb-3">
                        <i class="fas fa-user fa-3x text-warning"></i>
                    </div>
                    <h5 class="mb-1">The Team</h5>
                    <p class="text-muted small mb-2">Community Manager</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-facebook"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="text-white fw-bold mb-3">Join the movement</h2>
                <p class="text-white-50 mb-0">
                    Be part of the solution, not the pollution
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5">
                    Sign up now
                </a>
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

.value-card {
    padding: 30px 20px;
    border-radius: 15px;
    background: white;
    border: 2px solid #f3f4f6;
    transition: all 0.3s ease;
}

.value-card:hover {
    border-color: #10b981;
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(16, 185, 129, 0.1);
}

.value-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.team-card {
    padding: 30px;
    background: white;
    border-radius: 15px;
}

.team-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
}

.team-card .social-links a {
    display: inline-block;
    width: 35px;
    height: 35px;
    background: #f3f4f6;
    border-radius: 50%;
    text-align: center;
    line-height: 35px;
    margin: 0 5px;
    color: #6b7280;
    transition: all 0.3s ease;
}

.team-card .social-links a:hover {
    background: #10b981;
    color: white;
}

.cta-section {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    padding: 80px 0;
    margin-top: 80px;
}
</style>
@endsection
