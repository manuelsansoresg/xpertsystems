@php
    $content = match ($state) {
        'success' => [
            'label' => 'Pago confirmado',
            'title' => 'Tu proyecto ya está en marcha.',
            'message' => 'Recibimos correctamente el pago total de $'.number_format((float) $order->total_amount, 0).' MXN por el primer año del plan '.$order->package->name.'.',
            'class' => 'return-card--success',
        ],
        'failure' => [
            'label' => 'Pago no completado',
            'title' => 'El pago no pudo completarse.',
            'message' => 'Mercado Pago no confirmó el cobro. Tus datos quedaron guardados para que podamos ayudarte a continuar.',
            'class' => 'return-card--failure',
        ],
        default => [
            'label' => 'Pago en revisión',
            'title' => 'Estamos verificando tu pago.',
            'message' => 'Mercado Pago todavía no confirma el cobro. No necesitas llenar el formulario otra vez.',
            'class' => 'return-card--pending',
        ],
    };
    $waUrl = $whatsapp ? 'https://wa.me/'.$whatsapp.'?text='.rawurlencode("Hola, necesito ayuda con el pago de la orden {$order->reference}.") : route('home').'#contacto';
@endphp
<x-layouts.app :title="$content['label'].' — XpertSystems'" description="Estado del pago de tu proyecto con XpertSystems.">
    <div class="checkout-page">
        <header class="checkout-header"><div class="container checkout-header__inner"><a href="{{ route('home') }}"><x-brand /></a><a href="{{ route('home') }}">Ir al inicio</a></div></header>
        <main id="contenido" class="return-card {{ $content['class'] }}">
            <div class="return-card__signal" aria-hidden="true"><span></span></div>
            <span class="return-status">{{ $content['label'] }}</span>
            <h1>{{ $content['title'] }}</h1>
            <p>{{ $content['message'] }}</p>

            <section class="return-contact" aria-labelledby="next-step-title">
                <span class="return-contact__eyebrow">Siguiente paso</span>
                <h2 id="next-step-title">Un asesor se comunicará contigo.</h2>
                <p>Usaremos los datos que nos dejaste para darte seguimiento y explicarte cómo continuamos con tu proyecto.</p>
                <dl>
                    <div><dt>Nombre</dt><dd>{{ $order->customer_name }}</dd></div>
                    <div><dt>Correo</dt><dd>{{ $order->customer_email }}</dd></div>
                    <div><dt>WhatsApp</dt><dd>{{ $order->customer_whatsapp }}</dd></div>
                    <div><dt>Referencia</dt><dd>{{ $order->reference }}</dd></div>
                </dl>
            </section>

            @if($state === 'success')
                <script>window.addEventListener('load',()=>{if(typeof gtag==='function')gtag('event','purchase',{transaction_id:'{{ $order->reference }}',value:{{ (float)$order->total_amount }},currency:'{{ $order->currency }}'})})</script>
            @endif

            <div class="return-actions">
                @if($state === 'failure')
                    <a class="button button--gold" href="{{ route('checkout.show', $order->package) }}">Intentar de nuevo <span>→</span></a>
                @endif
                <a class="button button--navy" href="{{ $waUrl }}" target="{{ $whatsapp ? '_blank' : '_self' }}" rel="noopener">Hablar con un asesor <span>↗</span></a>
                <a class="return-actions__home" href="{{ route('home') }}">Volver al inicio</a>
            </div>
        </main>
    </div>
</x-layouts.app>
