@extends('layouts.auth')

@section('title', 'Register - Waste2Product')

@section('content')
<div class="min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0 auth-card">
                    <div class="card-body p-5">
                        <!-- Logo and Header -->
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <img src="{{ asset('images/waste2product_logo.png') }}" alt="Waste2Product" class="mx-auto" style="height: 100px; width: auto;">
                            </div>
                            <h2 class="fw-bold text-dark mb-2">Join Our Community!</h2>
                            <p class="text-muted">Start your sustainability journey today</p>
                        </div>

                        <!-- Registration Form -->
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            
                            <!-- Name Field -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">
                                    <i class="fas fa-user text-muted me-2"></i>Full Name
                                </label>
                                <input id="name" type="text" 
                                       class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required 
                                       autocomplete="name" 
                                       autofocus
                                       placeholder="Enter your full name">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Email Field -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope text-muted me-2"></i>Email Address
                                </label>
                                <input id="email" type="email" 
                                       class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="email"
                                       placeholder="Enter your email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Role Selection -->
                            <div class="mb-3">
                                <label for="role" class="form-label fw-semibold">
                                    <i class="fas fa-user-tag text-muted me-2"></i>Choose Your Role
                                </label>
                                <select id="role" class="form-select form-select-lg @error('role') is-invalid @enderror" 
                                        name="role" required>
                                    <option value="">Select your role...</option>
                                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>
                                        🌱 Community Member - Share items, get repairs, join events
                                    </option>
                                    <option value="repairer" {{ old('role') == 'repairer' ? 'selected' : '' }}>
                                        🔧 Expert Repairer - Fix items, earn money, help planet
                                    </option>
                                    <option value="artisan" {{ old('role') == 'artisan' ? 'selected' : '' }}>
                                        🎨 Creative Artisan - Upcycle items into art & products
                                    </option>
                                </select>
                                @error('role')
                                    <span class="invalid-feedback" role="alert">
                                        <strong><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Address Field -->
                            <div class="mb-3">
                                <label for="address" class="form-label fw-semibold">
                                    <i class="fas fa-map-marker-alt text-muted me-2"></i>Location <span class="text-muted small">(Optional)</span>
                                </label>
                                <textarea id="address" 
                                          class="form-control @error('address') is-invalid @enderror" 
                                          name="address" 
                                          rows="2"
                                          placeholder="Enter your city and country (e.g., Paris, France)">{{ old('address') }}</textarea>
                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Phone Field -->
                            <div class="mb-3">
                                <label for="phone" class="form-label fw-semibold">
                                    <i class="fas fa-phone text-muted me-2"></i>Phone Number <span class="text-muted small">(Optional)</span>
                                </label>
                                <input id="phone" type="tel" 
                                       class="form-control form-control-lg @error('phone') is-invalid @enderror" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       placeholder="+33 6 12 34 56 78">
                                <small class="text-muted">Format international recommandé</small>
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Password Field -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-lock text-muted me-2"></i>Password
                                </label>
                                <div class="position-relative">
                                    <input id="password" type="password" 
                                           class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                           name="password" 
                                           required 
                                           autocomplete="new-password"
                                           placeholder="Create a secure password">
                                    <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y" 
                                            onclick="togglePassword('password')" style="z-index: 10;">
                                        <i class="fas fa-eye text-muted" id="password-toggle-icon"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Min 8 caractères, majuscules, minuscules, chiffres et symboles</small>
                                <div id="password-strength" class="mt-2"></div>
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Confirm Password Field -->
                            <div class="mb-3">
                                <label for="password-confirm" class="form-label fw-semibold">
                                    <i class="fas fa-lock text-muted me-2"></i>Confirm Password
                                </label>
                                <div class="position-relative">
                                    <input id="password-confirm" type="password" 
                                           class="form-control form-control-lg" 
                                           name="password_confirmation" 
                                           required 
                                           autocomplete="new-password"
                                           placeholder="Confirm your password">
                                    <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y" 
                                            onclick="togglePassword('password-confirm')" style="z-index: 10;">
                                        <i class="fas fa-eye text-muted" id="password-confirm-toggle-icon"></i>
                                    </button>
                                </div>
                                <div id="password-match" class="mt-2"></div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input @error('terms') is-invalid @enderror" 
                                           type="checkbox" 
                                           name="terms" 
                                           id="terms" 
                                           value="1"
                                           {{ old('terms') ? 'checked' : '' }}
                                           required>
                                    <label class="form-check-label" for="terms">
                                        J'accepte les <a href="#" class="text-success">conditions d'utilisation</a> et la 
                                        <a href="#" class="text-success">politique de confidentialité</a>
                                    </label>
                                    @error('terms')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Register Button -->
                            <button type="submit" class="btn btn-success btn-lg w-100 mb-3 btn-enhanced">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </button>
                        </form>

                        <!-- Login Link -->
                        <div class="text-center">
                            <p class="text-muted mb-0">
                                Already have an account? 
                                <a href="{{ route('login') }}" class="text-success fw-semibold text-decoration-none">
                                    Sign in here
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Info -->
                <div class="text-center mt-4">
                    <div class="d-flex justify-content-center gap-4 text-white-50 small">
                        <span><i class="fas fa-shield-alt me-1"></i>100% Secure</span>
                        <span><i class="fas fa-users me-1"></i>10K+ Members</span>
                        <span><i class="fas fa-leaf me-1"></i>Planet Friendly</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-toggle-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'fas fa-eye-slash text-muted';
    } else {
        field.type = 'password';
        icon.className = 'fas fa-eye text-muted';
    }
}

