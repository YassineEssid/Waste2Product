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
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-recycle text-success fs-1"></i>
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
                                    <i class="fas fa-map-marker-alt text-muted me-2"></i>Location
                                </label>
                                <textarea id="address" 
                                          class="form-control @error('address') is-invalid @enderror" 
                                          name="address" 
                                          rows="2"
                                          placeholder="Enter your city and country (e.g., New York, USA)">{{ old('address') }}</textarea>
                                @error('address')
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
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Confirm Password Field -->
                            <div class="mb-4">
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