<x-layouts.admin title="Cupones">
    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Comercial</span>
            <h1>Cupones</h1>
            <p>Gestiona descuentos, campañas y cupones asociados a tu equipo comercial.</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="admin-btn admin-btn--primary">
            + Nuevo cupón
        </a>
    </section>

    @if(session('success'))
        <div class="admin-toast admin-toast--success" role="status">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.coupons.index') }}" class="admin-filters">
        <label class="admin-filters__field">
            <span>Buscar</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Código o nombre">
        </label>
        <label class="admin-filters__field">
            <span>Estado</span>
            <select name="status">
                <option value="">Todos</option>
                <option value="active" @selected(request('status') === 'active')>Activo</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactivo</option>
                <option value="expired" @selected(request('status') === 'expired')>Expirado</option>
                <option value="exhausted" @selected(request('status') === 'exhausted')>Agotado</option>
            </select>
        </label>
        <label class="admin-filters__field">
            <span>Tipo</span>
            <select name="type">
                <option value="">Todos</option>
                <option value="percentage" @selected(request('type') === 'percentage')>Porcentaje</option>
                <option value="fixed" @selected(request('type') === 'fixed')>Monto fijo</option>
            </select>
        </label>
        <label class="admin-filters__field">
            <span>Alcance</span>
            <select name="scope">
                <option value="">Todos</option>
                <option value="global" @selected(request('scope') === 'global')>Todos los paquetes</option>
                <option value="packages" @selected(request('scope') === 'packages')>Paquetes específicos</option>
            </select>
        </label>
        <label class="admin-filters__field">
            <span>Vendedor</span>
            <select name="seller_id">
                <option value="">Todos</option>
                <option value="general" @selected(request('seller_id') === 'general')">Cupón general</option>
                @foreach($sellers as $seller)
                    <option value="{{ $seller->id }}" @selected(request('seller_id') == $seller->id)>{{ $seller->name }}</option>
                @endforeach
            </select>
        </label>
        <button type="submit" class="admin-btn admin-btn--ghost">Filtrar</button>
        @if(request()->hasAny(['search', 'status', 'type', 'scope', 'seller_id']))
            <a href="{{ route('admin.coupons.index') }}" class="admin-btn admin-btn--ghost">Limpiar</a>
        @endif
    </form>

    <section class="admin-panel admin-panel--table">
        <header class="admin-panel__header">
            <div><span>Listado</span><h2>{{ $coupons->total() }} cupones</h2></div>
        </header>

        @if($coupons->isEmpty())
            <div class="admin-empty-state">
                <span>XS</span>
                <div><h3>Aún no hay cupones.</h3><p>Crea tu primer cupón de descuento.</p></div>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Descuento</th>
                            <th>Aplicación</th>
                            <th>Vendedor</th>
                            <th>Vigencia</th>
                            <th>Usos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                            <tr>
                                <td data-label="Código">
                                    <code class="admin-code">{{ $coupon->code }}</code>
                                </td>
                                <td data-label="Nombre">{{ $coupon->name }}</td>
                                <td data-label="Descuento">
                                    <strong>{{ $coupon->discountDisplay() }}</strong>
                                </td>
                                <td data-label="Aplicación">
                                    @if($coupon->scope === \App\Enums\CouponScope::Global)
                                        <span class="admin-tag">Todos</span>
                                    @else
                                        <span class="admin-tag" title="{{ $coupon->packages->pluck('name')->implode(', ') }}">
                                            {{ $coupon->packages->count() }} paquete(s)
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Vendedor">
                                    @if($coupon->seller)
                                        <span>{{ $coupon->seller->name }}</span>
                                    @else
                                        <span class="admin-tag">General</span>
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
                                <td data-label="Acciones">
                                    <div class="admin-actions">
                                        <a href="{{ route('admin.coupons.show', $coupon) }}" class="admin-actions__btn" title="Ver detalle">👁</a>
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="admin-actions__btn" title="Editar">✎</a>
                                        <form method="POST" action="{{ route('admin.coupons.toggle', $coupon) }}" style="display:inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="admin-actions__btn" title="{{ $coupon->is_active ? 'Desactivar' : 'Activar' }}">
                                                {{ $coupon->is_active ? '⊘' : '✓' }}
                                            </button>
                                        </form>
                                    </div>
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
