@extends('front.layout')

@section('title', 'Contact - Waste2Product')

@section('content')
<!-- Hero -->
<section class="page-hero">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold text-white mb-4">Contact Us</h1>
                <p class="lead text-white-50">
                    A question? A suggestion? We are here to help you
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
                    <h3 class="mb-4">Send us a message</h3>

                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full name</label>
                                <input type="text" class="form-control" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="john@example.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Subject</label>
                                <select class="form-select">
                                    <option>General question</option>
                                    <option>Technical support</option>
                                    <option>Partnership</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message</label>
                                <textarea class="form-control" rows="6" placeholder="Your message..." required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-paper-plane me-2"></i>Send message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-5">
                <div class="contact-info-wrapper">
                    <h3 class="mb-4">Contact information</h3>

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
                            <h5 class="mb-1">Phone</h5>
                            <p class="text-muted mb-0">+216 XX XXX XXX</p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="info-icon bg-warning">
                            <i class="fas fa-map-marker-alt text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Address</h5>
                            <p class="text-muted mb-0">Tunis, Tunisia</p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="info-icon bg-info">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Hours</h5>
                            <p class="text-muted mb-0">Mon-Fri: 9am-6pm</p>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="mt-5">
                        <h5 class="mb-3">Follow us</h5>
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
                <h2 class="fw-bold">Frequently Asked Questions</h2>
                <p class="text-muted">Find answers quickly</p>
            </div>

            <div class="col-lg-10 mx-auto">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                How can I register?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Registration is free and simple. Click the "Sign up" button at the top of the page,
                                fill out the form and you're ready to get started!
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                How does repair work?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Post your item to repair with photos and description. Repairers in your area
                                will be able to see your request and offer their services with a quote.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Can I sell my creations?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes! If you're an artisan, you can create transformations and sell them
                                on our marketplace. It's free and simple.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Are there any fees?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Registration and basic use of the platform are completely free.
                                Only transactions on the marketplace may include a small commission.
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
