<x-layouts.admin title="Cupón {{ $coupon->code }}">
    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Comercial > Cupones</span>
            <h1>{{ $coupon->code }}</h1>
            <p>{{ $coupon->name }}</p>
        </div>
        <div class="admin-crud-head__actions">
            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="admin-btn admin-btn--primary">Editar</a>
            <form method="POST" action="{{ route('admin.coupons.toggle', $coupon) }}" style="display:inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-btn admin-btn--ghost">
                    {{ $coupon->is_active ? 'Desactivar' : 'Activar' }}
                </button>
            </form>
            <form method="POST" action="{{ route('admin.coupons.duplicate', $coupon) }}" style="display:inline">
                @csrf
                <button type="submit" class="admin-btn admin-btn--ghost">Duplicar</button>
            </form>
        </div>
    </section>

    @if(session('success'))
        <div class="admin-toast admin-toast--success" role="status">{{ session('success') }}</div>
    @endif

    <section class="admin-metrics" aria-label="Métricas del cupón">
        <article class="admin-metric">
            <div class="admin-metric__top"><span>01</span><i></i></div>
            <p>Usos</p>
            <strong>{{ $metrics['total_uses'] }}{{ $coupon->usage_limit ? ' / ' . $coupon->usage_limit : '' }}</strong>
            <span class="admin-metric__note">{{ $coupon->usage_limit ? 'Con límite' : 'Sin límite' }}</span>
        </article>
        <article class="admin-metric admin-metric--featured">
            <div class="admin-metric__top"><span>02</span><i></i></div>
            <p>Descuento concedido</p>
            <strong><small>$</small>{{ number_format($metrics['total_discount'], 0) }}<em>MXN</em></strong>
            <span class="admin-metric__note">Histórico</span>
        </article>
        <article class="admin-metric">
            <div class="admin-metric__top"><span>03</span><i></i></div>
            <p>Estado</p>
            <strong style="font-size: 1.2rem;">
                <span class="admin-status admin-status--{{ $coupon->statusColor() }}">{{ $coupon->statusLabel() }}</span>
            </strong>
        </article>
        <article class="admin-metric">
            <div class="admin-metric__top"><span>04</span><i></i></div>
            <p>Descuento</p>
            <strong style="font-size: 1.2rem;">{{ $coupon->discountDisplay() }}</strong>
            <span class="admin-metric__note">{{ $coupon->discount_type->label() }}</span>
        </article>
    </section>

    <div class="admin-profile-grid">
        <section class="admin-panel">
            <header class="admin-panel__header">
                <div><span>Configuración</span><h2>Detalles del cupón</h2></div>
            </header>
            <div class="admin-panel__body">
                <dl class="admin-detail-list">
                    <div>
                        <dt>Código</dt>
                        <dd><code class="admin-code">{{ $coupon->code }}</code></dd>
                    </div>
                    <div>
                        <dt>Nombre</dt>
                        <dd>{{ $coupon->name }}</dd>
                    </div>
                    @if($coupon->description)
                    <div>
                        <dt>Descripción</dt>
                        <dd>{{ $coupon->description }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt>Tipo de descuento</dt>
                        <dd>{{ $coupon->discount_type->label() }}</dd>
                    </div>
                    <div>
                        <dt>Valor</dt>
                        <dd>{{ $coupon->discountDisplay() }}</dd>
                    </div>
                    @if($coupon->maximum_discount)
                    <div>
                        <dt>Descuento máximo</dt>
                        <dd>${{ number_format($coupon->maximum_discount, 2) }} MXN</dd>
                    </div>
                    @endif
                    @if($coupon->minimum_amount)
                    <div>
                        <dt>Compra mínima</dt>
                        <dd>${{ number_format($coupon->minimum_amount, 2) }} MXN</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </section>

        <section class="admin-panel">
            <header class="admin-panel__header">
                <div><span>Alcance</span><h2>Aplicación</h2></div>
            </header>
            <div class="admin-panel__body">
                <dl class="admin-detail-list">
                    <div>
                        <dt>Aplica a</dt>
                        <dd>
                            @if($coupon->scope === \App\Enums\CouponScope::Global)
                                <span class="admin-tag">Todos los paquetes</span>
                            @else
                                @foreach($coupon->packages as $package)
                                    <span class="admin-tag">{{ $package->name }}</span>
                                @endforeach
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt>Vendedor</dt>
                        <dd>
                            @if($coupon->seller)
                                <a href="{{ route('admin.sellers.show', $coupon->seller->sellerProfile ?? $coupon->seller) }}" class="admin-link-small">
                                    {{ $coupon->seller->name }}
                                </a>
                            @else
                                <span class="admin-tag">Cupón general</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </section>

        <section class="admin-panel">
            <header class="admin-panel__header">
                <div><span>Vigencia</span><h2>Fechas y límites</h2></div>
            </header>
            <div class="admin-panel__body">
                <dl class="admin-detail-list">
                    <div>
                        <dt>Inicio</dt>
                        <dd>{{ $coupon->starts_at?->format('d/m/Y H:i') ?? 'Sin fecha de inicio' }}</dd>
                    </div>
                    <div>
                        <dt>Vencimiento</dt>
                        <dd>{{ $coupon->expires_at?->format('d/m/Y H:i') ?? 'Sin vencimiento' }}</dd>
                    </div>
                    <div>
                        <dt>Límite total</dt>
                        <dd>{{ $coupon->usage_limit ?? 'Sin límite' }}</dd>
                    </div>
                    <div>
                        <dt>Límite por cliente</dt>
                        <dd>{{ $coupon->usage_limit_per_customer ?? 'Sin límite' }}</dd>
                    </div>
                    <div>
                        <dt>Usos actuales</dt>
                        <dd>{{ $coupon->times_used }}</dd>
                    </div>
                    <div>
                        <dt>Estado</dt>
                        <dd>
                            <span class="admin-status admin-status--{{ $coupon->statusColor() }}">{{ $coupon->statusLabel() }}</span>
                        </dd>
                    </div>
                </dl>
            </div>
        </section>

        <section class="admin-panel admin-panel--full">
            <header class="admin-panel__header">
                <div><span>Historial</span><h2>Usos recientes</h2></div>
            </header>
            <div class="admin-panel__body">
                @if($coupon->redemptions->isEmpty())
                    <div class="admin-empty-state" style="min-height: 120px;">
                        <span>XS</span>
                        <div><h3>No hay usos registrados todavía.</h3><p>Los usos aparecerán cuando se aplique este cupón en una venta.</p></div>
                    </div>
                @else
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Descuento aplicado</th>
                                    <th>Vendedor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coupon->redemptions->take(10) as $redemption)
                                    <tr>
                                        <td data-label="Fecha">{{ $redemption->redeemed_at->format('d/m/Y H:i') }}</td>
                                        <td data-label="Cliente">{{ $redemption->customer?->first_name ?? '—' }} {{ $redemption->customer?->last_name ?? '' }}</td>
                                        <td data-label="Descuento">${{ number_format($redemption->discount_amount, 2) }}</td>
                                        <td data-label="Vendedor">{{ $redemption->seller?->name ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <div style="margin-top:2rem;display:flex;gap:.75rem;justify-content:flex-end">
        <a href="{{ route('admin.coupons.index') }}" class="admin-btn admin-btn--ghost"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Volver al listado</a>
    </div>
</x-layouts.admin>
