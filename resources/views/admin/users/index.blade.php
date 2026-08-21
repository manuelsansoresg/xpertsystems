<x-layouts.admin title="Usuarios">
    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Sistema</span>
            <h1>Usuarios</h1>
            <p>Gestión de cuentas internas del sistema.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="admin-btn admin-btn--primary">
            <i class="fa-solid fa-plus" aria-hidden="true"></i> Nuevo usuario
        </a>
    </section>

    @if(session('status'))
        <div class="admin-toast admin-toast--success" role="status">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="admin-toast admin-toast--error" role="alert">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.users.index') }}" class="admin-filters">
        <label class="admin-filters__field">
            <span>Buscar</span>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nombre o email">
        </label>
        <label class="admin-filters__field">
            <span>Rol</span>
            <select name="role">
                <option value="">Todos</option>
                <option value="admin" @selected(($filters['role'] ?? '') === 'admin')>Administrador</option>
                <option value="seller" @selected(($filters['role'] ?? '') === 'seller')>Vendedor</option>
            </select>
        </label>
        <label class="admin-filters__field">
            <span>Estado</span>
            <select name="active">
                <option value="">Todos</option>
                <option value="1" @selected(($filters['active'] ?? '') === '1' || ($filters['active'] ?? '') === 1)>Activo</option>
                <option value="0" @selected(($filters['active'] ?? '') === '0' || ($filters['active'] ?? '') === 0)>Inactivo</option>
            </select>
        </label>
        <button type="submit" class="admin-btn admin-btn--ghost">Filtrar</button>
        @if($filters['search'] || $filters['role'] || $filters['active'] !== null)
            <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn--ghost">Limpiar</a>
        @endif
    </form>

    <section class="admin-panel admin-panel--table">
        <header class="admin-panel__header">
            <div><span>Listado</span><h2>{{ $users->total() }} usuarios</h2></div>
        </header>

        @if($users->isEmpty())
            <div class="admin-empty-state">
                <span>XS</span>
                <div><h3>No hay usuarios.</h3><p>Crea el primer usuario del sistema.</p></div>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Último acceso</th>
                            <th>Alta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td data-label="Nombre"><strong>{{ $user->name }}</strong></td>
                                <td data-label="Email"><span>{{ $user->email }}</span></td>
                                <td data-label="Rol">
                                    @foreach($user->roles as $role)
                                        <span class="admin-tag">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td data-label="Estado">
                                    <span class="admin-status admin-status--{{ $user->active ? 'completed' : 'cancelled' }}">
                                        {{ $user->active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td data-label="Último acceso">
                                    {{ $user->last_login_at?->diffForHumans() ?? '—' }}
                                </td>
                                <td data-label="Alta">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td data-label="Acciones">
                                    <div class="admin-actions">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="admin-actions__btn admin-actions__btn--edit" title="Editar" aria-label="Editar {{ $user->name }}"><i class="fa-solid fa-pen" aria-hidden="true"></i></a>
                                        @if(! $user->is(auth()->user()))
                                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="admin-actions__btn {{ $user->active ? 'admin-actions__btn--danger' : 'admin-actions__btn--success' }}" title="{{ $user->active ? 'Desactivar' : 'Activar' }}" aria-label="{{ $user->active ? 'Desactivar' : 'Activar' }} {{ $user->name }}">
                                                    <i class="fa-solid {{ $user->active ? 'fa-toggle-off' : 'fa-toggle-on' }}" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="admin-pagination">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </section>
</x-layouts.admin>