// Password strength checker
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthDiv = document.getElementById('password-strength');
    
    let strength = 0;
    let feedback = [];
    
    // Check length
    if (password.length >= 8) strength++;
    else feedback.push('au moins 8 caractères');
    
    // Check for lowercase
    if (/[a-z]/.test(password)) strength++;
    else feedback.push('minuscules');
    
    // Check for uppercase
    if (/[A-Z]/.test(password)) strength++;
    else feedback.push('majuscules');
    
    // Check for numbers
    if (/\d/.test(password)) strength++;
    else feedback.push('chiffres');
    
    // Check for special characters
    if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;
    else feedback.push('symboles');
    
    // Display strength
    if (password.length === 0) {
        strengthDiv.innerHTML = '';
    } else if (strength < 3) {
        strengthDiv.innerHTML = '<small class="text-danger"><i class="fas fa-times-circle"></i> Faible - Manque: ' + feedback.join(', ') + '</small>';
    } else if (strength < 5) {
        strengthDiv.innerHTML = '<small class="text-warning"><i class="fas fa-exclamation-circle"></i> Moyen - Manque: ' + feedback.join(', ') + '</small>';
    } else {
        strengthDiv.innerHTML = '<small class="text-success"><i class="fas fa-check-circle"></i> Fort - Mot de passe sécurisé!</small>';
    }
});

// Password match checker
document.getElementById('password-confirm').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    const matchDiv = document.getElementById('password-match');
    
    if (confirmPassword.length === 0) {
        matchDiv.innerHTML = '';
    } else if (password === confirmPassword) {
        matchDiv.innerHTML = '<small class="text-success"><i class="fas fa-check-circle"></i> Les mots de passe correspondent</small>';
        this.setCustomValidity('');
    } else {
        matchDiv.innerHTML = '<small class="text-danger"><i class="fas fa-times-circle"></i> Les mots de passe ne correspondent pas</small>';
        this.setCustomValidity('Les mots de passe ne correspondent pas');
    }
});

// Name validation
document.getElementById('name').addEventListener('input', function() {
    const nameRegex = /^[a-zA-ZÀ-ÿ\s\'-]+$/;
    if (this.value && !nameRegex.test(this.value)) {
        this.setCustomValidity('Le nom ne peut contenir que des lettres, espaces, apostrophes et traits d\'union');
    } else if (this.value && this.value.length < 3) {
        this.setCustomValidity('Le nom doit contenir au moins 3 caractères');
    } else {
        this.setCustomValidity('');
    }
});

// Email validation
document.getElementById('email').addEventListener('blur', function() {
    const email = this.value;
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    
    if (email && !emailRegex.test(email)) {
        this.setCustomValidity('Veuillez entrer une adresse email valide');
    } else {
        this.setCustomValidity('');
    }
});

// Phone validation
document.getElementById('phone').addEventListener('input', function() {
    const phoneRegex = /^[\+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}$/;
    if (this.value && !phoneRegex.test(this.value)) {
        this.setCustomValidity('Format de téléphone invalide');
    } else {
        this.setCustomValidity('');
    }
});

// Handle role selection for query parameter
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const roleParam = urlParams.get('role');
    if (roleParam) {
        const roleSelect = document.getElementById('role');
        roleSelect.value = roleParam;
    }
});
</script>
@endsection