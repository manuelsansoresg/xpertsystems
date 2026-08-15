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

                    <div class="hero-stage" aria-label="Muestra de una experiencia web en computadora y teléfono">
                        <div class="hero-stage__halo" aria-hidden="true"></div>
                        <div class="browser browser--primary">
                            <div class="browser__bar"><i></i><i></i><i></i><span>xpertsystems / presencia digital</span></div>
                            <div class="browser__content browser__content--main">
                                <div class="mock-nav"><b>MARCA</b><em>PROYECTO DIGITAL</em></div>
                                <div class="mock-hero">
                                    <small>ESTUDIO / 2026</small>
                                    <strong><span>Una presencia digital</span><em>a la altura de tu negocio.</em></strong>
                                    <i></i>
                                </div>
                                <div class="mock-footer"><span>DISEÑO A MEDIDA</span><span>RESPONSIVE</span><span>CONVERSIÓN</span></div>
                            </div>
                        </div>
                        <div class="phone"><div class="phone__notch"></div><div class="phone__screen"><span>XS</span><small>RESPONSIVE</small><b>Tu sitio.<br><em>Impecable.</em></b><i>Explorar →</i></div></div>
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
                    <div class="section-kicker"><span>02</span> El problema</div>
                    <div class="problem__question">
                        <h2 class="split-title">¿Tu negocio depende solamente de <em>Facebook o Instagram?</em></h2>
                        <p class="problem__lead">Las redes ayudan a encontrarte. Pero no deberían ser el único lugar donde tus clientes intentan entenderte.</p>
                    </div>
                    <div class="problem__signal" aria-hidden="true">
                        <div class="signal__ring"><span class="signal__value" data-count="24">0</span><small>/ 7</small></div>
                        <p>Tu sitio sigue trabajando<br>cuando tú no estás conectado.</p>
                    </div>
                    <ol class="problem__list">
                        <li><span>01</span><p>La información importante se pierde entre publicaciones.</p></li>
                        <li><span>02</span><p>El cliente no encuentra todos tus servicios con claridad.</p></li>
                        <li><span>03</span><p>Dependes de un algoritmo y de una plataforma que no controlas.</p></li>
                        <li><span>04</span><p>Sin un espacio propio, transmitir confianza cuesta más.</p></li>
                    </ol>
                    <blockquote>Tu página debe trabajar para tu negocio incluso cuando tú no estás conectado.</blockquote>
                </div>
            </section>

            <section class="solution section--light">
                <div class="container solution__grid">
                    <div class="solution__visual reveal-clip">
                        <div class="blueprint-grid"></div>
                        <span class="solution__number">+1</span>
                        <div class="solution__window">
                            <small>XPERTSYSTEMS / MÉTODO</small>
                            <div class="solution__diagram"><span>VISITA</span><i>→</i><span>CONFIANZA</span><i>→</i><strong>CONTACTO</strong></div>
                        </div>
                    </div>
                    <div class="solution__copy">
                        <div class="section-kicker"><span>03</span> La solución</div>
                        <h2 class="split-title">No hacemos sitios únicamente para que <em>“se vean bonitos”.</em></h2>
                        <p>Diseñamos una ruta clara para que cada visita entienda, confíe y dé el siguiente paso.</p>
                        <div class="solution__outcomes">
                            @foreach(['Confianza','Contacto','Prospectos','Ventas','Presencia profesional','Claridad','Experiencia móvil'] as $index => $outcome)
                                <span><b>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</b>{{ $outcome }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section id="proyectos" class="portfolio section--dark" x-data="portfolioShowcase(@js($projects->map(fn($p) => ['name'=>$p->name,'slug'=>$p->slug,'category'=>$p->category,'description'=>$p->description,'desktop_image'=>$p->desktop_image,'mobile_image'=>$p->mobile_image,'url'=>$p->url,'accent'=>$p->accent])))">
                <div class="container">
                    <div class="portfolio__intro">
                        <div class="section-kicker section-kicker--light"><span>04</span> Trabajo seleccionado</div>
                        <h2>Proyectos con una idea clara detrás.</h2>
                        <p>Diseño pensado para el negocio, no para llenar una plantilla.</p>
                    </div>
                    <div class="portfolio__showcase">
                        <div class="portfolio__rail" role="tablist" aria-label="Proyectos">
                            <template x-for="(project, index) in projects" :key="project.slug">
                                <button type="button" role="tab" :aria-selected="active === index" :class="{ 'is-active': active === index }" @mouseenter="active=index" @focus="active=index" @click="active=index">
                                    <span x-text="String(index + 1).padStart(2,'0')"></span><strong x-text="project.name"></strong><i>↗</i>
                                </button>
                            </template>
                        </div>
                        <div class="portfolio__stage" :style="`--project-accent:${current.accent}`">
                            <div class="project-meta"><span x-text="String(active + 1).padStart(2,'0') + ' / ' + String(projects.length).padStart(2,'0')"></span><small x-text="current.category || 'Proyecto web' "></small></div>
                            <div class="project-canvas">
                                <div class="project-browser">
                                    <div class="browser__bar"><i></i><i></i><i></i><span x-text="current.slug + '.com'"></span></div>
                                    <template x-if="current.desktop_image"><img :src="current.desktop_image" :alt="`Vista de ${current.name}`"></template>
                                    <template x-if="!current.desktop_image"><div class="project-art"><small>PROYECTO DIGITAL</small><b x-text="current.name"></b><span>Diseño con intención<br>y una dirección propia.</span><em>Descubrir →</em></div></template>
                                </div>
                                <div class="project-phone"><div><small>XS / MOBILE</small><b x-text="current.name"></b><span></span><span></span><i>Explorar →</i></div></div>
                            </div>
                            <div class="project-caption"><h3 x-text="current.name"></h3><p x-text="current.description || 'Identidad, estructura y experiencia alineadas en una presencia digital clara.'"></p><a x-show="current.url" :href="current.url" target="_blank" rel="noopener">Visitar proyecto ↗</a></div>
                            <div class="portfolio__mobile-controls"><button @click="previous" aria-label="Proyecto anterior">←</button><span x-text="`${active+1} / ${projects.length}`"></span><button @click="next" aria-label="Proyecto siguiente">→</button></div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="paquetes" class="pricing section--cream" x-data="quoteModal">
                <div class="container">
                    <div class="pricing__intro">
                        <div class="section-kicker"><span>05</span> Elige cómo empezar</div>
                        <h2>Una inversión clara.<br><em>Sin letras pequeñas.</em></h2>
                        <p>Todos los paquetes incluyen la base técnica para publicar con confianza desde el primer día.</p>
                    </div>
                    <div class="pricing__composition">
                        @foreach($packages as $package)
                            <article class="package package--{{ $package->featured ? 'featured' : ($loop->first ? 'starter' : 'store') }} package-reveal">
                                <header>
                                    <div class="package__topline"><span>0{{ $loop->iteration }}</span>@if($package->badge)<b>{{ $package->badge }}</b>@endif</div>
                                    <h3>{{ $package->name }}</h3>
                                    <p>{{ $package->short_description }}</p>
                                    <div class="package__price">@if($package->price_type === 'starting_at')<small>Desde</small>@endif <strong>${{ number_format((float)$package->price, 0) }}</strong><span>MXN</span></div>
                                    @if($package->direct_checkout)
                                        <div class="package__split"><span>Anticipo <b>${{ number_format($package->deposit_amount, 0) }}</b></span><i></i><span>Al finalizar <b>${{ number_format((float)$package->price - $package->deposit_amount, 0) }}</b></span></div>
                                    @endif
                                </header>
                                <div class="package__features">
                                    <button type="button" class="package__features-toggle" @click="$el.parentElement.classList.toggle('is-open')">Lo que incluye <span>+</span></button>
                                    <ul>
                                        @foreach($package->features as $feature)<li><span>✓</span>{{ $feature }}</li>@endforeach
                                    </ul>
                                    @if($package->note)<p class="package__note">{{ $package->note }}</p>@endif
                                </div>
                                @if($package->direct_checkout)
                                    <a href="{{ route('checkout.show', $package) }}" class="button {{ $package->featured ? 'button--gold' : 'button--navy' }} button--full" data-package="{{ $package->slug }}" data-analytics="select_package">Elegir {{ $package->featured ? 'Profesional' : 'Landing' }} <span>→</span></a>
                                @else
                                    <button type="button" @click="open({{ $package->id }})" class="button button--navy button--full" data-package="{{ $package->slug }}" data-analytics="select_package">Cotizar tienda <span>→</span></button>
                                @endif
                            </article>
                        @endforeach
                    </div>
                    <div class="pricing__help"><span>¿No sabes cuál te conviene?</span><a href="{{ $waFinal }}" target="{{ $whatsapp ? '_blank' : '_self' }}" rel="noopener" data-analytics="contact_whatsapp">Cuéntanos sobre tu negocio →</a></div>
                </div>

                <div class="modal" x-cloak x-show="visible" x-transition.opacity @keydown.escape.window="close" role="dialog" aria-modal="true" aria-labelledby="quote-title">
                    <button class="modal__backdrop" @click="close" aria-label="Cerrar cotización"></button>
                    <div class="modal__panel" x-show="visible" x-transition>
                        <button type="button" class="modal__close" @click="close" aria-label="Cerrar">×</button>
                        <div class="section-kicker"><span>TIENDA</span> Cotización personalizada</div>
                        <h2 id="quote-title">Cuéntanos qué quieres vender.</h2>
                        <p>Revisamos tus productos, envíos e integraciones antes de darte una cifra definitiva.</p>
                        <form method="POST" action="{{ route('quote.store', $packages->firstWhere('requires_quote', true)) }}" class="quote-form">
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
            </section>

            <section id="proceso" class="process section--light">
                <div class="container process__grid">
                    <div class="process__sticky">
                        <div class="section-kicker"><span>06</span> Así trabajamos</div>
                        <h2>De la decisión a una página lista para trabajar.</h2>
                        <p>Un proceso acompañado, sin tecnicismos innecesarios y con claridad en cada etapa.</p>
                    </div>
                    <ol class="process__timeline">
                        @foreach([['Eliges tu paquete','Comparas con calma y eliges el alcance que tu negocio necesita.'],['Realizas el anticipo','Reservamos tu proyecto con el 50% del total.'],['Nos compartes la información','Te guiamos para reunir textos, imágenes y datos esenciales.'],['Diseñamos y desarrollamos','Convertimos tu información en una experiencia clara y profesional.'],['Revisamos contigo','Afinamos el resultado contigo antes de publicar.'],['Publicamos','Tu sitio queda en línea con dominio, hosting y SSL.']] as $index => $step)
                            <li class="process-step"><div class="process-step__number">{{ str_pad($index+1, 2, '0', STR_PAD_LEFT) }}</div><div><h3>{{ $step[0] }}</h3><p>{{ $step[1] }}</p></div><span aria-hidden="true">↘</span></li>
                        @endforeach
                    </ol>
                </div>
            </section>

            <section id="preguntas" class="faq section--cream" x-data="{ open: 0 }">
                <div class="container faq__grid">
                    <div class="faq__intro"><div class="section-kicker"><span>07</span> Preguntas frecuentes</div><h2>Antes de empezar,<br>hablemos claro.</h2><p>Si algo no aparece aquí, escríbenos. Te respondemos como personas, no como un sistema automático.</p></div>
                    <div class="faq__list">
                        @php($faqs = [
                            ['¿Qué necesito para comenzar?','La información básica de tu negocio, tus servicios, datos de contacto y las imágenes que quieras usar. Te guiamos con una lista sencilla.'],
                            ['¿Tengo que tener dominio?','No. Podemos registrar el dominio .com incluido en tu paquete. Si ya tienes uno, también podemos trabajar con él.'],
                            ['¿El dominio está incluido?','Sí, un dominio .com está incluido durante el primer año en los tres paquetes.'],
                            ['¿El hosting está incluido?','Sí. Incluimos hosting y certificado SSL durante el primer año.'],
                            ['¿Qué incluye el soporte?','Atendemos problemas técnicos atribuibles al sitio durante un año. Nuevas secciones, cambios de alcance o contenido recurrente se cotizan por separado.'],
                            ['¿Qué pasa después del primer año?','Te avisamos con anticipación el costo de renovación de dominio y hosting. Tu sitio no se renueva sin tu autorización.'],
                            ['¿Cuánto tarda mi página?','Depende del alcance y de qué tan rápido reunamos la información. Una landing suele tomar menos tiempo que un sitio profesional o una tienda. Confirmamos el calendario antes de comenzar.'],
                            ['¿Puedo solicitar cambios?','Sí. El proceso incluye una etapa de revisión contigo antes de publicar.'],
                            ['¿Cómo funciona el anticipo?','Pagas el 50% para reservar e iniciar el proyecto. El 50% restante se liquida cuando terminamos, antes de publicar.'],
                            ['¿Puedo contratar si estoy fuera de México?','Sí. Trabajamos a distancia y puedes elegir “Otro país” durante el checkout.'],
                            ['¿Cómo pago desde otro país?','El anticipo se procesa mediante PayPal. El resumen siempre te muestra el monto antes de continuar.'],
                            ['¿La tienda puede tener muchos productos?','Sí. El catálogo es administrable y no presentamos la tienda como un sistema con un límite fijo de productos.'],
                            ['¿Podré subir productos por mi cuenta?','Sí. Incluimos una carga inicial y capacitación básica para que después agregues productos desde tu panel.'],
                        ])
                        @foreach($faqs as $index => $faq)
                            <article class="faq-item" :class="{ 'is-open': open === {{ $index }} }">
                                <h3><button type="button" @click="open = open === {{ $index }} ? -1 : {{ $index }}" :aria-expanded="open === {{ $index }}"><span>{{ str_pad($index+1,2,'0',STR_PAD_LEFT) }}</span>{{ $faq[0] }}<i></i></button></h3>
                                <div class="faq-answer" x-cloak x-show="open === {{ $index }}" x-collapse><p>{{ $faq[1] }}</p></div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="contacto" class="final-cta section--dark">
                <div class="final-cta__orb" aria-hidden="true"></div>
                <div class="container final-cta__inner">
                    <div class="section-kicker section-kicker--light"><span>08</span> Tu siguiente paso</div>
                    <h2><span>Tu próximo cliente</span><span>puede estar buscándote</span><em>ahora.</em></h2>
                    <p>Haz que encuentre un negocio que se vea profesional y le dé confianza para contactarte.</p>
                    <div class="final-cta__actions"><a href="#paquetes" class="button button--gold" data-analytics="view_packages">Ver paquetes <span>↘</span></a><a href="{{ $waFinal }}" target="{{ $whatsapp ? '_blank' : '_self' }}" rel="noopener" class="button button--outline" data-analytics="contact_whatsapp">Hablar por WhatsApp <span>↗</span></a></div>
                    <div class="final-cta__stamp"><x-brand /></div>
                </div>
            </section>
        </main>

        <footer class="footer">
            <div class="container footer__grid">
                <div><x-brand :light="false" /><p>Desarrollo web para negocios.</p></div>
                <nav aria-label="Navegación del pie"><b>Explorar</b><a href="#servicios">Servicios</a><a href="#proyectos">Proyectos</a><a href="#paquetes">Paquetes</a><a href="#contacto">Contacto</a></nav>
                <nav aria-label="Información legal"><b>Información</b><a href="{{ route('privacy') }}">Aviso de privacidad</a><a href="{{ route('terms') }}">Términos</a>@if($contactEmail)<a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>@endif</nav>
                <div class="footer__cta"><b>¿Hablamos?</b><a href="{{ $waFinal }}" target="{{ $whatsapp ? '_blank' : '_self' }}" rel="noopener" data-analytics="contact_whatsapp">WhatsApp ↗</a></div>
            </div>
            <div class="container footer__bottom"><span>© {{ date('Y') }} XpertSystems</span><span>Hecho en México · Para cualquier lugar</span><a href="#inicio">Volver arriba ↑</a></div>
        </footer>

        @if($whatsapp)
            <a href="{{ $waFinal }}" target="_blank" rel="noopener" class="floating-wa" aria-label="Hablar por WhatsApp" data-analytics="contact_whatsapp"><span>WA</span><i></i></a>
        @endif
    </div>
</x-layouts.app>
