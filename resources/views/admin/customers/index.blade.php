<x-layouts.admin title="Clientes y prospectos">
    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Comercial</span>
            <h1>Clientes y prospectos</h1>
            <p>Centraliza la información comercial de quienes preguntan, compran o llegan por tus vendedores.</p>
        </div>
        <a href="{{ route('admin.customers.create') }}" class="admin-btn admin-btn--primary">
            + Nuevo cliente
        </a>
    </section>

    @if(session('status'))
        <div class="admin-toast admin-toast--success" role="status">{{ session('status') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.customers.index') }}" class="admin-filters">
        <label class="admin-filters__field">
            <span>Buscar</span>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nombre, negocio, email, teléfono">
        </label>
        <label class="admin-filters__field">
            <span>Estado</span>
            <select name="status">
                <option value="">Todos</option>
                <option value="lead" @selected(($filters['status'] ?? '') === 'lead')>Prospecto</option>
                <option value="customer" @selected(($filters['status'] ?? '') === 'customer')>Cliente</option>
                <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactivo</option>
            </select>
        </label>
        <label class="admin-filters__field">
            <span>Origen</span>
            <select name="source">
                <option value="">Todos</option>
                <option value="direct" @selected(($filters['source'] ?? '') === 'direct')>Directo</option>
                <option value="referral" @selected(($filters['source'] ?? '') === 'referral')>Referido</option>
                <option value="coupon" @selected(($filters['source'] ?? '') === 'coupon')>Cupón</option>
                <option value="whatsapp" @selected(($filters['source'] ?? '') === 'whatsapp')>WhatsApp</option>
                <option value="facebook" @selected(($filters['source'] ?? '') === 'facebook')>Facebook</option>
                <option value="instagram" @selected(($filters['source'] ?? '') === 'instagram')>Instagram</option>
                <option value="google" @selected(($filters['source'] ?? '') === 'google')>Google</option>
                <option value="email" @selected(($filters['source'] ?? '') === 'email')>Correo</option>
                <option value="other" @selected(($filters['source'] ?? '') === 'other')>Otro</option>
            </select>
        </label>
        <label class="admin-filters__field">
            <span>Vendedor</span>
            <select name="seller_id">
                <option value="">Todos</option>
                <option value="" @selected(($filters['seller_id'] ?? '') === '')>Sin vendedor</option>
                @foreach($sellers as $seller)
                    <option value="{{ $seller->id }}" @selected(($filters['seller_id'] ?? '') == $seller->id)>{{ $seller->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="admin-filters__field">
            <span>País</span>
            <select name="country">
                <option value="">Todos</option>
                @foreach($countries as $country)
                    <option value="{{ $country }}" @selected(($filters['country'] ?? '') === $country)>{{ $country }}</option>
                @endforeach
            </select>
        </label>
        <button type="submit" class="admin-btn admin-btn--ghost">Filtrar</button>
        @if($filters['search'] || $filters['status'] || $filters['source'] || $filters['seller_id'] !== null || $filters['country'])
            <a href="{{ route('admin.customers.index') }}" class="admin-btn admin-btn--ghost">Limpiar</a>
        @endif
    </form>

    <section class="admin-panel admin-panel--table">
        <header class="admin-panel__header">
            <div><span>Listado</span><h2>{{ $customers->total() }} clientes</h2></div>
        </header>

        @if($customers->isEmpty())
            <div class="admin-empty-state">
                <span>XS</span>
                <div><h3>Aún no hay clientes.</h3><p>Registra el primer cliente o prospecto.</p></div>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Negocio</th>
                            <th>Contacto</th>
                            <th>País</th>
                            <th>Origen</th>
                            <th>Vendedor</th>
                            <th>Estado</th>
                            <th>Compras</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td data-label="Cliente">
                                    <strong>{{ $customer->first_name }} {{ $customer->last_name }}</strong>
                                </td>
                                <td data-label="Negocio">{{ $customer->business_name ?? '—' }}</td>
                                <td data-label="Contacto">
                                    @if($customer->email)
                                        <span>{{ $customer->email }}</span>
                                    @endif
                                    @if($customer->phone)
                                        <small>{{ $customer->phone }}</small>
                                    @endif
                                </td>
                                <td data-label="País">{{ $customer->country ?? '—' }}</td>
                                <td data-label="Origen">
                                    <span class="admin-tag">{{ ucfirst($customer->source) }}</span>
                                </td>
                                <td data-label="Vendedor">
                                    @if($customer->seller)
                                        <span>{{ $customer->seller->name }}</span>
                                    @else
                                        <span class="admin-tag">Directo</span>
                                    @endif
                                </td>
                                <td data-label="Estado">
                                    <span class="admin-status admin-status--{{ $customer->status === 'customer' ? 'completed' : ($customer->status === 'inactive' ? 'cancelled' : 'pending') }}">
                                        {{ ucfirst($customer->status) }}
                                    </span>
                                </td>
                                <td data-label="Compras">{{ $customer->sales_count }}</td>
                                <td data-label="Total">${{ number_format($customer->sales_sum_total, 0) }}</td>
                                <td data-label="Acciones">
                                    <div class="admin-actions">
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="admin-actions__btn" title="Ver ficha">👁</a>
                                        <a href="{{ route('admin.customers.edit', $customer) }}" class="admin-actions__btn" title="Editar"></a>
                                        <form method="POST" action="{{ route('admin.customers.toggle', $customer) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="admin-actions__btn" title="{{ $customer->status === 'inactive' ? 'Reactivar' : 'Inactivar' }}">
                                                {{ $customer->status === 'inactive' ? '✓' : '⊘' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($customers->hasPages())
                <div class="admin-pagination">
                    {{ $customers->links() }}
                </div>
            @endif
        @endif
    </section>
</x-layouts.admin>
