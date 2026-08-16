<x-layouts.admin title="Equipo de vendedores">
    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Vendedores</span>
            <h1>Equipo comercial</h1>
            <p>Gestiona vendedores, códigos de referido y reglas de comisión.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="admin-btn admin-btn--primary">
            + Nuevo vendedor
        </a>
    </section>

    @if(session('status'))
        <div class="admin-toast admin-toast--success" role="status">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="admin-toast admin-toast--error" role="alert">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.sellers.index') }}" class="admin-filters">
        <label class="admin-filters__field">
            <span>Buscar</span>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nombre, email o código">
        </label>
        <label class="admin-filters__field">
            <span>Estado</span>
            <select name="active">
                <option value="">Todos</option>
                <option value="1" @selected(($filters['active'] ?? '') === '1' || ($filters['active'] ?? '') === 1)>Activo</option>
                <option value="0" @selected(($filters['active'] ?? '') === '0' || ($filters['active'] ?? '') === 0)>Inactivo</option>
            </select>
        </label>
        <label class="admin-filters__field">
            <span>Comisión</span>
            <select name="commission_type">
                <option value="">Todas</option>
                <option value="percentage" @selected(($filters['commission_type'] ?? '') === 'percentage')>Porcentaje</option>
                <option value="fixed" @selected(($filters['commission_type'] ?? '') === 'fixed')>Monto fijo</option>
            </select>
        </label>
        <button type="submit" class="admin-btn admin-btn--ghost">Filtrar</button>
        @if($filters['search'] || $filters['active'] !== null || ($filters['commission_type'] ?? null))
            <a href="{{ route('admin.sellers.index') }}" class="admin-btn admin-btn--ghost">Limpiar</a>
        @endif
    </form>

    <section class="admin-panel admin-panel--table">
        <header class="admin-panel__header">
            <div><span>Equipo comercial</span><h2>{{ $sellers->total() }} vendedores</h2></div>
        </header>

        @if($sellers->isEmpty())
            <div class="admin-empty-state">
                <span>XS</span>
                <div><h3>Aún no hay vendedores.</h3><p>Alta al primer miembro del equipo comercial.</p></div>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Vendedor</th>
                            <th>Código</th>
                            <th>Comisión</th>
                            <th>Estado</th>
                            <th>Clientes</th>
                            <th>Ventas</th>
                            <th>Monto vendido</th>
                            <th>Comisión gen.</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sellers as $seller)
                            <tr>
                                <td data-label="Vendedor">
                                    <strong>{{ $seller->name }}</strong>
                                    <small>{{ $seller->email }}</small>
                                </td>
                                <td data-label="Código">
                                    <code class="admin-code">{{ $seller->sellerProfile?->referral_code ?? '—' }}</code>
                                </td>
                                <td data-label="Comisión">
                                    @if($seller->sellerProfile)
                                        <span class="admin-tag {{ $seller->sellerProfile->commission_type === 'percentage' ? 'admin-tag--gold' : 'admin-tag--blue' }}">
                                            @if($seller->sellerProfile->commission_type === 'percentage')
                                                {{ number_format((float) $seller->sellerProfile->commission_value, 1) }}%
                                            @else
                                                ${{ number_format((float) $seller->sellerProfile->commission_value, 0) }}
                                            @endif
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td data-label="Estado">
                                    <span class="admin-status admin-status--{{ $seller->active ? 'completed' : 'cancelled' }}">
                                        {{ $seller->active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td data-label="Clientes">{{ number_format($seller->customers_count) }}</td>
                                <td data-label="Ventas">{{ number_format($seller->orders_count) }}</td>
                                <td data-label="Monto vendido">${{ number_format((float) $seller->sales_sum, 0) }}</td>
                                <td data-label="Comisión gen.">${{ number_format($seller->commissions_sum, 0) }}</td>
                                <td data-label="Acciones">
                                    <div class="admin-actions">
                                        @if($seller->sellerProfile)
                                            <a href="{{ route('admin.sellers.show', $seller->sellerProfile) }}" class="admin-actions__btn" title="Ver perfil">👁</a>
                                            <a href="{{ route('admin.sellers.edit', $seller->sellerProfile) }}" class="admin-actions__btn" title="Editar">✎</a>
                                            @if(! $seller->is(auth()->user()))
                                                <form method="POST" action="{{ route('admin.sellers.toggle', $seller->sellerProfile) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="admin-actions__btn" title="{{ $seller->active ? 'Desactivar' : 'Activar' }}">
                                                        {{ $seller->active ? '⊘' : '✓' }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($sellers->hasPages())
                <div class="admin-pagination">
                    {{ $sellers->links() }}
                </div>
            @endif
        @endif
    </section>
</x-layouts.admin>
