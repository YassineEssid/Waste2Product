@extends('layouts.auth')

@section('title', 'Login - Waste2Product')

@section('content')
<div class="min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 auth-card">
                    <div class="card-body p-5">
                        <!-- Logo and Header -->
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <img src="{{ asset('images/waste2product_logo.png') }}" alt="Waste2Product" class="mx-auto" style="height: 100px; width: auto;">
                            </div>
                            <h2 class="fw-bold text-dark mb-2">Welcome Back!</h2>
                            <p class="text-muted">Sign in to continue your sustainability journey</p>
                        </div>

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
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
                                       autofocus
                                       placeholder="Enter your email">
                                @error('email')
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
                                           autocomplete="current-password"
                                           placeholder="Enter your password">
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

                            <!-- Remember Me -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" 
                                           {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label text-muted" for="remember">
                                        Remember me
                                    </label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a class="text-decoration-none text-success fw-semibold" href="{{ route('password.request') }}">
                                        Forgot password?
                                    </a>
                                @endif
                            </div>

                            <!-- Login Button -->
                            <button type="submit" class="btn btn-success btn-lg w-100 mb-3 btn-enhanced">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                        </form>

                        <!-- Register Link -->
                        <div class="text-center">
                            <p class="text-muted mb-0">
                                Don't have an account? 
                                <a href="{{ route('register') }}" class="text-success fw-semibold text-decoration-none">
                                    Join our community
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Info -->
                <div class="text-center mt-4">
                    <div class="d-flex justify-content-center gap-4 text-white-50 small">
                        <span><i class="fas fa-users me-1"></i>10K+ Members</span>
                        <span><i class="fas fa-recycle me-1"></i>50K+ Items Saved</span>
                        <span><i class="fas fa-leaf me-1"></i>245T CO₂ Reduced</span>
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
</script>
@endsection