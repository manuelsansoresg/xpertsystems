<x-layouts.admin title="Pagos a vendedores">
    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Vendedores</span>
            <h1>Pagos</h1>
            <p>Registro de pagos realizados a vendedores por comisiones.</p>
        </div>
    </section>

    <section class="admin-metrics" aria-label="Resumen de pagos">
        <article class="admin-metric">
            <div class="admin-metric__top"><span>01</span><i></i></div>
            <p>Total pagado</p>
            <strong><small>$</small>{{ number_format((float) $summary['total'], 0) }}<em>MXN</em></strong>
            <span class="admin-metric__note">Histórico</span>
        </article>
        <article class="admin-metric">
            <div class="admin-metric__top"><span>02</span><i></i></div>
            <p>Pendiente</p>
            <strong><small>$</small>{{ number_format((float) $summary['pending'], 0) }}<em>MXN</em></strong>
            <span class="admin-metric__note">Por liquidar</span>
        </article>
        <article class="admin-metric admin-metric--featured">
            <div class="admin-metric__top"><span>03</span><i></i></div>
            <p>Completado</p>
            <strong><small>$</small>{{ number_format((float) $summary['completed'], 0) }}<em>MXN</em></strong>
            <span class="admin-metric__note">Liquidado</span>
        </article>
    </section>

    <form method="GET" action="{{ route('admin.payouts.index') }}" class="admin-filters">
        <label class="admin-filters__field">
            <span>Vendedor</span>
            <select name="seller_id">
                <option value="">Todos</option>
                @foreach($sellers as $seller)
                    <option value="{{ $seller->id }}" @selected(($filters['seller_id'] ?? '') == $seller->id)>{{ $seller->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="admin-filters__field">
            <span>Estado</span>
            <select name="status">
                <option value="">Todos</option>
                <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Pendiente</option>
                <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Completado</option>
            </select>
        </label>
        <button type="submit" class="admin-btn admin-btn--ghost">Filtrar</button>
        @if($filters['seller_id'] || $filters['status'])
            <a href="{{ route('admin.payouts.index') }}" class="admin-btn admin-btn--ghost">Limpiar</a>
        @endif
    </form>

    <section class="admin-panel admin-panel--table">
        <header class="admin-panel__header">
            <div><span>Listado</span><h2>{{ $payouts->total() }} pagos</h2></div>
        </header>

        @if($payouts->isEmpty())
            <div class="admin-empty-state">
                <span>XS</span>
                <div><h3>Aún no hay pagos registrados.</h3><p>Los pagos se registran manualmente al liquidar comisiones.</p></div>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Vendedor</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Referencia</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payouts as $payout)
                            <tr>
                                <td data-label="ID"><strong>#{{ $payout->id }}</strong></td>
                                <td data-label="Vendedor">{{ $payout->seller->name }}</td>
                                <td data-label="Monto"><strong>${{ number_format((float) $payout->amount, 0) }}</strong></td>
                                <td data-label="Método">{{ ucfirst($payout->payment_method) }}</td>
                                <td data-label="Referencia">{{ $payout->reference ?? '—' }}</td>
                                <td data-label="Estado">
                                    <span class="admin-status admin-status--{{ $payout->status === 'completed' ? 'completed' : 'pending' }}">
                                        {{ ucfirst($payout->status) }}
                                    </span>
                                </td>
                                <td data-label="Fecha">{{ $payout->created_at->format('d/m/Y') }}</td>
                                <td data-label="Acciones">
                                    <div class="admin-actions">
                                        <a href="{{ route('admin.payouts.show', $payout) }}" class="admin-actions__btn admin-actions__btn--view" title="Ver detalle" aria-label="Ver detalle del pago {{ $payout->id }}"><i class="fa-solid fa-eye" aria-hidden="true"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($payouts->hasPages())
                <div class="admin-pagination">
                    {{ $payouts->links() }}
                </div>
            @endif
        @endif
    </section>
</x-layouts.admin>
