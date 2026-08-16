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
                <button type="button" class="admin-sidebar__close" @click="sidebarOpen = false" aria-label="Cerrar navegación">×</button>
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
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 13h6V4H4v9Zm0 7h6v-4H4v4Zm10 0h6v-9h-6v9Zm0-16v4h6V4h-6Z"/></svg>
                    <span>Dashboard</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <span class="admin-nav__label">Vendedores</span>
                    <a href="{{ route('admin.sellers.index') }}" class="{{ str_starts_with($currentRoute ?? '', 'admin.sellers') ? 'is-active' : '' }}" @if(str_starts_with($currentRoute ?? '', 'admin.sellers')) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3Zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3Zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5Zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5Z"/></svg>
                        <span>Equipo</span>
                    </a>
                    <a href="{{ route('admin.commissions.index') }}" class="{{ str_starts_with($currentRoute ?? '', 'admin.commissions') ? 'is-active' : '' }}" @if(str_starts_with($currentRoute ?? '', 'admin.commissions')) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2Zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.94s4.18 1.36 4.18 3.85c-.01 1.83-1.38 2.83-3.12 3.19Z"/></svg>
                        <span>Comisiones</span>
                    </a>
                    <a href="{{ route('admin.payouts.index') }}" class="{{ str_starts_with($currentRoute ?? '', 'admin.payouts') ? 'is-active' : '' }}" @if(str_starts_with($currentRoute ?? '', 'admin.payouts')) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 18v1c0 1.1-.9 2-2 2H5c-1.11 0-2-.9-2-2V5c0-1.1.89-2 2-2h14c1.1 0 2 .9 2 2v1h-9c-1.11 0-2 .9-2 2v8c0 1.1.89 2 2 2h9Zm-9-2h10V8H12v8Zm4-2.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5Z"/></svg>
                        <span>Pagos</span>
                    </a>

                    <span class="admin-nav__label">Comercial</span>
                    <span class="admin-nav__pending"><i></i>Ventas<small>Próxima fase</small></span>
                    <span class="admin-nav__pending"><i></i>Clientes<small>Próxima fase</small></span>
                    <a href="{{ route('admin.packages.index') }}" class="{{ str_starts_with($currentRoute ?? '', 'admin.packages') ? 'is-active' : '' }}" @if(str_starts_with($currentRoute ?? '', 'admin.packages')) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2Zm-6 0h-4V4h4v2Z"/></svg>
                        <span>Paquetes</span>
                    </a>
                    <span class="admin-nav__pending"><i></i>Cupones<small>Próxima fase</small></span>

                    <span class="admin-nav__label">Sistema</span>
                    <a href="{{ route('admin.users.index') }}" class="{{ str_starts_with($currentRoute ?? '', 'admin.users') ? 'is-active' : '' }}" @if(str_starts_with($currentRoute ?? '', 'admin.users')) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4Zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4Z"/></svg>
                        <span>Usuarios</span>
                    </a>
                    <span class="admin-nav__pending"><i></i>Auditoría<small>Próxima fase</small></span>
                    <span class="admin-nav__pending"><i></i>Configuración<small>Próxima fase</small></span>
                @else
                    <span class="admin-nav__label">Mi espacio</span>
                    @foreach(['Mis ventas', 'Mis clientes', 'Mis cupones', 'Mis comisiones', 'Mis pagos'] as $item)
                        <span class="admin-nav__pending"><i></i>{{ $item }}<small>Próxima fase</small></span>
                    @endforeach
                @endif
            </nav>

            <div class="admin-sidebar__user">
                <span class="admin-avatar">{{ str(auth()->user()->name)->substr(0, 1)->upper() }}</span>
                <span><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->email }}</small></span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" title="Cerrar sesión" aria-label="Cerrar sesión">↗</button>
                </form>
            </div>
        </aside>

        <div class="admin-workspace">
            <header class="admin-topbar">
                <button type="button" class="admin-menu-button" @click="sidebarOpen = true" aria-label="Abrir navegación">
                    <span></span><span></span>
                </button>
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
