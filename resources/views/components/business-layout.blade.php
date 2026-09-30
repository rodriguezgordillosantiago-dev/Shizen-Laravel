<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Panel' }} | Shizen Negocio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=20260929-logout-2">
</head>
<body class="layout">
    <nav class="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('business.dashboard') }}" class="sidebar-logo">
                <img src="{{ asset('assets/logo.png') }}" alt="Shizen Negocio">
            </a>
        </div>
        <div class="sidebar-nav">
            <p class="sidebar-label">Menu principal</p>
            <a class="nav-item {{ request()->routeIs('business.dashboard') ? 'active' : '' }}" href="{{ route('business.dashboard') }}"><i class="bx bx-home-alt-2"></i>Dashboard</a>
            <a class="nav-item {{ request()->routeIs('business.products*') ? 'active' : '' }}" href="{{ route('business.products') }}"><i class="bx bx-package"></i>Productos</a>
            <a class="nav-item {{ request()->routeIs('business.orders*') ? 'active' : '' }}" href="{{ route('business.orders') }}"><i class="bx bx-receipt"></i>Pedidos</a>
            <a class="nav-item {{ request()->routeIs('business.clients') ? 'active' : '' }}" href="{{ route('business.clients') }}"><i class="bx bx-group"></i>Clientes</a>
            <a class="nav-item {{ request()->routeIs('business.notifications') ? 'active' : '' }}" href="{{ route('business.notifications') }}"><i class="bx bx-bell"></i>Notificaciones</a>
            <a class="nav-item {{ request()->routeIs('business.settings*') ? 'active' : '' }}" href="{{ route('business.settings') }}"><i class="bx bx-cog"></i>Configuración</a>
            <a class="nav-item {{ request()->routeIs('business.profile*') ? 'active' : '' }}" href="{{ route('business.profile') }}"><i class="bx bx-user"></i>Mi perfil</a>
        </div>
        <div class="sidebar-footer">
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="nav-item nav-item-logout" type="submit"><svg class="logout-icon" width="18" height="18" fill="currentColor" viewBox="0 0 512 512" aria-hidden="true"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path></svg>Cerrar sesión</button>
            </form>
        </div>
    </nav>
    <div class="main">
        <header class="topbar">
            <div class="topbar-left">
                <span class="topbar-title">{{ $title ?? 'Panel' }}</span>
                <span class="topbar-sub">Bienvenido, {{ session('business_user.nombre') }}</span>
            </div>
            <div class="topbar-user">
                <div class="topbar-avatar">{{ strtoupper(substr(session('business_user.nombre'), 0, 1)) }}</div>
                <div>
                    <div class="topbar-name">{{ session('business_user.negocio') }}</div>
                    <div class="topbar-role">Administrador del negocio</div>
                </div>
            </div>
        </header>
        <main class="content">
            @if (session('success'))
                <div class="alert alert-success"><i class="bx bx-check-circle"></i>{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger"><i class="bx bx-error-circle"></i>{{ $errors->first() }}</div>
            @endif
            {{ $slot }}
        </main>
    </div>
    @stack('scripts')
</body>
</html>
