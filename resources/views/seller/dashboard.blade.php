<x-layouts.admin title="Mi dashboard">
    <section class="admin-dashboard-head">
        <div>
            <span class="admin-eyebrow">Tu espacio</span>
            <h1>Hola, {{ $user->first_name ?? $user->name }}.</h1>
            <p>Consulta tu rendimiento comercial y tu enlace de referido.</p>
        </div>
        <div class="admin-dashboard-head__stamp">
            <span>{{ now()->format('m / Y') }}</span>
            <small>Actualizado ahora</small>
        </div>
    </section>

    @if($referralUrl)
        <section class="admin-referral-card" x-data="{ copied: false }">
            <div>
                <span>Tu código de referido</span>
                <strong>{{ $referralCode }}</strong>
            </div>
            <div>
                <span>Tu enlace</span>
                <strong style="color: rgba(255,255,255,.7); font-size: .68rem;">{{ $referralUrl }}</strong>
            </div>
            <button type="button" @click="navigator.clipboard.writeText(@js($referralUrl)); copied = true; setTimeout(() => copied = false, 1800)">
                <span x-text="copied ? 'Copiado' : 'Copiar enlace'"></span> ↗
            </button>
        </section>
    @endif

    <section class="admin-metrics" aria-label="Indicadores de rendimiento">
        @php
            $sellerMetrics = [
                ['label' => 'Ventas', 'value' => $metrics['sales'], 'kind' => 'number', 'note' => 'Atribuidas a ti'],
                ['label' => 'Clientes', 'value' => $metrics['customers'], 'kind' => 'number', 'note' => 'Únicos'],
                ['label' => 'Comisión generada', 'value' => $metrics['commissions_generated'], 'kind' => 'money', 'note' => 'Histórico'],
                ['label' => 'Pendiente', 'value' => $metrics['commissions_pending'], 'kind' => 'money', 'note' => 'Por liquidar'],
            ];
        @endphp
        @foreach($sellerMetrics as $index => $metric)
            <article class="admin-metric {{ $index === 2 ? 'admin-metric--featured' : '' }}">
                <div class="admin-metric__top"><span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><i></i></div>
                <p>{{ $metric['label'] }}</p>
                <strong>
                    @if($metric['kind'] === 'money')
                        <small>$</small>{{ number_format((float) $metric['value'], 0) }}<em>MXN</em>
                    @else
                        {{ number_format((float) $metric['value'], 0) }}
                    @endif
                </strong>
                <span class="admin-metric__note">{{ $metric['note'] }}</span>
            </article>
        @endforeach
    </section>
</x-layouts.admin>
