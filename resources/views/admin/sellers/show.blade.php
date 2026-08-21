<x-layouts.admin title="Perfil de vendedor">
    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Vendedores > Equipo</span>
            <h1>{{ $seller->user->name }}</h1>
            <p>{{ $seller->user->email }}</p>
        </div>
        <div class="admin-crud-head__actions">
            <a href="{{ route('admin.sellers.edit', $seller) }}" class="admin-btn admin-btn--primary">Editar</a>
            <form method="POST" action="{{ route('admin.sellers.toggle', $seller) }}" style="display:inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-btn admin-btn--ghost">
                    {{ $seller->user->active ? 'Desactivar' : 'Activar' }}
                </button>
            </form>
        </div>
    </section>

    @if(session('status'))
        <div class="admin-toast admin-toast--success" role="status">{{ session('status') }}</div>
    @endif

    <section class="admin-metrics" aria-label="Métricas del vendedor">
        <article class="admin-metric">
            <div class="admin-metric__top"><span>01</span><i></i></div>
            <p>Clientes</p>
            <strong>{{ number_format($metrics['customers']) }}</strong>
            <span class="admin-metric__note">Únicos</span>
        </article>
        <article class="admin-metric">
            <div class="admin-metric__top"><span>02</span><i></i></div>
            <p>Ventas</p>
            <strong>{{ number_format($metrics['sales']) }}</strong>
            <span class="admin-metric__note">Atribuidas</span>
        </article>
        <article class="admin-metric admin-metric--featured">
            <div class="admin-metric__top"><span>03</span><i></i></div>
            <p>Monto vendido</p>
            <strong><small>$</small>{{ number_format((float) $metrics['sales_sum'], 0) }}<em>MXN</em></strong>
            <span class="admin-metric__note">Total</span>
        </article>
        <article class="admin-metric">
            <div class="admin-metric__top"><span>04</span><i></i></div>
            <p>Comisión generada</p>
            <strong><small>$</small>{{ number_format((float) $metrics['commissions_sum'], 0) }}<em>MXN</em></strong>
            <span class="admin-metric__note">Histórico</span>
        </article>
    </section>

    <div class="admin-profile-grid">
        <section class="admin-panel">
            <header class="admin-panel__header">
                <div><span>Referido</span><h2>Código de referido</h2></div>
            </header>
            <div class="admin-panel__body">
                <code class="admin-code admin-code--large">{{ $seller->referral_code }}</code>
                <div class="admin-referral-link" x-data="{ copied: false }">
                    <span class="admin-referral-link__url">{{ $referralUrl }}</span>
                    <button type="button" class="admin-btn admin-btn--ghost" @click="navigator.clipboard.writeText(@js($referralUrl)); copied = true; setTimeout(() => copied = false, 1800)">
                        <span x-text="copied ? 'Copiado' : 'Copiar enlace'"></span>
                    </button>
                </div>
            </div>
        </section>

        <section class="admin-panel">
            <header class="admin-panel__header">
                <div><span>Comisión</span><h2>Configuración</h2></div>
            </header>
            <div class="admin-panel__body">
                <dl class="admin-detail-list">
                    <div>
                        <dt>Tipo</dt>
                        <dd>
                            <span class="admin-tag {{ $seller->commission_type === 'percentage' ? 'admin-tag--gold' : 'admin-tag--blue' }}">
                                {{ $seller->commission_type === 'percentage' ? 'Porcentaje' : 'Monto fijo' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt>Valor</dt>
                        <dd>
                            @if($seller->commission_type === 'percentage')
                                {{ number_format((float) $seller->commission_value, 1) }}%
                            @else
                                ${{ number_format((float) $seller->commission_value, 0) }} MXN
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </section>

        @if($seller->payment_method || $seller->payment_details)
        <section class="admin-panel">
            <header class="admin-panel__header">
                <div><span>Pago</span><h2>Datos de pago</h2></div>
            </header>
            <div class="admin-panel__body">
                <dl class="admin-detail-list">
                    @if($seller->payment_method)
                    <div>
                        <dt>Método</dt>
                        <dd>{{ ucfirst($seller->payment_method) }}</dd>
                    </div>
                    @endif
                    @if($seller->payment_details)
                    <div>
                        <dt>Detalles</dt>
                        <dd>{{ $seller->payment_details }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </section>
        @endif

        @if($seller->notes)
        <section class="admin-panel">
            <header class="admin-panel__header">
                <div><span>Interno</span><h2>Notas</h2></div>
            </header>
            <div class="admin-panel__body">
                <p class="admin-notes">{{ $seller->notes }}</p>
            </div>
        </section>
        @endif

        <section class="admin-panel admin-panel--full">
            <header class="admin-panel__header">
                <div>
                    <span>Cupones</span>
                    <h2>Cupones asignados</h2>
                </div>
                <a href="{{ route('admin.coupons.create', ['seller_id' => $seller->user_id]) }}" class="admin-btn admin-btn--ghost" style="font-size:.75rem;padding:.35rem .7rem">
                    <i class="fa-solid fa-plus" aria-hidden="true"></i> Crear cupón
                </a>
            </header>
            <div class="admin-panel__body">
                @if($coupons->isEmpty())
                    <div class="admin-empty-state" style="min-height:100px">
                        <span>XS</span>
                        <div><h3>Sin cupones asignados</h3><p>Este vendedor no tiene cupones asignados.</p></div>
                    </div>
                @else
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Descuento</th>
                                    <th>Paquetes</th>
                                    <th>Usos</th>
                                    <th>Estado</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coupons as $coupon)
                                    <tr>
                                        <td data-label="Código"><code class="admin-code">{{ $coupon->code }}</code></td>
                                        <td data-label="Descuento"><strong>{{ $coupon->discountDisplay() }}</strong></td>
                                        <td data-label="Paquetes">
                                            @if($coupon->scope === \App\Enums\CouponScope::Global)
                                                <span class="admin-tag">Todos</span>
                                            @else
                                                <span class="admin-tag">{{ $coupon->packages->count() }} paquete(s)</span>
                                            @endif
                                        </td>
                                        <td data-label="Usos">{{ $coupon->times_used }}{{ $coupon->usage_limit ? '/' . $coupon->usage_limit : '' }}</td>
                                        <td data-label="Estado">
                                            <span class="admin-status admin-status--{{ $coupon->statusColor() }}">{{ $coupon->statusLabel() }}</span>
                                        </td>
                                        <td data-label="">
                                            <a href="{{ route('admin.coupons.show', $coupon) }}" class="admin-actions__btn admin-actions__btn--view" title="Ver" aria-label="Ver cupón {{ $coupon->code }}"><i class="fa-solid fa-eye" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top:.75rem;text-align:right">
                        <a href="{{ route('admin.coupons.index', ['seller_id' => $seller->user_id]) }}" class="admin-link-small">Ver todos los cupones <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-layouts.admin>
