<x-layouts.admin title="Comisiones">
    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Vendedores</span>
            <h1>Comisiones</h1>
            <p>Registro de comisiones generadas por ventas atribuidas.</p>
        </div>
    </section>

    <section class="admin-metrics" aria-label="Resumen de comisiones">
        <article class="admin-metric">
            <div class="admin-metric__top"><span>01</span><i></i></div>
            <p>Total generado</p>
            <strong><small>$</small>{{ number_format((float) $summary['total'], 0) }}<em>MXN</em></strong>
            <span class="admin-metric__note">Histórico</span>
        </article>
        <article class="admin-metric">
            <div class="admin-metric__top"><span>02</span><i></i></div>
            <p>Pendiente</p>
            <strong><small>$</small>{{ number_format((float) $summary['pending'], 0) }}<em>MXN</em></strong>
            <span class="admin-metric__note">Por acreditar</span>
        </article>
        <article class="admin-metric admin-metric--featured">
            <div class="admin-metric__top"><span>03</span><i></i></div>
            <p>Disponible</p>
            <strong><small>$</small>{{ number_format((float) $summary['available'], 0) }}<em>MXN</em></strong>
            <span class="admin-metric__note">Para pago</span>
        </article>
        <article class="admin-metric">
            <div class="admin-metric__top"><span>04</span><i></i></div>
            <p>Pagado</p>
            <strong><small>$</small>{{ number_format((float) $summary['paid'], 0) }}<em>MXN</em></strong>
            <span class="admin-metric__note">Liquidado</span>
        </article>
    </section>

    <form method="GET" action="{{ route('admin.commissions.index') }}" class="admin-filters">
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
                <option value="available" @selected(($filters['status'] ?? '') === 'available')>Disponible</option>
                <option value="paid" @selected(($filters['status'] ?? '') === 'paid')>Pagado</option>
            </select>
        </label>
        <button type="submit" class="admin-btn admin-btn--ghost">Filtrar</button>
        @if($filters['seller_id'] || $filters['status'])
            <a href="{{ route('admin.commissions.index') }}" class="admin-btn admin-btn--ghost">Limpiar</a>
        @endif
    </form>

    <section class="admin-panel admin-panel--table">
        <header class="admin-panel__header">
            <div><span>Listado</span><h2>{{ $commissions->total() }} comisiones</h2></div>
        </header>

        @if($commissions->isEmpty())
            <div class="admin-empty-state">
                <span>XS</span>
                <div><h3>Aún no hay comisiones.</h3><p>Las comisiones se generan automáticamente al registrar ventas.</p></div>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Vendedor</th>
                            <th>Orden</th>
                            <th>Base</th>
                            <th>Tipo</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commissions as $commission)
                            <tr>
                                <td data-label="ID"><strong>#{{ $commission->id }}</strong></td>
                                <td data-label="Vendedor">{{ $commission->seller->name }}</td>
                                <td data-label="Orden">
                                    @if($commission->order)
                                        <span>{{ $commission->order->reference }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td data-label="Base">${{ number_format((float) $commission->base_amount, 0) }}</td>
                                <td data-label="Tipo">
                                    <span class="admin-tag {{ $commission->commission_type_snapshot === 'percentage' ? 'admin-tag--gold' : 'admin-tag--blue' }}">
                                        @if($commission->commission_type_snapshot === 'percentage')
                                            {{ number_format((float) $commission->commission_value_snapshot, 1) }}%
                                        @else
                                            ${{ number_format((float) $commission->commission_value_snapshot, 0) }}
                                        @endif
                                    </span>
                                </td>
                                <td data-label="Monto"><strong>${{ number_format((float) $commission->commission_amount, 0) }}</strong></td>
                                <td data-label="Estado">
                                    <span class="admin-status admin-status--{{ $commission->status === 'paid' ? 'completed' : ($commission->status === 'available' ? 'deposit-paid' : 'pending') }}">
                                        {{ ucfirst($commission->status) }}
                                    </span>
                                </td>
                                <td data-label="Fecha">{{ $commission->created_at->format('d/m/Y') }}</td>
                                <td data-label="Acciones">
                                    <div class="admin-actions">
                                        <a href="{{ route('admin.commissions.show', $commission) }}" class="admin-actions__btn" title="Ver detalle">👁</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($commissions->hasPages())
                <div class="admin-pagination">
                    {{ $commissions->links() }}
                </div>
            @endif
        @endif
    </section>
</x-layouts.admin>
