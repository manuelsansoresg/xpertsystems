<x-layouts.admin title="Mis cupones">
    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Tu espacio</span>
            <h1>Mis cupones</h1>
            <p>Consulta los cupones de descuento asignados a ti.</p>
        </div>
    </section>

    <section class="admin-panel admin-panel--table">
        <header class="admin-panel__header">
            <div><span>Listado</span><h2>{{ $coupons->total() }} cupones</h2></div>
        </header>

        @if($coupons->isEmpty())
            <div class="admin-empty-state">
                <span>XS</span>
                <div><h3>No tienes cupones asignados.</h3><p>Cuando se te asignen cupones, aparecerán aquí.</p></div>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Descuento</th>
                            <th>Paquetes</th>
                            <th>Vigencia</th>
                            <th>Usos</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                            <tr>
                                <td data-label="Código">
                                    <code class="admin-code">{{ $coupon->code }}</code>
                                </td>
                                <td data-label="Descuento">
                                    <strong>{{ $coupon->discountDisplay() }}</strong>
                                </td>
                                <td data-label="Paquetes">
                                    @if($coupon->scope === \App\Enums\CouponScope::Global)
                                        <span class="admin-tag">Todos</span>
                                    @else
                                        <span class="admin-tag" title="{{ $coupon->packages->pluck('name')->implode(', ') }}">
                                            {{ $coupon->packages->count() }} paquete(s)
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Vigencia">
                                    @if($coupon->expires_at)
                                        <small>Hasta {{ $coupon->expires_at->format('d/m/Y') }}</small>
                                    @else
                                        <span class="admin-tag">Sin límite</span>
                                    @endif
                                </td>
                                <td data-label="Usos">
                                    {{ $coupon->times_used }}{{ $coupon->usage_limit ? ' / ' . $coupon->usage_limit : '' }}
                                </td>
                                <td data-label="Estado">
                                    <span class="admin-status admin-status--{{ $coupon->statusColor() }}">
                                        {{ $coupon->statusLabel() }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($coupons->hasPages())
                <div class="admin-pagination">
                    {{ $coupons->links() }}
                </div>
            @endif
        @endif
    </section>
</x-layouts.admin>
