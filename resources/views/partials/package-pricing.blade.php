@php
    $checkoutSource = $checkoutSource ?? 'home';
    $checkoutOptions = $packages->where('direct_checkout', true)->mapWithKeys(fn ($package) => [
        $package->slug => [
            'action' => route('checkout.store', $package),
            'name' => $package->name,
            'price' => '$'.number_format((float) $package->price, 0).' MXN',
        ],
    ])->all();
    $packageWaBase = $whatsapp ? "https://wa.me/{$whatsapp}?text=" : '#contacto-directo';
    $packageWaFinal = $whatsapp
        ? $packageWaBase.rawurlencode('Hola, quiero comenzar mi página web con XpertSystems. ¿Me ayudan a elegir el paquete?')
        : '#contacto-directo';
@endphp

<section id="paquetes" class="pricing section--cream" x-data="checkoutModal(@js($checkoutOptions), @js(old('package_slug', request()->query('checkout'))))">
    <div class="container">
        <header class="pricing__header">
            <div class="section-kicker"><span>05</span> Elige cómo empezar</div>
            <h2>Tres formas de empezar.<br><em>Una meta: hacer crecer tu presencia digital.</em></h2>
            <p>Todos los paquetes incluyen una base técnica sólida para publicar con confianza desde el primer día.</p>
        </header>

        <div class="pricing__grid">
            @foreach($packages as $package)
                @php($summaryFeatures = $package->featureItems->where('visible_summary', true)->where('active', true))

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
                                <strong>${{ number_format((float) $package->price, 0) }}</strong>
                                <span class="pkg__currency">MXN</span>
                            @endif
                        </div>
                        @if($package->price_type !== 'quote' && $package->direct_checkout)
                            <div class="pkg__payment-type"><span>PAGO ÚNICO</span></div>
                        @endif
                    </div>

                    <ul class="pkg__features">
                        @foreach($summaryFeatures as $feature)<li><span>✓</span>{{ $feature->title }}</li>@endforeach
                    </ul>

                    @if($package->renewal_enabled && $package->renewal_public_text)
                        <p class="pkg__note">{{ $package->renewal_public_text }}</p>
                    @elseif($package->note)
                        <p class="pkg__note">{{ $package->note }}</p>
                    @endif

                    <div class="pkg__action">
                        @if($package->requires_quote)
                            @php($packageWa = $whatsapp ? $packageWaBase.rawurlencode("Hola, quiero cotizar el paquete {$package->name} con XpertSystems.") : '#contacto-directo')
                            <a href="{{ $packageWa }}" target="{{ $whatsapp ? '_blank' : '_self' }}" rel="noopener" class="button button--navy button--full" data-package="{{ $package->slug }}" data-analytics="contact_whatsapp">{{ $package->button_text ?? 'Cotizar' }} <span>→</span></a>
                        @elseif($package->direct_checkout)
                            <button type="button" @click="openCheckout('{{ $package->slug }}')" class="button {{ $package->is_featured ? 'button--gold' : 'button--navy' }} button--full" data-package="{{ $package->slug }}" data-analytics="select_package">{{ $package->button_text ?? 'Contratar' }} <span>→</span></button>
                        @else
                            <a href="{{ route('contacto') }}" class="button {{ $package->is_featured ? 'button--gold' : 'button--navy' }} button--full" data-package="{{ $package->slug }}">{{ $package->button_text ?? 'Consultar' }} <span>→</span></a>
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
                    <a href="{{ $packageWaFinal }}" target="{{ $whatsapp ? '_blank' : '_self' }}" rel="noopener" class="pricing__guide-cta" data-analytics="contact_whatsapp">
                        <span>Quiero orientación</span>
                        <span class="pricing__guide-arrow">→</span>
                    </a>
                    <span class="pricing__guide-micro">Sin compromiso.</span>
                </div>
            </div>
        </div>
    </div>

    @if($checkoutOptions)
        <div class="modal" x-cloak x-show="visible" x-transition.opacity @keydown.escape.window="close" role="dialog" aria-modal="true" aria-labelledby="checkout-title">
            <button type="button" class="modal__backdrop" @click="close" aria-label="Cerrar checkout"></button>
            <div class="modal__panel" x-show="visible" x-transition>
                <button type="button" class="modal__close" @click="close" aria-label="Cerrar">×</button>
                <div class="section-kicker"><span>CHECKOUT</span> Pago seguro</div>
                <h2 id="checkout-title">Completa tus datos para contratar <span x-text="packageName"></span>.</h2>
                <p>Importe del paquete: <strong x-text="packagePrice"></strong>. Al continuar irás directamente a Mercado Pago.</p>
                <form method="POST" :action="action" class="quote-form" data-analytics-submit="form_submit" :data-package="packageSlug">
                    @csrf
                    <div class="honeypot" aria-hidden="true"><label>Tu sitio web<input name="website" tabindex="-1" autocomplete="off"></label></div>
                    @if(session('payment_error'))<div class="form-alert field--full" role="alert">{{ session('payment_error') }}</div>@endif
                    <label><span>Nombre completo</span><input name="name" value="{{ old('name') }}" required maxlength="100" autocomplete="name">@error('name')<small class="form-error" role="alert">{{ $message }}</small>@enderror</label>
                    <label><span>Correo</span><input name="email" type="email" value="{{ old('email') }}" required maxlength="160" autocomplete="email">@error('email')<small class="form-error" role="alert">{{ $message }}</small>@enderror</label>
                    <label><span>WhatsApp</span><input name="whatsapp" value="{{ old('whatsapp') }}" required maxlength="24" autocomplete="tel" inputmode="tel">@error('whatsapp')<small class="form-error" role="alert">{{ $message }}</small>@enderror</label>
                    <label><span>Nombre del negocio <small>(opcional)</small></span><input name="business_name" value="{{ old('business_name') }}" maxlength="140" autocomplete="organization">@error('business_name')<small class="form-error" role="alert">{{ $message }}</small>@enderror</label>
                    <input type="hidden" name="country" value="MX">
                    <input type="hidden" name="package_slug" :value="packageSlug">
                    <input type="hidden" name="checkout_source" value="{{ $checkoutSource }}">
                    <label class="terms-check field--full"><input name="terms" type="checkbox" value="1" required {{ old('terms') ? 'checked' : '' }}><span>Acepto el <a href="{{ route('privacy') }}" target="_blank">aviso de privacidad</a> y los <a href="{{ route('terms') }}" target="_blank">términos del servicio</a>.</span></label>
                    @error('terms')<small class="form-error field--full" role="alert">{{ $message }}</small>@enderror
                    <button class="button button--gold button--full field--full" type="submit" data-analytics="begin_checkout">Continuar a Mercado Pago <span>→</span></button>
                </form>
            </div>
        </div>
    @endif
</section>
