<x-layouts.app
    :title="'Contratar '.$package->name.' — XpertSystems'"
    description="Completa tus datos y realiza el pago único para comenzar tu proyecto con XpertSystems."
>
    @php($waUrl = $whatsapp ? 'https://wa.me/'.$whatsapp.'?text='.rawurlencode("Hola, vi el paquete {$package->name} de XpertSystems y quisiera resolver una duda antes de contratar.") : route('home').'#contacto')
    <div class="checkout-page">
        <header class="checkout-header"><div class="container checkout-header__inner"><a href="{{ route('home') }}"><x-brand /></a><a href="{{ route('home') }}#paquetes">← Volver a paquetes</a></div></header>
        <main class="checkout">
            <div class="container">
                <div class="checkout__intro">
                    <div class="section-kicker"><span>CHECKOUT</span> Comencemos</div>
                    <h1>Tu proyecto empieza con información clara.</h1>
                    <p>Completa tus datos, confirma desde dónde contratas y revisa el resumen antes de ir a la plataforma de pago.</p>
                </div>
                <div class="checkout__grid">
                    <form method="POST" action="{{ route('checkout.store', $package) }}" class="checkout-form" data-checkout-form>
                        @csrf
                        <div class="honeypot" aria-hidden="true"><label>Tu sitio web<input name="website" tabindex="-1" autocomplete="off"></label></div>
                        @if(session('payment_error'))<div class="form-alert">{{ session('payment_error') }}</div>@endif
                        <label><span>Nombre completo</span><input name="name" value="{{ old('name') }}" required maxlength="100" autocomplete="name">@error('name')<small class="form-error">{{ $message }}</small>@enderror</label>
                        <label><span>Correo</span><input name="email" type="email" value="{{ old('email') }}" required maxlength="160" autocomplete="email">@error('email')<small class="form-error">{{ $message }}</small>@enderror</label>
                        <label><span>WhatsApp</span><input name="whatsapp" value="{{ old('whatsapp') }}" required maxlength="24" autocomplete="tel" inputmode="tel">@error('whatsapp')<small class="form-error">{{ $message }}</small>@enderror</label>
                        <label><span>Nombre del negocio</span><input name="business_name" value="{{ old('business_name') }}" required maxlength="140" autocomplete="organization">@error('business_name')<small class="form-error">{{ $message }}</small>@enderror</label>
                        <fieldset>
                            <legend>¿Desde dónde estás contratando?</legend>
                            <div class="country-choice">
                                <label><input type="radio" name="country" value="MX" {{ old('country','MX') === 'MX' ? 'checked' : '' }}> México <small>Mercado Pago</small></label>
                                <label><input type="radio" name="country" value="OTHER" {{ old('country') === 'OTHER' ? 'checked' : '' }}> Otro país <small>PayPal</small></label>
                            </div>
                            @error('country')<small class="form-error">{{ $message }}</small>@enderror
                        </fieldset>
                        <label class="terms-check"><input name="terms" type="checkbox" value="1" required {{ old('terms') ? 'checked' : '' }}><span>Acepto el <a href="{{ route('privacy') }}" target="_blank">aviso de privacidad</a> y los <a href="{{ route('terms') }}" target="_blank">términos del servicio</a>.</span></label>
                        @error('terms')<small class="form-error">{{ $message }}</small>@enderror
                        <div class="checkout-submit">
                            <button type="submit" class="button button--gold button--full" data-analytics="begin_checkout" data-package="{{ $package->slug }}">Pagar ahora <span>→</span></button>
                            <a class="order-summary__help" href="{{ $waUrl }}" target="{{ $whatsapp ? '_blank' : '_self' }}" rel="noopener" data-analytics="contact_whatsapp">¿Tienes dudas? Hablar por WhatsApp ↗</a>
                        </div>
                    </form>
                    <aside class="order-summary" aria-label="Resumen de la orden">
                        <span class="order-summary__eyebrow">Resumen del proyecto</span>
                        <h2>{{ $package->name }}</h2>
                        <div class="summary-row"><span>Pago único</span><strong>${{ number_format((float)$package->price, 0) }} MXN</strong></div>
                        @if($package->renewal_enabled && $package->renewal_price)
                        <div class="summary-row"><span>Renovación anual</span><strong>${{ number_format((float)$package->renewal_price, 0) }} MXN</strong></div>
                        @endif
                        <p class="order-summary__note">El monto se calcula en nuestro servidor. No almacenamos datos de tarjeta: el pago se completa en Mercado Pago o PayPal, según tu país.</p>
                    </aside>
                </div>
            </div>
        </main>
    </div>
</x-layouts.app>
