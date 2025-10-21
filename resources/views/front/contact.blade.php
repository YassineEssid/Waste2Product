@extends('front.layout')

@section('title', 'Contact - Waste2Product')

@section('content')
<!-- Hero -->
<section class="page-hero">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold text-white mb-4">Contactez-nous</h1>
                <p class="lead text-white-50">
                    Une question ? Une suggestion ? Nous sommes là pour vous aider
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="contact-form-wrapper">
                    <h3 class="mb-4">Envoyez-nous un message</h3>
                    
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom complet</label>
                                <input type="text" class="form-control" placeholder="Jean Dupont" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="jean@example.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Sujet</label>
                                <select class="form-select">
                                    <option>Question générale</option>
                                    <option>Support technique</option>
                                    <option>Partenariat</option>
                                    <option>Autre</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message</label>
                                <textarea class="form-control" rows="6" placeholder="Votre message..." required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer le message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-5">
                <div class="contact-info-wrapper">
                    <h3 class="mb-4">Informations de contact</h3>
                    
                    <div class="contact-info-item">
                        <div class="info-icon bg-success">
                            <i class="fas fa-envelope text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Email</h5>
                            <p class="text-muted mb-0">contact@waste2product.com</p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="info-icon bg-primary">
                            <i class="fas fa-phone text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Téléphone</h5>
                            <p class="text-muted mb-0">+216 XX XXX XXX</p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="info-icon bg-warning">
                            <i class="fas fa-map-marker-alt text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Adresse</h5>
                            <p class="text-muted mb-0">Tunis, Tunisie</p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="info-icon bg-info">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Horaires</h5>
                            <p class="text-muted mb-0">Lun-Ven: 9h-18h</p>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="mt-5">
                        <h5 class="mb-3">Suivez-nous</h5>
                        <div class="d-flex gap-3">
                            <a href="#" class="social-btn">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-btn">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-btn">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-btn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="row mt-5 pt-5">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">Questions fréquentes</h2>
                <p class="text-muted">Trouvez rapidement des réponses</p>
            </div>

            <div class="col-lg-10 mx-auto">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Comment puis-je m'inscrire ?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                L'inscription est gratuite et simple. Cliquez sur le bouton "Inscription" en haut de la page, 
                                remplissez le formulaire et vous êtes prêt à commencer !
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Comment fonctionne la réparation ?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Publiez votre objet à réparer avec photos et description. Les réparateurs de votre région 
                                pourront voir votre demande et vous proposer leurs services avec un devis.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Puis-je vendre mes créations ?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Oui ! Si vous êtes artisan, vous pouvez créer des transformations et les vendre 
                                sur notre marketplace. C'est gratuit et simple.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Y a-t-il des frais ?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                L'inscription et l'utilisation de base de la plateforme sont entièrement gratuites. 
                                Seules les transactions sur la marketplace peuvent inclure une petite commission.
                            </div>
                        </div>
                    </div>
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

.contact-form-wrapper {
    background: white;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 5px 30px rgba(0, 0, 0, 0.08);
}

.contact-info-wrapper {
    background: white;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 5px 30px rgba(0, 0, 0, 0.08);
    height: 100%;
}

.contact-info-item {
    display: flex;
    gap: 20px;
    align-items: start;
    margin-bottom: 30px;
}

.info-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.social-btn {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-btn:hover {
    background: #10b981;
    color: white;
    transform: translateY(-3px);
}

.accordion-item {
    border: none;
    margin-bottom: 15px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.accordion-button {
    background: white;
    font-weight: 600;
    color: #1f2937;
    border: none;
}

.accordion-button:not(.collapsed) {
    background: #10b981;
    color: white;
}

.accordion-button:focus {
    box-shadow: none;
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