<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Authentication</title>

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        
        <!-- Scripts -->
        @vite(['resources/js/app.js'])
        
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            
            .auth-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            }
            
            .company-logo {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 28px;
                font-weight: bold;
                margin: 0 auto 20px;
                box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
                transition: transform 0.3s ease;
            }
            
            .company-logo:hover {
                transform: scale(1.05);
            }
            
            .auth-header {
                text-align: center;
                margin-bottom: 30px;
            }
            
            .auth-header h1 {
                color: #2d3748;
                font-weight: 600;
                margin-bottom: 8px;
            }
            
            .auth-header p {
                color: #718096;
                margin: 0;
            }
            
            .btn-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                padding: 12px 30px;
                font-weight: 500;
                transition: all 0.3s ease;
            }
            
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
                background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            }
            
            .form-control:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            }
            
            .floating-elements {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                overflow: hidden;
                pointer-events: none;
            }
            
            .floating-elements::before,
            .floating-elements::after {
                content: '';
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
                animation: float 6s ease-in-out infinite;
            }
            
            .floating-elements::before {
                width: 100px;
                height: 100px;
                top: 20%;
                left: 10%;
                animation-delay: 0s;
            }
            
            .floating-elements::after {
                width: 150px;
                height: 150px;
                top: 60%;
                right: 10%;
                animation-delay: 3s;
            }
            
            @keyframes float {
                0%, 100% { transform: translateY(0px) rotate(0deg); }
                50% { transform: translateY(-20px) rotate(180deg); }
            }
            
            .electronics-pattern {
                position: absolute;
                width: 20px;
                height: 20px;
                opacity: 0.1;
                color: white;
            }
            
            .error-message {
                background: rgba(220, 53, 69, 0.1);
                border: 1px solid rgba(220, 53, 69, 0.2);
                color: #dc3545;
                border-radius: 8px;
                padding: 8px 12px;
                font-size: 14px;
                margin-top: 5px;
            }
            
            .status-message {
                background: rgba(25, 135, 84, 0.1);
                border: 1px solid rgba(25, 135, 84, 0.2);
                color: #198754;
                border-radius: 8px;
                padding: 12px;
                margin-bottom: 20px;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="floating-elements"></div>
        
        <!-- Electronics Pattern -->
        <div class="electronics-pattern" style="top: 15%; left: 5%;"><i class="bi bi-cpu"></i></div>
        <div class="electronics-pattern" style="top: 25%; right: 8%;"><i class="bi bi-lightning"></i></div>
        <div class="electronics-pattern" style="top: 45%; left: 3%;"><i class="bi bi-phone"></i></div>
        <div class="electronics-pattern" style="top: 65%; right: 5%;"><i class="bi bi-laptop"></i></div>
        <div class="electronics-pattern" style="top: 80%; left: 8%;"><i class="bi bi-tv"></i></div>
        
        <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5">
            <div class="row w-100 justify-content-center">
                <div class="col-11 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                    <div class="card auth-card border-0 rounded-4">
                        <div class="card-body p-5">
                            <div class="auth-header">
                                <div class="company-logo">
                                    <i class="bi bi-lightning-charge"></i>
                                </div>
                                <h1 class="h3">Dream Electronics</h1>
                                <p class="text-muted">Multi-Branch Electronics Management</p>
                            </div>
                            {{ $slot }}
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <p class="text-white-50 mb-0">
                            <i class="bi bi-shield-check me-2"></i>Secure Access Portal
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
