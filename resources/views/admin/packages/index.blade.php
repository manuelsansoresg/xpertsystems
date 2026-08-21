<x-layouts.admin title="Paquetes">
    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Comercial</span>
            <h1>Paquetes</h1>
            <p>Gestiona los paquetes disponibles en la landing pública.</p>
        </div>
        <a href="{{ route('admin.packages.create') }}" class="admin-btn admin-btn--primary">
            <i class="fa-solid fa-plus" aria-hidden="true"></i> Nuevo paquete
        </a>
    </section>

    @if(session('status'))
        <div class="admin-toast admin-toast--success" role="status">{{ session('status') }}</div>
    @endif

    <section class="admin-panel admin-panel--table">
        <header class="admin-panel__header">
            <div><span>Listado</span><h2>{{ $packages->count() }} paquetes</h2></div>
        </header>

        @if($packages->isEmpty())
            <div class="admin-empty-state">
                <span>XS</span>
                <div><h3>Aún no hay paquetes.</h3><p>Crea el primer paquete para la landing.</p></div>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Precio</th>
                            <th>Renovación</th>
                            <th>Destacado</th>
                            <th>Orden</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($packages as $package)
                            <tr>
                                <td data-label="Nombre">
                                    <strong>{{ $package->name }}</strong>
                                    <small>{{ $package->slug }}</small>
                                </td>
                                <td data-label="Tipo">
                                    <span class="admin-tag {{ $package->price_type === 'fixed' ? 'admin-tag--gold' : ($package->price_type === 'starting_at' ? 'admin-tag--blue' : '') }}">
                                        {{ ucfirst(str_replace('_', ' ', $package->price_type)) }}
                                    </span>
                                </td>
                                <td data-label="Precio">
                                    @if($package->price_type === 'quote')
                                        Cotizar
                                    @else
                                        ${{ number_format((float) $package->price, 0) }}
                                    @endif
                                </td>
                                <td data-label="Renovación">
                                    @if($package->renewal_enabled)
                                        <span class="admin-tag admin-tag--gold">
                                            @if($package->show_renewal_price)
                                                ${{ number_format((float) $package->renewal_price, 0) }}
                                            @else
                                                Sí
                                            @endif
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td data-label="Destacado">
                                    @if($package->is_featured)
                                        <span class="admin-tag admin-tag--gold">Destacado</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td data-label="Orden">{{ $package->sort_order }}</td>
                                <td data-label="Estado">
                                    <span class="admin-status admin-status--{{ $package->active ? 'completed' : 'cancelled' }}">
                                        {{ $package->active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td data-label="Acciones">
                                    <div class="admin-actions">
                                        <a href="{{ route('admin.packages.edit', $package) }}" class="admin-actions__btn admin-actions__btn--edit" title="Editar" aria-label="Editar {{ $package->name }}"><i class="fa-solid fa-pen" aria-hidden="true"></i></a>
                                        <form method="POST" action="{{ route('admin.packages.toggle', $package) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="admin-actions__btn {{ $package->active ? 'admin-actions__btn--danger' : 'admin-actions__btn--success' }}" title="{{ $package->active ? 'Desactivar' : 'Activar' }}" aria-label="{{ $package->active ? 'Desactivar' : 'Activar' }} {{ $package->name }}">
                                                <i class="fa-solid {{ $package->active ? 'fa-toggle-off' : 'fa-toggle-on' }}" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-layouts.admin>
