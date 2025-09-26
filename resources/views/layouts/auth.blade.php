<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Waste2Product - Authentication')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            min-height: 100vh;
        }
        
        .auth-card {
            border-radius: 20px;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .btn-enhanced {
            border-radius: 50px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        }
        
        .form-control {
            border-radius: 10px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
            border-color: #28a745;
        }
    </style>
    @stack('styles')
</head>
<body>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x" style="z-index: 1050; margin-top: 20px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x" style="z-index: 1050; margin-top: 20px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>