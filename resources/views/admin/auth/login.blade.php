<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
    <title>Acceso interno — XpertSystems</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,400;6..72,500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-auth-body">
    <main class="admin-login">
        <section class="admin-login__brand" aria-label="XpertSystems">
            <div class="admin-login__brand-top"><x-brand /></div>
            <div class="admin-login__brand-copy">
                <span class="admin-login__edition">Plataforma comercial · Acceso interno</span>
                <h1>Claridad para<br><em>hacer crecer</em><br>cada venta.</h1>
                <p>Clientes, pagos y rendimiento comercial en un solo lugar.</p>
            </div>
            <div class="admin-login__brand-foot"><span>XS / CONTROL</span><span>SESIÓN PROTEGIDA</span></div>
        </section>

        <section class="admin-login__form-side">
            <div class="admin-login__form-wrap">
                <header>
                    <span class="admin-login__step">01 — Identificación</span>
                    <h2>Bienvenido de vuelta.</h2>
                    <p>Ingresa con tu cuenta interna de XpertSystems.</p>
                </header>

                @if(session('auth_error'))
                    <div class="admin-alert" role="alert">{{ session('auth_error') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.login.store') }}" class="admin-login-form">
                    @csrf
                    <label>
                        <span>Correo electrónico</span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nombre@xpertsystems.xyz">
                        @error('email')<small class="admin-field-error">{{ $message }}</small>@enderror
                    </label>
                    <label>
                        <span>Contraseña</span>
                        <input type="password" name="password" required autocomplete="current-password" placeholder="Tu contraseña">
                        @error('password')<small class="admin-field-error">{{ $message }}</small>@enderror
                    </label>
                    <label class="admin-remember">
                        <input type="checkbox" name="remember" value="1">
                        <span>Recordar esta sesión</span>
                    </label>
                    <button type="submit" class="admin-login-button">Entrar al panel <span><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span></button>
                </form>

                <p class="admin-login__help">El acceso se crea únicamente desde administración. Si no puedes entrar, solicita que revisen el estado de tu cuenta.</p>
            </div>
        </section>
    </main>
</body>
</html>
