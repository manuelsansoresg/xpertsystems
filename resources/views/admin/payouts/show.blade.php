<x-layouts.admin title="Detalle de pago">
    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Vendedores > Pagos</span>
            <h1>Pago #{{ $payout->id }}</h1>
            <p>{{ $payout->seller->name }} — {{ $payout->seller->email }}</p>
        </div>
        <a href="{{ route('admin.payouts.index') }}" class="admin-btn admin-btn--ghost"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Volver</a>
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
                            <span class="admin-status admin-status--{{ $payout->status === 'completed' ? 'completed' : 'pending' }}">
                                {{ ucfirst($payout->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt>Monto</dt>
                        <dd><strong>${{ number_format((float) $payout->amount, 2) }}</strong></dd>
                    </div>
                    <div>
                        <dt>Método de pago</dt>
                        <dd>{{ ucfirst($payout->payment_method) }}</dd>
                    </div>
                    @if($payout->reference)
                    <div>
                        <dt>Referencia</dt>
                        <dd>{{ $payout->reference }}</dd>
                    </div>
                    @endif
                    @if($payout->paid_at)
                    <div>
                        <dt>Fecha de pago</dt>
                        <dd>{{ $payout->paid_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt>Registrado por</dt>
                        <dd>{{ $payout->recordedBy?->name ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </section>

        @if($payout->notes)
        <section class="admin-panel">
            <header class="admin-panel__header">
                <div><span>Notas</span><h2>Observaciones</h2></div>
            </header>
            <div class="admin-panel__body">
                <p class="admin-notes">{{ $payout->notes }}</p>
            </div>
        </section>
        @endif

        @if($payout->commissions->isNotEmpty())
        <section class="admin-panel admin-panel--full">
            <header class="admin-panel__header">
                <div><span>Comisiones</span><h2>Comisiones incluidas en este pago</h2></div>
            </header>
            <div class="admin-panel__body">
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Comisión</th>
                                <th>Orden</th>
                                <th>Monto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payout->commissions as $commission)
                                <tr>
                                    <td data-label="Comisión">#{{ $commission->id }}</td>
                                    <td data-label="Orden">{{ $commission->order?->reference ?? '—' }}</td>
                                    <td data-label="Monto">${{ number_format((float) $commission->pivot->amount, 2) }}</td>
                                    <td data-label="Estado">
                                        <span class="admin-status admin-status--{{ $commission->status === 'paid' ? 'completed' : 'pending' }}">
                                            {{ ucfirst($commission->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        @endif
    </div>
</x-layouts.admin>
