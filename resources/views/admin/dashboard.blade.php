<x-layouts.admin :title="$mode === 'admin' ? 'Dashboard general' : 'Mi dashboard'">
    <section class="admin-dashboard-head">
        <div>
            <span class="admin-eyebrow">{{ $mode === 'admin' ? 'Visión comercial' : 'Rendimiento personal' }}</span>
            <h1>{{ $mode === 'admin' ? 'El negocio, en perspectiva.' : 'Tu trabajo, en números.' }}</h1>
            <p>{{ $mode === 'admin' ? 'Una lectura rápida de ventas, cobros y operación comercial.' : 'Consulta tus ventas atribuidas y el estado de tus comisiones.' }}</p>
        </div>
        <div class="admin-dashboard-head__stamp">
            <span>{{ now()->format('m / Y') }}</span>
            <small>Actualizado ahora</small>
        </div>
    </section>

    @if($mode === 'seller' && $referralUrl)
        <section class="admin-referral-card" x-data="{ copied: false }">
            <div><span>Tu enlace de referido</span><strong>{{ $referralUrl }}</strong></div>
            <button type="button" @click="navigator.clipboard.writeText(@js($referralUrl)); copied = true; setTimeout(() => copied = false, 1800)">
                <span x-text="copied ? 'Copiado' : 'Copiar enlace'"></span> ↗
            </button>
        </section>
    @endif

    <section class="admin-metrics" aria-label="Indicadores principales">
        @foreach($metrics as $index => $metric)
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

    @if($mode === 'admin')
        <section class="admin-dashboard-grid">
            <article class="admin-panel admin-panel--chart">
                <header class="admin-panel__header">
                    <div><span>Últimos 6 meses</span><h2>Valor de ventas</h2></div>
                    <span class="admin-panel__legend"><i></i>Monto registrado</span>
                </header>
                <div class="admin-bars" aria-label="Monto vendido por mes">
                    @foreach($months as $month)
                        <div class="admin-bar">
                            <span class="admin-bar__value">${{ number_format($month['revenue'] / 1000, 1) }}k</span>
                            <i style="--bar-height: {{ max(4, ($month['revenue'] / $chartMax) * 100) }}%"></i>
                            <span>{{ $month['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="admin-panel admin-panel--summary">
                <header class="admin-panel__header"><div><span>Estado de la operación</span><h2>Pulso comercial</h2></div></header>
                <dl class="admin-summary-list">
                    <div><dt>Clientes registrados</dt><dd>{{ number_format($commercialSummary['customers']) }}</dd></div>
                    <div><dt>Ventas atribuidas</dt><dd>{{ number_format($commercialSummary['attributedSales']) }}</dd></div>
                    <div><dt>Comisiones pendientes</dt><dd>${{ number_format($commercialSummary['pendingCommissions'], 0) }}</dd></div>
                    <div><dt>Renovaciones próximas</dt><dd>{{ number_format($commercialSummary['upcomingRenewals']) }}</dd></div>
                    <div><dt>Renovaciones vencidas</dt><dd class="is-alert">{{ number_format($commercialSummary['overdueRenewals']) }}</dd></div>
                </dl>
                <p class="admin-panel__footnote">La estructura financiera ya está preparada. Los módulos operativos se habilitarán por fase.</p>
            </article>
        </section>
    @endif

    <section class="admin-panel admin-panel--table">
        <header class="admin-panel__header">
            <div><span>Actividad reciente</span><h2>{{ $mode === 'admin' ? 'Últimas ventas' : 'Mis últimas ventas' }}</h2></div>
            <span class="admin-table-count">{{ $recentOrders->count() }} registros</span>
        </header>

        @if($recentOrders->isEmpty())
            <div class="admin-empty-state">
                <span>XS</span>
                <div><h3>Aún no hay ventas registradas.</h3><p>Cuando se genere una orden, aparecerá aquí con su estado de pago.</p></div>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead><tr><th>Referencia</th><th>Cliente</th><th>Paquete</th>@if($mode === 'admin')<th>Vendedor</th>@endif<th>Total</th><th>Estado</th><th>Fecha</th></tr></thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr>
                                <td data-label="Referencia"><strong>{{ $order->reference }}</strong></td>
                                <td data-label="Cliente"><span>{{ $order->customer_name }}</span><small>{{ $order->business_name }}</small></td>
                                <td data-label="Paquete">{{ $order->package_name_snapshot ?: $order->package?->name }}</td>
                                @if($mode === 'admin')<td data-label="Vendedor">{{ $order->seller?->name ?: 'Venta directa' }}</td>@endif
                                <td data-label="Total"><strong>${{ number_format((float) $order->total_amount, 0) }}</strong> <small>{{ $order->currency }}</small></td>
                                <td data-label="Estado"><span class="admin-status admin-status--{{ str($order->status->value ?? $order->status)->replace('_', '-') }}">{{ str($order->status->value ?? $order->status)->replace('_', ' ')->headline() }}</span></td>
                                <td data-label="Fecha">{{ $order->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-layouts.admin>
