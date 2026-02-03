<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Acueducto Rural') - Sistema de Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0d6efd;
            --sidebar-bg: #212529;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            z-index: 100;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 0;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: #fff;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .sidebar-brand {
            padding: 20px;
            color: #fff;
            font-size: 1.2rem;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand i {
            color: var(--primary-color);
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            font-weight: 600;
        }
        .btn-primary {
            background-color: var(--primary-color);
        }
        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
        }
        .badge-pendiente { background-color: #ffc107; color: #000; }
        .badge-facturado { background-color: #0d6efd; }
        .badge-pagada { background-color: #198754; }
        .badge-parcial { background-color: #fd7e14; }
        .badge-anulada { background-color: #dc3545; }
        .badge-activo { background-color: #198754; }
        .sidebar-section {
            color: rgba(255,255,255,0.5);
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 15px 20px 5px;
            letter-spacing: 1px;
        }
        @media print {
            .sidebar, .no-print { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-droplet-fill"></i> Acueducto Rural
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            
            <div class="sidebar-section">Gestión</div>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('subscribers.*') ? 'active' : '' }}" href="{{ route('subscribers.index') }}">
                    <i class="bi bi-people"></i> Suscriptores
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('readings.*') ? 'active' : '' }}" href="{{ route('readings.index') }}">
                    <i class="bi bi-speedometer"></i> Lecturas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}" href="{{ route('invoices.index') }}">
                    <i class="bi bi-receipt"></i> Cuotas Familiares
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.index') }}">
                    <i class="bi bi-cash-coin"></i> Abonos/Pagos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('credits.*') ? 'active' : '' }}" href="{{ route('credits.index') }}">
                    <i class="bi bi-credit-card"></i> Créditos
                </a>
            </li>
            
            <div class="sidebar-section">Configuración</div>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('settings.prices.*') ? 'active' : '' }}" href="{{ route('settings.prices.index') }}">
                    <i class="bi bi-currency-dollar"></i> Tarifas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('settings.company.*') ? 'active' : '' }}" href="{{ route('settings.company.edit') }}">
                    <i class="bi bi-building"></i> Empresa
                </a>
            </li>
        </ul>
    </nav>

    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('scripts')
</body>
</html>
