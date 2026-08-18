<x-layouts.app :title="'Confirmación '.$order->reference.' — XpertSystems'" description="Estado del pago de tu proyecto con XpertSystems.">
    <div class="checkout-page">
        <header class="checkout-header"><div class="container checkout-header__inner"><a href="{{ route('home') }}"><x-brand /></a><a href="{{ route('home') }}">Ir al inicio</a></div></header>
        <main class="return-card">
            @if($order->status === \App\Enums\OrderStatus::DepositPaid)
                <span class="return-status">Pago confirmado</span>
                <h1>Gracias. Tu proyecto ya tiene un primer paso firme.</h1>
                <p>Recibimos el pago de <strong>${{ number_format((float)$order->total_amount, 0) }} MXN</strong> para {{ $order->package->name }}. Te contactaremos con los siguientes pasos.</p>
                <script>window.addEventListener('load',()=>{if(typeof gtag==='function')gtag('event','purchase',{transaction_id:'{{ $order->reference }}',value:{{ (float)$order->deposit_amount }},currency:'{{ $order->currency }}'})})</script>
            @else
                <span class="return-status">Confirmación en proceso</span>
                <h1>Estamos verificando tu pago.</h1>
                <p>La plataforma de pago todavía no confirma el pago. Conserva esta referencia: <strong>{{ $order->reference }}</strong>. Actualizaremos el estado cuando recibamos la confirmación segura.</p>
            @endif
            <p><a class="button button--navy" href="{{ route('home') }}">Volver al sitio <span>→</span></a></p>
        </main>
    </div>
</x-layouts.app>
