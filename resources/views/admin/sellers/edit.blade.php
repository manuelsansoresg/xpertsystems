<x-layouts.admin title="Editar vendedor">
    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Vendedores > Equipo</span>
            <h1>Editar vendedor</h1>
            <p>{{ $seller->user->name }} — {{ $seller->user->email }}</p>
        </div>
        <a href="{{ route('admin.sellers.index') }}" class="admin-btn admin-btn--ghost"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Volver</a>
    </section>

    <form method="POST" action="{{ route('admin.sellers.update', $seller) }}" class="admin-form" x-data="sellerForm()" data-generate-code-url="{{ route('admin.sellers.generate-code') }}">
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
                    <input type="text" name="name" x-model="name" value="{{ old('name', $seller->user->name) }}" required>
                    @error('name')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Nombre</span>
                    <input type="text" name="first_name" value="{{ old('first_name', $seller->user->first_name) }}">
                </label>
                <label class="admin-form__field">
                    <span>Apellidos</span>
                    <input type="text" name="last_name" value="{{ old('last_name', $seller->user->last_name) }}">
                </label>
                <label class="admin-form__field">
                    <span>Email <em>*</em></span>
                    <input type="email" name="email" value="{{ old('email', $seller->user->email) }}" required>
                    @error('email')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Teléfono</span>
                    <input type="text" name="phone" value="{{ old('phone', $seller->user->phone) }}">
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
                    <input type="checkbox" name="active" value="1" @checked(old('active', $seller->user->active))>
                    <span>Vendedor activo</span>
                </label>
            </div>
        </fieldset>

        <fieldset class="admin-form__section">
            <legend>Perfil comercial</legend>
            <div class="admin-form__grid">
                <label class="admin-form__field">
                    <span>Código de referido <em>*</em></span>
                    <div class="admin-form__inline">
                        <input type="text" name="referral_code" value="{{ old('referral_code', $seller->referral_code) }}" required pattern="[A-Z0-9]+" style="text-transform:uppercase">
                        <button type="button" class="admin-btn admin-btn--ghost" @click="generateCode()">Generar código</button>
                    </div>
                    @error('referral_code')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Tipo de comisión <em>*</em></span>
                    <select name="commission_type" required>
                        <option value="">Selecciona</option>
                        <option value="percentage" @selected(old('commission_type', $seller->commission_type) === 'percentage')>Porcentaje (%)</option>
                        <option value="fixed" @selected(old('commission_type', $seller->commission_type) === 'fixed')>Monto fijo ($)</option>
                    </select>
                    @error('commission_type')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Valor de comisión <em>*</em></span>
                    <input type="number" name="commission_value" value="{{ old('commission_value', $seller->commission_value) }}" step="0.01" min="0" required>
                    @error('commission_value')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Método de pago</span>
                    <input type="text" name="payment_method" value="{{ old('payment_method', $seller->payment_method) }}">
                </label>
                <label class="admin-form__field admin-form__field--full">
                    <span>Datos de pago</span>
                    <textarea name="payment_details" rows="2">{{ old('payment_details', $seller->payment_details) }}</textarea>
                </label>
                <label class="admin-form__field admin-form__field--full">
                    <span>Notas</span>
                    <textarea name="notes" rows="2">{{ old('notes', $seller->notes) }}</textarea>
                </label>
            </div>
        </fieldset>

        <div class="admin-form__actions">
            <a href="{{ route('admin.sellers.index') }}" class="admin-btn admin-btn--ghost">Cancelar</a>
            <button type="submit" class="admin-btn admin-btn--primary">Guardar cambios</button>
        </div>
    </form>
</x-layouts.admin>
