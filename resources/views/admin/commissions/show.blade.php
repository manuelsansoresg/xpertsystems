<x-layouts.admin title="Detalle de comisión">
    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Vendedores > Comisiones</span>
            <h1>Comisión #{{ $commission->id }}</h1>
            <p>{{ $commission->seller->name }} — {{ $commission->seller->email }}</p>
        </div>
        <a href="{{ route('admin.commissions.index') }}" class="admin-btn admin-btn--ghost">← Volver</a>
    </section>

    <div class="admin-profile-grid">
        <section class="admin-panel">
            <header class="admin-panel__header">
                <div><span>Información</span><h2>Detalle</h2></div>
            </header>
            <div class="admin-panel__body">
                <dl class="admin-detail-list">
                    <div>
                        <dt>Estado</dt>
                        <dd>
                            <span class="admin-status admin-status--{{ $commission->status === 'paid' ? 'completed' : ($commission->status === 'available' ? 'deposit-paid' : 'pending') }}">
                                {{ ucfirst($commission->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt>Monto base</dt>
                        <dd>${{ number_format((float) $commission->base_amount, 2) }}</dd>
                    </div>
                    <div>
                        <dt>Tipo de comisión</dt>
                        <dd>
                            <span class="admin-tag {{ $commission->commission_type_snapshot === 'percentage' ? 'admin-tag--gold' : 'admin-tag--blue' }}">
                                @if($commission->commission_type_snapshot === 'percentage')
                                    {{ number_format((float) $commission->commission_value_snapshot, 1) }}%
                                @else
                                    ${{ number_format((float) $commission->commission_value_snapshot, 0) }} fijo
                                @endif
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt>Monto de comisión</dt>
                        <dd><strong>${{ number_format((float) $commission->commission_amount, 2) }}</strong></dd>
                    </div>
                    <div>
                        <dt>Base de cálculo</dt>
                        <dd>{{ ucfirst(str_replace('_', ' ', $commission->calculation_basis)) }}</dd>
                    </div>
                </dl>
            </div>
        </section>

        <section class="admin-panel">
            <header class="admin-panel__header">
                <div><span>Fechas</span><h2>Timeline</h2></div>
            </header>
            <div class="admin-panel__body">
                <dl class="admin-detail-list">
                    <div>
                        <dt>Creada</dt>
                        <dd>{{ $commission->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @if($commission->earned_at)
                    <div>
                        <dt>Devengada</dt>
                        <dd>{{ $commission->earned_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                    @if($commission->available_at)
                    <div>
                        <dt>Disponible</dt>
                        <dd>{{ $commission->available_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                    @if($commission->paid_at)
                    <div>
                        <dt>Pagada</dt>
                        <dd>{{ $commission->paid_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </section>

        @if($commission->order)
        <section class="admin-panel">
            <header class="admin-panel__header">
                <div><span>Orden asociada</span><h2>Venta</h2></div>
            </header>
            <div class="admin-panel__body">
                <dl class="admin-detail-list">
                    <div>
                        <dt>Referencia</dt>
                        <dd>{{ $commission->order->reference }}</dd>
                    </div>
                    <div>
                        <dt>Cliente</dt>
                        <dd>{{ $commission->order->customer_name }}</dd>
                    </div>
                    <div>
                        <dt>Total</dt>
                        <dd>${{ number_format((float) $commission->order->total_amount, 2) }}</dd>
                    </div>
                    <div>
                        <dt>Fecha</dt>
                        <dd>{{ $commission->order->created_at->format('d/m/Y') }}</dd>
                    </div>
                </dl>
            </div>
        </section>
        @endif

        @if($commission->payouts->isNotEmpty())
        <section class="admin-panel">
            <header class="admin-panel__header">
                <div><span>Pagos</span><h2>Pagos asociados</h2></div>
            </header>
            <div class="admin-panel__body">
                <ul class="admin-list">
                    @foreach($commission->payouts as $payout)
                        <li>
                            <span>#{{ $payout->id }} — ${{ number_format((float) $payout->pivot->amount, 2) }}</span>
                            <span class="admin-status admin-status--{{ $payout->status === 'completed' ? 'completed' : 'pending' }}">
                                {{ ucfirst($payout->status) }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
        @endif
    </div>
</x-layouts.admin>
