<x-layouts.app>
    @push('head')
        <script type="application/ld+json">{!! json_encode([
            '@'.'context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => 'XpertSystems',
            'url' => route('home'),
            'description' => 'Diseño y desarrollo de sitios web profesionales para negocios.',
            'areaServed' => ['México', 'Internacional'],
            'priceRange' => '$$'
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    @php
        $waBase = $whatsapp ? "https://wa.me/{$whatsapp}?text=" : '#contacto';
        $waHero = $whatsapp ? $waBase.rawurlencode('Hola, vi el sitio de XpertSystems y quisiera información para crear mi página web.') : '#contacto';
        $waFinal = $whatsapp ? $waBase.rawurlencode('Hola, quiero comenzar mi página web con XpertSystems. ¿Me ayudan a elegir el paquete?') : '#contacto';
    @endphp

    <div x-data="siteShell" class="site-shell">
        <header class="site-header" :class="{ 'is-scrolled': scrolled }" x-init="initHeader()">
            <div class="container header__inner">
                <a href="#inicio" class="brand-link" aria-label="XpertSystems, inicio"><x-brand /></a>
                <nav class="desktop-nav" aria-label="Navegación principal">
                    <a href="#servicios">Servicios</a><a href="#proyectos">Proyectos</a><a href="#paquetes">Paquetes</a><a href="#proceso">Cómo funciona</a><a href="#preguntas">Preguntas</a>
                </nav>
                <a href="#paquetes" class="button button--small button--cream desktop-cta" data-analytics="view_packages">Ver paquetes <span>↘</span></a>
                <button type="button" class="menu-toggle" @click="menuOpen = !menuOpen" :aria-expanded="menuOpen" aria-controls="mobile-menu"><span></span><span></span><span class="sr-only">Abrir menú</span></button>
            </div>
            <div id="mobile-menu" class="mobile-menu" x-cloak x-show="menuOpen" x-transition.opacity @click.outside="menuOpen = false">
                <nav aria-label="Navegación móvil">
                    <a @click="menuOpen=false" href="#servicios">Servicios</a><a @click="menuOpen=false" href="#proyectos">Proyectos</a><a @click="menuOpen=false" href="#paquetes">Paquetes</a><a @click="menuOpen=false" href="#proceso">Cómo funciona</a><a @click="menuOpen=false" href="#preguntas">Preguntas</a>
                    <a @click="menuOpen=false" href="#paquetes" class="button button--gold">Ver paquetes</a>
                </nav>
            </div>
        </header>

        <main id="contenido">
            <section id="inicio" class="hero section--dark">
                <div class="hero__grid-lines" aria-hidden="true"></div>
                <div class="container hero__inner hero__layout">
                    <div class="hero__content">
                        <div class="hero__eyebrow reveal-line"><span class="status-dot"></span> Desarrollo web para negocios que quieren crecer</div>
                        <div class="hero__headline">
                            <h1>
                                <span>Tu negocio merece</span>
                                <span>más que solo</span>
                                <em>redes sociales.</em>
                            </h1>
                        </div>
                        <div class="hero__copy reveal-up">
                            <p>Creamos sitios web profesionales diseñados para generar confianza, mostrar tus servicios y convertir visitantes en clientes.</p>
                            <div class="hero__actions">
                                <a href="#paquetes" class="button button--gold" data-analytics="view_packages">Ver paquetes <span>↘</span></a>
                                <a href="{{ $waHero }}" class="button button--text" target="{{ $whatsapp ? '_blank' : '_self' }}" rel="noopener" data-analytics="contact_whatsapp">Hablar por WhatsApp <span>↗</span></a>
                            </div>
                            <div class="hero__assurance"><span>✓</span> Dominio + Hosting + SSL incluidos durante 1 año.</div>
                        </div>
                    </div>

                    <div class="hero-stage">
                        <div class="hero-stage__halo" aria-hidden="true"></div>
                        <img class="hero-stage__image" src="{{ asset('images/hero.png') }}" alt="Sitio web de XpertSystems mostrado en computadora, teléfono y paneles de servicios">
                    </div>
                </div>
            </section>

            <aside class="trust-strip" aria-label="Beneficios incluidos">
                <div class="trust-strip__track">
                    @foreach(['Dominio incluido','Hosting incluido','SSL','Diseño responsive','Soporte técnico','Atención personalizada'] as $item)
                        <span>{{ $item }}</span>
                    @endforeach
                </div>
            </aside>

            <section id="servicios" class="problem section--cream">
                <div class="container problem__grid">
                    <div class="problem__left">
                        <div class="section-kicker"><span>02</span> El problema</div>
                        <h2 class="problem__headline">¿Tu negocio vive solo en <em>redes sociales?</em></h2>
                        <p class="problem__lead">Las redes te ayudan a atraer personas. Tu sitio debe encargarse de explicar, generar confianza y facilitar el contacto.</p>
                    </div>
                    <div class="problem__right">
                        <div class="problem__signal" aria-hidden="true">
                            <div class="signal__ring"><span class="signal__value" data-count="24">0</span><small>/ 7</small></div>
                            <p>Tu sitio sigue trabajando<br>aunque tú no estés conectado.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="solucion" class="solution section--light">
                <div class="container solution__grid">
                    <div class="solution__copy">
                        <div class="section-kicker"><span>03</span> La solución</div>
                        <h2><span>No hacemos páginas</span><span>solo para que</span><em>"se vean bonitas".</em></h2>
                        <div class="solution__accent-line" aria-hidden="true"></div>
                        <p>Diseñamos cada página para que tu cliente entienda lo que ofreces, confíe en tu negocio y sepa cómo contactarte.</p>
                        <div class="solution__outcomes">
                            <article><span>01</span><i></i><h3>Claridad</h3><p>Estructura que comunica tu propuesta sin ruido.</p></article>
                            <article><span>02</span><i></i><h3>Confianza</h3><p>Diseño profesional que transmite credibilidad y seguridad.</p></article>
                            <article><span>03</span><i></i><h3>Contacto</h3><p>Facilitamos que te encuentren y sepan cómo hablarte.</p></article>
                            <article><span>04</span><i></i><h3>Oportunidades</h3><p>Convertimos visitas en conversaciones que hacen crecer tu negocio.</p></article>
                        </div>
                    </div>

                    <div class="solution__visual" aria-label="Recorrido de una visita hasta convertirse en contacto">
                        <div class="blueprint-grid" aria-hidden="true"></div>
                        <div class="solution__method">
                            <header class="method__intro">
                                <span>El recorrido</span>
                                <p>De una primera visita a una conversación real.</p>
                            </header>
                            <ol class="method__timeline">
                                <li class="method__step">
                                    <span class="method__marker">01</span>
                                    <div><h3>Visita</h3><p>Llegan por curiosidad.</p></div>
                                </li>
                                <li class="method__step">
                                    <span class="method__marker">02</span>
                                    <div><h3>Entiende</h3><p>Entiende tu propuesta, ve el valor y cómo ayudas.</p></div>
                                </li>
                                <li class="method__step">
                                    <span class="method__marker">03</span>
                                    <div><h3>Confía</h3><p>Tu mensaje genera claridad y confianza. Saben que están en buenas manos.</p></div>
                                </li>
                                <li class="method__step method__step--final">
                                    <span class="method__marker">04</span>
                                    <div><h3>Contacta</h3><p>Dan el paso. Es fácil contactarte y empiezan la conversación.</p></div>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            @php
                $portfolioProjects = $projects->map(fn($p) => [
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'category' => $p->category,
                    'description' => $p->description,
                    'desktop_image' => $p->desktop_image ?: (file_exists(public_path('images/portafolio/'.$p->slug.'.png')) ? asset('images/portafolio/'.$p->slug.'.png') : null),
                    'mobile_image' => $p->mobile_image ?: (file_exists(public_path('images/portafolio/'.$p->slug.'-mobile.png')) ? asset('images/portafolio/'.$p->slug.'-mobile.png') : null),
                    'url' => $p->url,
                    'accent' => $p->accent,
                ]);
            @endphp
            <section id="proyectos" class="portfolio section--dark" x-data="portfolioShowcase(@js($portfolioProjects))">
                <div class="container">
                    <div class="portfolio__intro">
                        <div class="section-kicker section-kicker--light"><span>04</span> Trabajo seleccionado</div>
                        <h2><span>Proyectos con</span><span>una idea clara detrás.</span></h2>
                        <p>Diseño y desarrollo digital pensado para impulsar negocios con propósito.</p>
                    </div>
                    <div class="portfolio__showcase">
                        <div class="portfolio__rail" role="tablist" aria-label="Proyectos">
                            <template x-for="(project, index) in projects" :key="project.slug">
                                <button type="button" role="tab" :aria-selected="active === index" :class="{ 'is-active': active === index }" @mouseenter="active=index" @focus="active=index" @click="active=index">
                                    <span x-text="String(index + 1).padStart(2,'0')"></span><strong x-text="project.name"></strong><i>↗</i>
                                </button>
                            </template>
                        </div>
                        <div class="portfolio__stage" :style="`--project-accent:${current.accent || '#d8b675'}`" aria-live="polite">
                            <div class="project-meta"><span aria-hidden="true"></span><small x-text="current.category || 'Proyecto web'"></small></div>
                            <div class="project-canvas">
                                <div class="project-browser">
                                    <template x-if="current.desktop_image"><img :src="current.desktop_image" :alt="`Vista de ${current.name}`"></template>
                                    <template x-if="!current.desktop_image"><div class="project-art"><div class="browser__bar"><i></i><i></i><i></i><span x-text="current.slug + '.com'"></span></div><div class="project-art__inner"><div class="project-art__nav"><strong x-text="current.name"></strong><span>Proyecto / 2026</span></div><div class="project-art__body"><small>DISEÑO WEB A MEDIDA</small><b>Una presencia<br>con intención.</b><p>Claridad, identidad y una experiencia pensada para conectar.</p><em>Descubrir →</em></div></div></div></template>
                                </div>
                                <template x-if="current.mobile_image">
                                    <div class="project-phone">
                                        <span class="project-phone__notch" aria-hidden="true"></span>
                                        <img :src="current.mobile_image" :alt="`Vista móvil de ${current.name}`">
                                    </div>
                                </template>
                            </div>
                            <div class="project-caption">
                                <div class="project-caption__copy"><h3 x-text="current.name"></h3><p x-text="current.description || 'Diseño y desarrollo de una experiencia digital serena, clara y enfocada en el usuario.'"></p></div>
                                <template x-if="current.url"><a :href="current.url" target="_blank" rel="noopener">Ver proyecto <span>→</span></a></template>
                                <template x-if="!current.url"><span class="project-caption__link is-disabled" aria-disabled="true">Ver proyecto <i>→</i></span></template>
                            </div>
                            <div class="portfolio__mobile-controls"><button @click="previous" aria-label="Proyecto anterior">←</button><span x-text="`${active+1} / ${projects.length}`"></span><button @click="next" aria-label="Proyecto siguiente">→</button></div>
                        </div>
                    </div>
                </div>
            </section>

            @php
                $quotePackage = $packages->firstWhere('requires_quote', true);
            @endphp
            <section id="paquetes" class="pricing section--cream" x-data="quoteModal">
                <div class="container">
                    <header class="pricing__header">
                        <div class="section-kicker"><span>05</span> Elige cómo empezar</div>
                        <h2>Tres formas de empezar.<br><em>Una meta: hacer crecer tu presencia digital.</em></h2>
                        <p>Todos los paquetes incluyen una base técnica sólida para publicar con confianza desde el primer día.</p>
                    </header>

                    <div class="pricing__grid">
                        @foreach($packages as $package)
                            @php
                                $summaryFeatures = $package->featureItems->where('visible_summary', true)->where('active', true);
                                $detailFeatures = $package->featureItems->where('visible_summary', false)->where('active', true);
                            @endphp

                            <article class="pkg pkg--{{ $package->is_featured ? 'featured' : ($loop->first ? 'starter' : 'store') }} package-reveal">
                                <div class="pkg__header">
                                    <span class="pkg__number">0{{ $loop->iteration }}</span>
                                    @if($package->badge)<span class="pkg__badge">{{ $package->badge }}</span>@endif
                                </div>

                                <h3 class="pkg__name">{{ $package->name }}</h3>
                                <p class="pkg__desc">{{ $package->short_description }}</p>

                                <div class="pkg__price-block">
                                    <div class="pkg__price">
                                        @if($package->price_type === 'starting_at')<span class="pkg__price-label">Desde</span>@endif
                                        @if($package->price_type === 'quote')
                                            <strong>Cotizar</strong>
                                        @else
                                            <strong>${{ number_format((float)$package->price, 0) }}</strong>
                                            <span class="pkg__currency">MXN</span>
                                        @endif
                                    </div>
                                    @if($package->price_type !== 'quote' && $package->direct_checkout)
                                        <div class="pkg__payment-type">
                                            <span>PAGO ÚNICO</span>
                                        </div>
                                    @endif
                                    
                                </div>

                                <ul class="pkg__features">
                                    @foreach($summaryFeatures as $feature)<li><span>✓</span>{{ $feature->title }}</li>@endforeach
                                </ul>

                                @if($detailFeatures->isNotEmpty())
                                    <div class="pkg__details" x-data="{ open: false }">
                                        <button type="button" class="pkg__details-toggle" @click="open = !open" :aria-expanded="open">
                                            <span x-text="open ? 'Ver menos' : 'Ver detalles'"></span>
                                            <i x-text="open ? '−' : '+'"></i>
                                        </button>
                                        <div x-show="open" x-collapse>
                                            <ul class="pkg__details-list">
                                                @foreach($detailFeatures as $feature)<li><span>✓</span>{{ $feature->title }}</li>@endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif

                                @if($package->renewal_enabled && $package->renewal_public_text)
                                    <p class="pkg__note">{{ $package->renewal_public_text }}</p>
                                @elseif($package->note)
                                    <p class="pkg__note">{{ $package->note }}</p>
                                @endif

                                <div class="pkg__action">
                                    @if($package->requires_quote)
                                        <button type="button" @click="open({{ $package->id }})" class="button button--navy button--full" data-package="{{ $package->slug }}" data-analytics="select_package">Cotizar <span>→</span></button>
                                    @elseif($package->direct_checkout)
                                        <a href="{{ route('checkout.show', $package) }}" class="button {{ $package->is_featured ? 'button--gold' : 'button--navy' }} button--full" data-package="{{ $package->slug }}" data-analytics="select_package">{{ $package->button_text ?? 'Contratar' }} <span>→</span></a>
                                    @else
                                        <a href="{{ route('checkout.show', $package) }}" class="button {{ $package->is_featured ? 'button--gold' : 'button--navy' }} button--full" data-package="{{ $package->slug }}" data-analytics="select_package">{{ $package->button_text ?? 'Contratar' }} <span>→</span></a>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="pricing__guide">
                        <div class="pricing__guide-content">
                            <div class="pricing__guide-text">
                                <h3>¿No sabes cuál paquete te conviene?</h3>
                                <p>Cuéntanos sobre tu negocio y te ayudamos a elegir la opción adecuada.</p>
                            </div>
                            <div class="pricing__guide-action">
                                <a href="{{ $waFinal }}" target="{{ $whatsapp ? '_blank' : '_self' }}" rel="noopener" class="pricing__guide-cta" data-analytics="contact_whatsapp">
                                    <span>Quiero orientación</span>
                                    <span class="pricing__guide-arrow">→</span>
                                </a>
                                <span class="pricing__guide-micro">Sin compromiso.</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($quotePackage)
                <div class="modal" x-cloak x-show="visible" x-transition.opacity @keydown.escape.window="close" role="dialog" aria-modal="true" aria-labelledby="quote-title">
                    <button class="modal__backdrop" @click="close" aria-label="Cerrar cotización"></button>
                    <div class="modal__panel" x-show="visible" x-transition>
                        <button type="button" class="modal__close" @click="close" aria-label="Cerrar">×</button>
                        <div class="section-kicker"><span>TIENDA</span> Cotización personalizada</div>
                        <h2 id="quote-title">Cuéntanos qué quieres vender.</h2>
                        <p>Revisamos tus productos, envíos e integraciones antes de darte una cifra definitiva.</p>
                        <form method="POST" action="{{ route('quote.store', $quotePackage) }}" class="quote-form">
                            @csrf
                            <div class="honeypot" aria-hidden="true"><label>Tu sitio web<input name="website" tabindex="-1" autocomplete="off"></label></div>
                            <label><span>Nombre</span><input name="name" required maxlength="100" autocomplete="name"></label>
                            <label><span>Correo</span><input name="email" type="email" required maxlength="160" autocomplete="email"></label>
                            <label><span>WhatsApp</span><input name="whatsapp" required maxlength="24" autocomplete="tel"></label>
                            <label><span>Nombre del negocio</span><input name="business_name" required maxlength="140" autocomplete="organization"></label>
                            <label class="field--full"><span>¿Qué necesitas?</span><textarea name="message" rows="3" maxlength="1200" placeholder="Productos, envíos o funciones especiales"></textarea></label>
                            <button class="button button--gold button--full" type="submit">Enviar y continuar <span>→</span></button>
                        </form>
                    </div>
                </div>
                @endif
            </section>

            <section id="proceso" class="process section--light">
                <div class="container process__grid">
                    <div class="process__intro">
                        <div class="section-kicker"><span>06</span> Así trabajamos</div>
                        <h2>De elegir tu paquete<br>a tener <em>tu sitio en línea.</em></h2>
                        <p>Un proceso acompañado, claro y sin tecnicismos innecesarios.</p>
                    </div>
                    <div class="process__timeline-wrapper">
                        <div class="process__progress-line" aria-hidden="true"></div>
                        <ol class="process__timeline">
                            @foreach([['Eliges tu paquete','Comparas opciones y eliges la que mejor se adapta a tu negocio.'],['Realizas tu pago','Confirmas tu paquete mediante un único pago.'],['Nos compartes la información','Te guiamos para reunir textos, imágenes y datos esenciales.'],['Diseñamos y desarrollamos','Convertimos tu información en una experiencia profesional.'],['Revisamos contigo','Afinamos el resultado antes de publicar.'],['Publicamos','Tu sitio queda en línea con los servicios incluidos.']] as $index => $step)
                                <li class="process-step" data-step="{{ $index + 1 }}">
                                    <div class="process-step__number">{{ str_pad($index+1, 2, '0', STR_PAD_LEFT) }}</div>
                                    <div class="process-step__content">
                                        <h3>{{ $step[0] }}</h3>
                                        <p>{{ $step[1] }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </section>

            <section id="preguntas" class="faq section--cream" x-data="{ open: 0 }">
                <div class="container faq__grid">
                    <div class="faq__intro">
                        <div class="section-kicker"><span>07</span> Preguntas frecuentes</div>
                        <h2>Antes de empezar,<br>hablemos claro.</h2>
                        <p>Resolvemos las dudas más comunes antes de comenzar. Si necesitas algo más específico, escríbenos y lo vemos contigo.</p>
                    </div>
                    <div class="faq__list">
                        @php($faqs = [
                            ['¿Qué necesito para comenzar?','Solo necesitamos la información básica de tu negocio, servicios, datos de contacto y las imágenes o contenido que quieras utilizar. Te guiamos durante el proceso para que sea sencillo.'],
                            ['¿Qué incluyen el sitio web?','Los paquetes incluyen dominio , hosting y certificado SSL durante el primer año. Si ya tienes dominio o alojamiento, podemos revisar cómo aprovecharlos. Después del primer año podemos ayudarte con la renovación correspondiente.'],
                            ['¿Cuánto tarda mi página?','El tiempo depende del paquete y de qué tan rápido tengamos el contenido necesario. Una landing sencilla puede estar lista en pocos días; proyectos más grandes requieren más tiempo. Antes de comenzar te indicamos un plazo aproximado.'],
                            ['¿Puedo solicitar cambios?','Sí. Durante el desarrollo revisamos el proyecto contigo antes de publicarlo. Los ajustes dentro del alcance contratado se revisan durante esta etapa. Cambios adicionales o nuevas funciones pueden cotizarse por separado.'],
                            ['¿Cómo funciona el pago?','Los paquetes estándar se contratan mediante un solo pago. Una vez confirmado, comenzamos el proceso de desarrollo. Los proyectos especiales o personalizados pueden manejar condiciones distintas según su alcance.'],
                            ['¿Puedo contratar si estoy fuera de México?','Sí. Podemos trabajar con clientes de otros países de forma remota. Para México manejamos las opciones de pago disponibles localmente y para clientes internacionales podemos utilizar métodos como PayPal según corresponda.'],
                            ['¿Cómo funciona una tienda en línea y podré administrarla?','Incluimos la configuración inicial y la carga acordada de productos. Después podrás administrar el catálogo y agregar nuevos productos desde tu panel. El alcance inicial depende de la cantidad de productos, variantes e integraciones que necesites.'],
                        ])
                        @foreach($faqs as $index => $faq)
                            <article class="faq-item" :class="{ 'is-open': open === {{ $index }} }">
                                <h3><button type="button" @click="open = open === {{ $index }} ? -1 : {{ $index }}" :aria-expanded="open === {{ $index }}" :aria-controls="'faq-answer-' + {{ $index }}"><span>{{ str_pad($index+1,2,'0',STR_PAD_LEFT) }}</span>{{ $faq[0] }}<i></i></button></h3>
                                <div class="faq-answer" id="faq-answer-{{ $index }}" x-cloak x-show="open === {{ $index }}" x-collapse><p>{{ $faq[1] }}</p></div>
                            </article>
                        @endforeach
                    </div>

                    <div class="faq__cta pricing__guide">
                        <div class="pricing__guide-content">
                            <div class="pricing__guide-text">
                                <h3>¿Te quedó alguna duda?</h3>
                                <p>Escríbenos y te respondemos directamente.</p>
                            </div>
                            <div class="pricing__guide-action">
                                <a href="{{ $waFinal }}" target="{{ $whatsapp ? '_blank' : '_self' }}" rel="noopener" class="pricing__guide-cta" data-analytics="contact_whatsapp">
                                    <span>Quiero orientación</span>
                                    <span class="pricing__guide-arrow">→</span>
                                </a>
                                <span class="pricing__guide-micro">Sin compromiso.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="contacto" class="final-cta section--dark">
                <div class="final-cta__orb" aria-hidden="true"></div>
                <div class="container final-cta__inner">
                    <div class="section-kicker section-kicker--light"><span>08</span> Tu siguiente paso</div>
                    <h2><span>Tu próximo cliente</span><span>puede estar buscándote</span><em>ahora.</em></h2>
                    <p>Haz que cuando te encuentre, vea un negocio profesional, confiable y fácil de contactar.</p>
                    <div class="final-cta__actions">
                        <a href="#paquetes" class="button button--gold button--lg" data-analytics="view_packages">Ver paquetes <span class="final-cta__arrow">→</span></a>
                        <a href="{{ $waFinal }}" target="{{ $whatsapp ? '_blank' : '_self' }}" rel="noopener" class="button button--outline button--lg" data-analytics="contact_whatsapp">Hablar por WhatsApp <span class="final-cta__arrow">↗</span></a>
                    </div>
                    <p class="final-cta__micro">Cuéntanos qué necesita tu negocio y te orientamos sin compromiso.</p>
                    <div class="final-cta__stamp"><x-brand /></div>
                </div>
            </section>
        </main>

        <footer class="footer">
            <div class="container footer__grid">
                <div class="footer__brand">
                    <x-brand :light="false" />
                    <p>Desarrollo web para negocios.</p>
                </div>
                <nav class="footer__nav" aria-label="Navegación del pie">
                    <b>Explorar</b>
                    <a href="#servicios">Servicios</a>
                    <a href="#proyectos">Proyectos</a>
                    <a href="#paquetes">Paquetes</a>
                    <a href="#contacto">Contacto</a>
                </nav>
                <nav class="footer__nav" aria-label="Información legal">
                    <b>Información</b>
                    <a href="{{ route('privacy') }}">Aviso de privacidad</a>
                    <a href="{{ route('terms') }}">Términos</a>
                    @if($contactEmail)<a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>@endif
                </nav>
                <div class="footer__contact">
                    <span class="footer__contact-eyebrow">¿Hablamos?</span>
                    <a href="{{ $waFinal }}" target="{{ $whatsapp ? '_blank' : '_self' }}" rel="noopener" class="footer__contact-link" data-analytics="contact_whatsapp" aria-label="Hablar por WhatsApp">
                        <svg class="footer__contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                        </svg>
                        <span>Hablar por WhatsApp</span>
                        <span class="footer__contact-arrow">→</span>
                    </a>
                    <p class="footer__contact-micro">Escríbenos y te respondemos personalmente.</p>
                </div>
            </div>
            <div class="container footer__bottom">
                <span>© {{ date('Y') }} XpertSystems</span>
                <span>Hecho en México · Para cualquier lugar</span>
                <a href="#inicio" class="footer__back-top">Volver arriba <span>↑</span></a>
            </div>
        </footer>

        @if($whatsapp)
            <a href="{{ $waFinal }}" target="_blank" rel="noopener" class="floating-wa" aria-label="Hablar por WhatsApp" data-analytics="contact_whatsapp"><span>WA</span><i></i></a>
        @endif
    </div>
</x-layouts.app>
