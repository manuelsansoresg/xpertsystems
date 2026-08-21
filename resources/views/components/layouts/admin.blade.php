@props(['title' => 'Panel'])
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $title }} — XpertSystems</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,400;6..72,500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    <a href="#admin-content" class="skip-link">Saltar al contenido</a>
    <div class="admin-shell" x-data="{ sidebarOpen: false }">
        <div class="admin-shell__veil" x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"></div>
        <aside class="admin-sidebar" :class="{ 'is-open': sidebarOpen }" aria-label="Navegación del panel">
            <div class="admin-sidebar__brand">
                <a href="{{ route('admin.dashboard') }}" aria-label="XpertSystems, panel principal"><x-brand /></a>
                <button type="button" class="admin-sidebar__close" @click="sidebarOpen = false" aria-label="Cerrar navegación"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
            </div>

            <div class="admin-sidebar__context">
                <span>Área interna</span>
                <strong>{{ auth()->user()->isAdmin() ? 'Administración' : 'Ventas' }}</strong>
            </div>

            <nav class="admin-nav">
                @php
                    $currentRoute = request()->route()?->getName();
                @endphp

                <span class="admin-nav__label">Inicio</span>
                <a href="{{ route('admin.dashboard') }}" class="{{ str_starts_with($currentRoute ?? '', 'admin.dashboard') ? 'is-active' : '' }}" @if(str_starts_with($currentRoute ?? '', 'admin.dashboard')) aria-current="page" @endif>
                    <i class="fa-solid fa-table-columns" aria-hidden="true"></i>
                    <span>Dashboard</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <span class="admin-nav__label">Vendedores</span>
                    <a href="{{ route('admin.sellers.index') }}" class="{{ str_starts_with($currentRoute ?? '', 'admin.sellers') ? 'is-active' : '' }}" @if(str_starts_with($currentRoute ?? '', 'admin.sellers')) aria-current="page" @endif>
                        <i class="fa-solid fa-user-group" aria-hidden="true"></i>
                        <span>Equipo</span>
                    </a>
                    <a href="{{ route('admin.commissions.index') }}" class="{{ str_starts_with($currentRoute ?? '', 'admin.commissions') ? 'is-active' : '' }}" @if(str_starts_with($currentRoute ?? '', 'admin.commissions')) aria-current="page" @endif>
                        <i class="fa-solid fa-coins" aria-hidden="true"></i>
                        <span>Comisiones</span>
                    </a>
                    <a href="{{ route('admin.payouts.index') }}" class="{{ str_starts_with($currentRoute ?? '', 'admin.payouts') ? 'is-active' : '' }}" @if(str_starts_with($currentRoute ?? '', 'admin.payouts')) aria-current="page" @endif>
                        <i class="fa-solid fa-wallet" aria-hidden="true"></i>
                        <span>Pagos</span>
                    </a>

                    <span class="admin-nav__label">Comercial</span>
                    <span class="admin-nav__pending"><i></i>Ventas<small>Próxima fase</small></span>
                    <a href="{{ route('admin.customers.index') }}" class="{{ str_starts_with($currentRoute ?? '', 'admin.customers') ? 'is-active' : '' }}" @if(str_starts_with($currentRoute ?? '', 'admin.customers')) aria-current="page" @endif>
                        <i class="fa-solid fa-address-book" aria-hidden="true"></i>
                        <span>Clientes</span>
                    </a>
                    <a href="{{ route('admin.packages.index') }}" class="{{ str_starts_with($currentRoute ?? '', 'admin.packages') ? 'is-active' : '' }}" @if(str_starts_with($currentRoute ?? '', 'admin.packages')) aria-current="page" @endif>
                        <i class="fa-solid fa-box-open" aria-hidden="true"></i>
                        <span>Paquetes</span>
                    </a>
                    <a href="{{ route('admin.coupons.index') }}" class="{{ str_starts_with($currentRoute ?? '', 'admin.coupons') ? 'is-active' : '' }}" @if(str_starts_with($currentRoute ?? '', 'admin.coupons')) aria-current="page" @endif>
                        <i class="fa-solid fa-ticket" aria-hidden="true"></i>
                        <span>Cupones</span>
                    </a>

                    <span class="admin-nav__label">Sistema</span>
                    <a href="{{ route('admin.users.index') }}" class="{{ str_starts_with($currentRoute ?? '', 'admin.users') ? 'is-active' : '' }}" @if(str_starts_with($currentRoute ?? '', 'admin.users')) aria-current="page" @endif>
                        <i class="fa-solid fa-users-gear" aria-hidden="true"></i>
                        <span>Usuarios</span>
                    </a>
                    <span class="admin-nav__pending"><i></i>Auditoría<small>Próxima fase</small></span>
                    <span class="admin-nav__pending"><i></i>Configuración<small>Próxima fase</small></span>
                @else
                    <span class="admin-nav__label">Mi espacio</span>
                    <span class="admin-nav__pending"><i></i>Mis ventas<small>Próxima fase</small></span>
                    <span class="admin-nav__pending"><i></i>Mis clientes<small>Próxima fase</small></span>
                    <a href="{{ route('seller.coupons.index') }}" class="{{ str_starts_with($currentRoute ?? '', 'seller.coupons') ? 'is-active' : '' }}" @if(str_starts_with($currentRoute ?? '', 'seller.coupons')) aria-current="page" @endif>
                        <i class="fa-solid fa-ticket" aria-hidden="true"></i>
                        <span>Mis cupones</span>
                    </a>
                    <span class="admin-nav__pending"><i></i>Mis comisiones<small>Próxima fase</small></span>
                    <span class="admin-nav__pending"><i></i>Mis pagos<small>Próxima fase</small></span>
                @endif
            </nav>

            <div class="admin-sidebar__user">
                <span class="admin-avatar">{{ str(auth()->user()->name)->substr(0, 1)->upper() }}</span>
                <span><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->email }}</small></span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" title="Cerrar sesión" aria-label="Cerrar sesión"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i></button>
                </form>
            </div>
        </aside>

        <div class="admin-workspace">
            <header class="admin-topbar">
                <button type="button" class="admin-menu-button" @click="sidebarOpen = true" aria-label="Abrir navegación"><i class="fa-solid fa-bars" aria-hidden="true"></i></button>
                <div>
                    <span>Panel XpertSystems</span>
                    <strong>{{ $title }}</strong>
                </div>
                <div class="admin-topbar__meta">
                    <span>{{ now()->locale('es')->translatedFormat('d M Y') }}</span>
                    <i class="status-dot"></i>
                    <span>Sistema operativo</span>
                </div>
            </header>

            <main id="admin-content" class="admin-main">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
