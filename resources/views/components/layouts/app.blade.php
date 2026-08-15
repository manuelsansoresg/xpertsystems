@props(['title' => null, 'description' => null, 'canonical' => null])
<!DOCTYPE html>
<html lang="es-MX" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#071923">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'XpertSystems — Desarrollo web para negocios' }}</title>
    <meta name="description" content="{{ $description ?? 'Diseño y desarrollo de páginas web profesionales para negocios en México. Dominio, hosting y SSL incluidos durante el primer año.' }}">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="es_MX">
    <meta property="og:site_name" content="XpertSystems">
    <meta property="og:title" content="{{ $title ?? 'XpertSystems — Desarrollo web para negocios' }}">
    <meta property="og:description" content="{{ $description ?? 'Sitios web profesionales que generan confianza y convierten visitas en clientes.' }}">
    <meta property="og:url" content="{{ $canonical ?? url()->current() }}">
    @if(file_exists(public_path('og-xpertsystems.png')))
        <meta property="og:image" content="{{ asset('og-xpertsystems.png') }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="{{ asset('og-xpertsystems.png') }}">
    @else
        <meta name="twitter:card" content="summary">
    @endif
    <meta name="twitter:title" content="{{ $title ?? 'XpertSystems — Desarrollo web para negocios' }}">
    <meta name="twitter:description" content="{{ $description ?? 'Sitios web profesionales que generan confianza y convierten visitas en clientes.' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Manrope:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')

    @if(config('services.analytics.ga4_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.analytics.ga4_id') }}"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','{{ config('services.analytics.ga4_id') }}');</script>
    @endif
    @if(config('services.analytics.meta_pixel_id'))
        <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','{{ config('services.analytics.meta_pixel_id') }}');fbq('track','PageView');</script>
    @endif
</head>
<body class="antialiased">
    <a href="#contenido" class="skip-link">Saltar al contenido</a>
    {{ $slot }}
</body>
</html>
