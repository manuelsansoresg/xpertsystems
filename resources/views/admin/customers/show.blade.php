<x-layouts.admin title="Ficha de cliente">
    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Comercial > Clientes</span>
            <h1>{{ $customer->first_name }} {{ $customer->last_name }}</h1>
            @if($customer->business_name)
                <p>{{ $customer->business_name }}</p>
            @endif
        </div>
        <div class="admin-crud-head__actions">
            <a href="{{ route('admin.customers.edit', $customer) }}" class="admin-btn admin-btn--primary">Editar</a>
            <form method="POST" action="{{ route('admin.customers.toggle', $customer) }}" style="display:inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-btn admin-btn--ghost">
                    {{ $customer->status === 'inactive' ? 'Reactivar' : 'Inactivar' }}
                </button>
            </form>
        </div>
    </section>

    @if(session('status'))
        <div class="admin-toast admin-toast--success" role="status">{{ session('status') }}</div>
    @endif

    <section class="admin-metrics" aria-label="Métricas del cliente">
        <article class="admin-metric">
            <div class="admin-metric__top"><span>01</span><i></i></div>
            <p>Compras</p>
            <strong>{{ number_format($salesCount) }}</strong>
            <span class="admin-metric__note">Completadas</span>
        </article>
        <article class="admin-metric admin-metric--featured">
            <div class="admin-metric__top"><span>02</span><i></i></div>
            <p>Total comprado</p>
            <strong><small>$</small>{{ number_format($salesSum, 0) }}<em>MXN</em></strong>
            <span class="admin-metric__note">Histórico</span>
        </article>
        <article class="admin-metric">
            <div class="admin-metric__top"><span>03</span><i></i></div>
            <p>Primera compra</p>
            <strong style="font-size: 1.2rem;">{{ $customer->first_purchase_at?->format('d/m/Y') ?? '—' }}</strong>
        </article>
        <article class="admin-metric">
            <div class="admin-metric__top"><span>04</span><i></i></div>
            <p>Última compra</p>
            <strong style="font-size: 1.2rem;">{{ $customer->last_purchase_at?->format('d/m/Y') ?? '—' }}</strong>
        </article>
    </section>

    <div class="admin-profile-grid">
        <section class="admin-panel">
            <header class="admin-panel__header">
                <div><span>Contacto</span><h2>Información de contacto</h2></div>
            </header>
            <div class="admin-panel__body">
                <dl class="admin-detail-list">
                    @if($customer->email)
                    <div>
                        <dt>Email</dt>
                        <dd>{{ $customer->email }}</dd>
                    </div>
                    @endif
                    @if($customer->phone)
                    <div>
                        <dt>Teléfono</dt>
                        <dd>
                            {{ $customer->phone }}
                            @php
                                $phoneClean = preg_replace('/\D+/', '', $customer->phone);
                                $whatsappUrl = $phoneClean ? "https://wa.me/{$phoneClean}" : null;
                            @endphp
                            @if($whatsappUrl)
                                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="admin-link-small">Abrir WhatsApp <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                            @endif
                        </dd>
                    </div>
                    @endif
                    @if($customer->country)
                    <div>
                        <dt>País</dt>
                        <dd>{{ $customer->country }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </section>

        <section class="admin-panel">
            <header class="admin-panel__header">
                <div><span>Atribución</span><h2>Información comercial</h2></div>
            </header>
            <div class="admin-panel__body">
                <dl class="admin-detail-list">
                    <div>
                        <dt>Estado</dt>
                        <dd>
                            <span class="admin-status admin-status--{{ $customer->status === 'customer' ? 'completed' : ($customer->status === 'inactive' ? 'cancelled' : 'pending') }}">
                                {{ ucfirst($customer->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt>Origen</dt>
                        <dd><span class="admin-tag">{{ ucfirst($customer->source) }}</span></dd>
                    </div>
                    @if($customer->seller)
                    <div>
                        <dt>Vendedor</dt>
                        <dd>
                            <a href="{{ route('admin.sellers.show', $customer->seller->sellerProfile) }}" class="admin-link-small">
                                {{ $customer->seller->name }} 
                            </a>
                        </dd>
                    </div>
                    @else
                    <div>
                        <dt>Vendedor</dt>
                        <dd><span class="admin-tag">Venta directa</span></dd>
                    </div>
                    @endif
                    @if($customer->referral_code)
                    <div>
                        <dt>Código referido</dt>
                        <dd><code class="admin-code">{{ $customer->referral_code }}</code></dd>
                    </div>
                    @endif
                </dl>
            </div>
        </section>

        @if($customer->notes)
        <section class="admin-panel admin-panel--full">
            <header class="admin-panel__header">
                <div><span>Interno</span><h2>Notas</h2></div>
            </header>
            <div class="admin-panel__body">
                <p class="admin-notes">{{ $customer->notes }}</p>
            </div>
        </section>
        @endif

        <section class="admin-panel admin-panel--full">
            <header class="admin-panel__header">
                <div><span>Historial</span><h2>Compras</h2></div>
            </header>
            <div class="admin-panel__body">
                @if($salesCount > 0)
                    <p>Este cliente tiene {{ $salesCount }} compra(s) registrada(s).</p>
                    <p class="admin-field-hint">El módulo de Ventas mostrará el detalle completo próximamente.</p>
                @else
                    <div class="admin-empty-state" style="min-height: 120px;">
                        <span>XS</span>
                        <div><h3>Sin compras registradas</h3><p>Este cliente todavía no tiene compras registradas.</p></div>
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-layouts.admin>
