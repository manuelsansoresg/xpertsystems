<x-layouts.admin title="Editar usuario">
    @php
        $isSeller = old('role', $user->hasRole('seller')) === 'seller';
    @endphp

    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Sistema > Usuarios</span>
            <h1>Editar usuario</h1>
            <p>{{ $user->name }} — {{ $user->email }}</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn--ghost"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Volver</a>
    </section>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="admin-form" x-data="sellerForm()" data-generate-code-url="{{ route('admin.sellers.generate-code') }}">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="admin-toast admin-toast--error" role="alert">
                Revisa los campos marcados.
            </div>
        @endif

        <fieldset class="admin-form__section">
            <legend>Datos del usuario</legend>
            <div class="admin-form__grid">
                <label class="admin-form__field">
                    <span>Nombre completo <em>*</em></span>
                    <input type="text" name="name" x-model="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Nombre</span>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}">
                </label>
                <label class="admin-form__field">
                    <span>Apellidos</span>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}">
                </label>
                <label class="admin-form__field">
                    <span>Email <em>*</em></span>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Teléfono</span>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                </label>
                <label class="admin-form__field">
                    <span>Rol <em>*</em></span>
                    <select name="role" x-model="role" required @change="onRoleChange()">
                        <option value="">Selecciona un rol</option>
                        <option value="admin" @selected(old('role', $user->hasRole('admin')) === 'admin')>Administrador</option>
                        <option value="seller" @selected(old('role', $user->hasRole('seller')) === 'seller')>Vendedor</option>
                    </select>
                    @error('role')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Contraseña nueva</span>
                    <input type="password" name="password" autocomplete="new-password">
                    <small class="admin-field-hint">Dejar vacío para mantener la actual.</small>
                    @error('password')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Confirmar contraseña</span>
                    <input type="password" name="password_confirmation" autocomplete="new-password">
                </label>
                <label class="admin-form__field admin-form__field--check">
                    <input type="checkbox" name="active" value="1" @checked(old('active', $user->active))>
                    <span>Usuario activo</span>
                </label>
            </div>
        </fieldset>

        <fieldset class="admin-form__section" x-show="role === 'seller'" x-cloak>
            <legend>Perfil comercial (Vendedor)</legend>
            <div class="admin-form__grid">
                <label class="admin-form__field">
                    <span>Código de referido <em>*</em></span>
                    <div class="admin-form__inline">
                        <input type="text" name="referral_code" value="{{ old('referral_code', $user->sellerProfile?->referral_code) }}" pattern="[A-Z0-9]+" style="text-transform:uppercase">
                        <button type="button" class="admin-btn admin-btn--ghost" @click="generateCode()">Generar código</button>
                    </div>
                    @error('referral_code')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Tipo de comisión <em>*</em></span>
                    <select name="commission_type">
                        <option value="">Selecciona</option>
                        <option value="percentage" @selected(old('commission_type', $user->sellerProfile?->commission_type) === 'percentage')>Porcentaje (%)</option>
                        <option value="fixed" @selected(old('commission_type', $user->sellerProfile?->commission_type) === 'fixed')>Monto fijo ($)</option>
                    </select>
                    @error('commission_type')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Valor de comisión <em>*</em></span>
                    <input type="number" name="commission_value" value="{{ old('commission_value', $user->sellerProfile?->commission_value) }}" step="0.01" min="0">
                    @error('commission_value')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Método de pago</span>
                    <input type="text" name="payment_method" value="{{ old('payment_method', $user->sellerProfile?->payment_method) }}">
                </label>
                <label class="admin-form__field admin-form__field--full">
                    <span>Datos de pago</span>
                    <textarea name="payment_details" rows="2">{{ old('payment_details', $user->sellerProfile?->payment_details) }}</textarea>
                </label>
                <label class="admin-form__field admin-form__field--full">
                    <span>Notas</span>
                    <textarea name="notes" rows="2">{{ old('notes', $user->sellerProfile?->notes) }}</textarea>
                </label>
            </div>
        </fieldset>

        <div class="admin-form__actions">
            <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn--ghost">Cancelar</a>
            <button type="submit" class="admin-btn admin-btn--primary">Guardar cambios</button>
        </div>
    </form>
</x-layouts.admin>
