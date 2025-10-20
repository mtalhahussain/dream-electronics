<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Dream Electronics</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    @vite(['resources/js/app.js'])
    
    <style>
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1.25rem;
            border-radius: 0.375rem;
            margin: 0.125rem 0.5rem;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.1);
        }
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }
        @media (max-width: 991.98px) {
            .main-content {
                margin-left: 0;
            }
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.show {
                margin-left: 0;
            }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Sidebar -->
    <div class="sidebar position-fixed top-0 start-0 d-flex flex-column p-3" id="sidebar">
        <div class="d-flex align-items-center mb-3 text-white">
            <i class="bi bi-lightning-charge-fill fs-3 me-2"></i>
            <span class="fs-5 fw-bold">Dream Electronics</span>
        </div>
        
        <nav class="nav nav-pills flex-column flex-nowrap overflow-hidden">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                <i class="bi bi-box-seam me-2"></i>Products
            </a>
            <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                <i class="bi bi-tags me-2"></i>Categories
            </a>
            <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                <i class="bi bi-people me-2"></i>Customers
            </a>
            <a class="nav-link {{ request()->routeIs('sales.index') || request()->routeIs('sales.show') || request()->routeIs('sales.create') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                <i class="bi bi-cart3 me-2"></i>Sales
            </a>
            <a class="nav-link {{ request()->routeIs('sales.installments') ? 'active' : '' }}" href="{{ route('sales.installments') }}">
                <i class="bi bi-calendar-check me-2"></i>Installments
            </a>
            <a class="nav-link {{ request()->routeIs('finance.*') || request()->routeIs('expenses.*') || request()->routeIs('stock-credits.*') || request()->routeIs('salary-payments.*') ? 'active' : '' }}" href="{{ route('finance.index') }}">
                <i class="bi bi-graph-up me-2"></i>Finance
            </a>
            
            <!-- Finance Sub-menu -->
            @if(request()->routeIs('finance.*') || request()->routeIs('expenses.*') || request()->routeIs('stock-credits.*') || request()->routeIs('salary-payments.*'))
            <div class="ms-3 mb-2">
                <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }} small" href="{{ route('expenses.index') }}">
                    <i class="bi bi-receipt me-2"></i>Manage Expenses
                </a>
                <a class="nav-link {{ request()->routeIs('stock-credits.*') ? 'active' : '' }} small" href="{{ route('stock-credits.index') }}">
                    <i class="bi bi-box me-2"></i>Manage Stock Credits
                </a>
                <a class="nav-link {{ request()->routeIs('salary-payments.*') ? 'active' : '' }} small" href="{{ route('salary-payments.index') }}">
                    <i class="bi bi-people me-2"></i>Manage Salaries
                </a>
            </div>
            @endif
            <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                <i class="bi bi-person-badge me-2"></i>Employees
            </a>
            
           
            <a class="nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}" href="{{ route('branches.index') }}">
                <i class="bi bi-building me-2"></i>Branches
            </a>
            <a class="nav-link {{ request()->routeIs('sms.*') ? 'active' : '' }}" href="{{ route('sms.index') }}">
                <i class="bi bi-chat-dots me-2"></i>SMS
            </a>
            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                <i class="bi bi-gear me-2"></i>Settings
            </a>
        </nav>
        
        <div class="mt-auto">
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="nav-link border-0 bg-transparent text-start w-100">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </form>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom">
            <div class="container-fluid">
                <button class="navbar-toggler d-lg-none" type="button" onclick="toggleSidebar()">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <h1 class="navbar-brand mb-0 h1 ms-3 ms-lg-0">@yield('title', 'Dashboard')</h1>
                
                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5 me-2"></i>
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2"></i>Profile
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Page Content -->
        <div class="container-fluid p-4">
            @yield('content')
        </div>
        
        <!-- Footer -->
        <footer class="bg-white border-top mt-5 py-3">
            <div class="container-fluid">
                <div class="text-center text-muted">
                    <small>&copy; {{ date('Y') }} Dream Electronics. All rights reserved.</small>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Toast Container -->
    <div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 1080;">
        <div id="toastContainer"></div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }
        
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toastContainer');
            const toastId = 'toast-' + Date.now();
            
            const iconClass = type === 'success' ? 'bi-check-circle-fill text-success' : 
                             type === 'error' ? 'bi-x-circle-fill text-danger' : 
                             'bi-info-circle-fill text-info';
            
            const toastHtml = `
                <div class="toast" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <i class="bi ${iconClass} me-2"></i>
                        <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            const toastElement = new bootstrap.Toast(document.getElementById(toastId));
            toastElement.show();
            
            // Remove toast element after it's hidden
            document.getElementById(toastId).addEventListener('hidden.bs.toast', function () {
                this.remove();
            });
        }
    </script>
    
    @stack('scripts')
</body>
</html>