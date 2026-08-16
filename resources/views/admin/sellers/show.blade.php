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
    </div>
</x-layouts.admin>
