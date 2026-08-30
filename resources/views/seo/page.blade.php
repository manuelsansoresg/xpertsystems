@php
    $canonical = route($page);
    $waMessage = rawurlencode("Hola, vi la página de {$serviceType} de XpertSystems y quiero información para mi negocio.");
    $waUrl = $whatsapp ? "https://wa.me/{$whatsapp}?text={$waMessage}" : '#contacto-directo';
    $breadcrumbs = [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => $h1, 'item' => $canonical],
    ];
    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'BreadcrumbList',
                'itemListElement' => $breadcrumbs,
            ],
            [
                '@type' => 'Service',
                'name' => $serviceType,
                'serviceType' => $serviceType,
                'provider' => ['@type' => 'Organization', 'name' => 'XpertSystems', 'url' => route('home')],
                'areaServed' => [['@type' => 'Country', 'name' => 'México']],
                'url' => $canonical,
                'description' => $description,
            ],
        ],
    ];
@endphp

<x-layouts.app :title="$title" :description="$description" :canonical="$canonical" :script="$page === 'precios' ? ['resources/js/pricing.js', 'resources/js/seo.js'] : 'resources/js/seo.js'">
    @push('head')
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <div class="site-shell seo-shell">
        <header class="site-header seo-header" data-site-header>
            <div class="container header__inner">
                <a href="{{ route('home') }}" class="brand-link" aria-label="XpertSystems, inicio"><x-brand /></a>
                <nav class="desktop-nav" aria-label="Navegación principal">
                    <a href="{{ route('paginas-web') }}" @if($page === 'paginas-web') aria-current="page" @endif>Páginas web</a>
                    <a href="{{ route('landing-pages') }}" @if($page === 'landing-pages') aria-current="page" @endif>Landing pages</a>
                    <a href="{{ route('tiendas-en-linea') }}" @if($page === 'tiendas-en-linea') aria-current="page" @endif>Tiendas</a>
                    <a href="{{ route('portafolio') }}" @if($page === 'portafolio') aria-current="page" @endif>Portafolio</a>
                    <a href="{{ route('precios') }}" @if($page === 'precios') aria-current="page" @endif>Precios</a>
                </nav>
                <a href="{{ route('contacto') }}" class="button button--small button--cream desktop-cta" data-analytics="quote_request">Cotizar <span>↘</span></a>
                <button type="button" class="menu-toggle" data-menu-toggle aria-expanded="false" aria-controls="mobile-menu"><span></span><span></span><span class="sr-only">Abrir menú</span></button>
            </div>
            <div id="mobile-menu" class="mobile-menu" data-mobile-menu hidden>
                <nav aria-label="Navegación móvil">
                    <a href="{{ route('paginas-web') }}">Páginas web</a><a href="{{ route('landing-pages') }}">Landing pages</a><a href="{{ route('tiendas-en-linea') }}">Tiendas en línea</a><a href="{{ route('portafolio') }}">Portafolio</a><a href="{{ route('precios') }}">Precios</a>
                    <a href="{{ route('contacto') }}" class="button button--gold">Cotizar proyecto</a>
                </nav>
            </div>
        </header>

        <main id="contenido">
            <section class="seo-hero section--dark">
                <div class="seo-hero__grid" aria-hidden="true"></div>
                <div class="container seo-hero__inner">
                    <nav class="breadcrumbs" aria-label="Migas de pan"><a href="{{ route('home') }}">Inicio</a><span aria-hidden="true">/</span><span>{{ $eyebrow }}</span></nav>
                    <p class="seo-eyebrow">{{ $eyebrow }}</p>
                    <h1>{{ $h1 }}</h1>
                    <p class="seo-hero__lead">{{ $lead }}</p>
                    <div class="seo-actions">
                        <a href="{{ $waUrl }}" target="{{ $whatsapp ? '_blank' : '_self' }}" rel="noopener" class="button button--gold button--lg" data-analytics="click_whatsapp">{{ $primaryCta }} <span>↗</span></a>
                        <a href="{{ route('precios') }}" class="button button--outline button--lg" data-analytics="view_packages">Ver precios <span>→</span></a>
                    </div>
                    <ul class="seo-assurances" aria-label="Elementos incluidos"><li>Diseño móvil</li><li>SSL</li><li>Atención personal</li><li>Sin promesas irreales de ranking</li></ul>
                </div>
            </section>

            <section class="seo-intro section--cream">
                <div class="container seo-intro__grid">
                    <p class="seo-section-label">Lo que gana tu negocio</p>
                    <div><h2>{{ $introTitle }}</h2><p>{{ $intro }}</p></div>
                </div>
                <div class="container seo-benefit-grid">
                    @foreach($benefits as $benefit)
                        <article><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $benefit[0] }}</h3><p>{{ $benefit[1] }}</p></article>
                    @endforeach
                </div>
            </section>

            @if($page === 'portafolio')
                <section class="seo-work section--light" aria-labelledby="work-title">
                    <div class="container"><p class="seo-section-label">Trabajo seleccionado</p><h2 id="work-title">Proyectos reales, presentados sin inventar resultados</h2>
                        <div class="seo-project-grid">
                            @forelse($projects as $project)
                                @php($projectImage = file_exists(public_path('images/portafolio/'.$project->slug.'.webp')) ? asset('images/portafolio/'.$project->slug.'.webp') : asset('images/portafolio/'.$project->slug.'.png'))
                                <article><img src="{{ $projectImage }}" alt="Vista del proyecto web {{ $project->name }}" width="1200" height="760" loading="{{ $loop->first ? 'eager' : 'lazy' }}" decoding="async"><div><p>{{ $project->category ?: 'Diseño y desarrollo web' }}</p><h3>{{ $project->name }}</h3><span>{{ $project->description ?: 'Proyecto digital con una experiencia clara y adaptable.' }}</span></div></article>
                            @empty
                                <p>Estamos preparando la selección pública de proyectos.</p>
                            @endforelse
                        </div>
                    </div>
                </section>
            @endif

            @if($page === 'precios')
                @include('partials.package-pricing', ['checkoutSource' => 'precios'])
            @endif

            @if(count($includes))
                <section class="seo-includes section--dark"><div class="container seo-includes__grid"><div><p class="seo-section-label">Alcance del servicio</p><h2>Lo esencial para publicar con confianza</h2><p>La propuesta definitiva se confirma antes de comenzar. Estos elementos resumen el alcance base del servicio.</p></div><ul>@foreach($includes as $item)<li><span aria-hidden="true">✓</span>{{ $item }}</li>@endforeach</ul></div></section>
            @endif

            @if(count($process))
                <section class="seo-process section--light"><div class="container"><p class="seo-section-label">Proceso de trabajo</p><h2>De la idea a un sitio publicado</h2><ol>@foreach($process as $step)<li><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><div><h3>{{ $step[0] }}</h3><p>{{ $step[1] }}</p></div></li>@endforeach</ol></div></section>
            @endif

            <section class="seo-faq section--cream"><div class="container seo-faq__grid"><div><p class="seo-section-label">Preguntas frecuentes</p><h2>Respuestas antes de decidir</h2></div><div class="seo-faq__list">@foreach($faqs as $faq)<details @if($loop->first) open @endif><summary>{{ $faq[0] }}<span aria-hidden="true">+</span></summary><p>{{ $faq[1] }}</p></details>@endforeach</div></div></section>

            <section class="seo-related section--light"><div class="container"><p class="seo-section-label">Siguiente lectura</p><h2>Encuentra la opción adecuada para tu objetivo</h2><div class="seo-related__grid">@foreach($related as $item)<a href="{{ route($item[2]) }}"><span>{{ $item[0] }}</span><strong>{{ $item[1] }}</strong><i aria-hidden="true">→</i></a>@endforeach</div></div></section>

            <section id="contacto-directo" class="seo-final section--dark"><div class="container"><p class="seo-section-label">Tu siguiente paso</p><h2>Haz que tu página trabaje para conseguir conversaciones, no solo visitas.</h2><p>Cuéntanos qué necesita tu negocio y te orientamos con un alcance claro.</p><div class="seo-actions"><a href="{{ $waUrl }}" target="{{ $whatsapp ? '_blank' : '_self' }}" rel="noopener" class="button button--gold button--lg" data-analytics="click_whatsapp">Hablar por WhatsApp <span>↗</span></a>@if($contactEmail)<a href="mailto:{{ $contactEmail }}" class="button button--outline button--lg" data-analytics="click_email">Enviar correo <span>→</span></a>@endif</div></div></section>
        </main>

        <footer class="seo-footer"><div class="container seo-footer__grid"><div><a href="{{ route('home') }}" aria-label="XpertSystems, inicio"><x-brand /></a><p>Páginas web para negocios en México.</p></div><nav aria-label="Servicios"><strong>Servicios</strong><a href="{{ route('landing-pages') }}">Landing pages</a><a href="{{ route('paginas-web') }}">Páginas web</a><a href="{{ route('tiendas-en-linea') }}">Tiendas en línea</a><a href="{{ route('paginas-web-merida') }}">Diseño web en Mérida</a></nav><nav aria-label="Empresa"><strong>Empresa</strong><a href="{{ route('portafolio') }}">Portafolio</a><a href="{{ route('precios') }}">Precios</a><a href="{{ route('contacto') }}">Contacto</a><a href="{{ route('privacy') }}">Privacidad</a></nav></div><div class="container seo-footer__bottom"><span>© {{ date('Y') }} XpertSystems</span><a href="{{ route('terms') }}">Términos del servicio</a></div></footer>

        @if($whatsapp)<a href="{{ $waUrl }}" target="_blank" rel="noopener" class="floating-wa" aria-label="Hablar por WhatsApp" data-analytics="click_whatsapp"><span>WA</span><i></i></a>@endif
    </div>
</x-layouts.app>
