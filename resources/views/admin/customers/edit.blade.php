<x-layouts.admin title="Editar cliente">
    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Comercial > Clientes</span>
            <h1>Editar cliente</h1>
            <p>{{ $customer->first_name }} {{ $customer->last_name }}</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="admin-btn admin-btn--ghost"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Volver</a>
    </section>

    <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="admin-form">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="admin-toast admin-toast--error" role="alert">
                Revisa los campos marcados.
            </div>
        @endif

        <fieldset class="admin-form__section">
            <legend>Datos principales</legend>
            <div class="admin-form__grid">
                <label class="admin-form__field">
                    <span>Nombre <em>*</em></span>
                    <input type="text" name="first_name" value="{{ old('first_name', $customer->first_name) }}" required>
                    @error('first_name')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Apellidos</span>
                    <input type="text" name="last_name" value="{{ old('last_name', $customer->last_name) }}">
                </label>
                <label class="admin-form__field">
                    <span>Nombre del negocio</span>
                    <input type="text" name="business_name" value="{{ old('business_name', $customer->business_name) }}">
                </label>
                <label class="admin-form__field">
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}">
                    @error('email')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Teléfono</span>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}">
                </label>
            </div>
        </fieldset>

        <fieldset class="admin-form__section">
            <legend>Ubicación</legend>
            <div class="admin-form__grid">
                <label class="admin-form__field">
                    <span>País</span>
                    <input type="text" name="country" value="{{ old('country', $customer->country) }}" maxlength="2">
                </label>
                <label class="admin-form__field">
                    <span>Moneda</span>
                    <input type="text" name="currency" value="{{ old('currency', $customer->currency) }}" maxlength="3">
                </label>
            </div>
        </fieldset>

        <fieldset class="admin-form__section">
            <legend>Información comercial</legend>
            <div class="admin-form__grid">
                <label class="admin-form__field">
                    <span>Estado <em>*</em></span>
                    <select name="status" required>
                        <option value="lead" @selected(old('status', $customer->status) === 'lead')>Prospecto</option>
                        <option value="customer" @selected(old('status', $customer->status) === 'customer')>Cliente</option>
                        <option value="inactive" @selected(old('status', $customer->status) === 'inactive')>Inactivo</option>
                    </select>
                    @error('status')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Origen <em>*</em></span>
                    <select name="source" required>
                        <option value="direct" @selected(old('source', $customer->source) === 'direct')>Directo</option>
                        <option value="referral" @selected(old('source', $customer->source) === 'referral')>Referido</option>
                        <option value="coupon" @selected(old('source', $customer->source) === 'coupon')>Cupón</option>
                        <option value="whatsapp" @selected(old('source', $customer->source) === 'whatsapp')>WhatsApp</option>
                        <option value="facebook" @selected(old('source', $customer->source) === 'facebook')>Facebook</option>
                        <option value="instagram" @selected(old('source', $customer->source) === 'instagram')>Instagram</option>
                        <option value="google" @selected(old('source', $customer->source) === 'google')>Google</option>
                        <option value="email" @selected(old('source', $customer->source) === 'email')>Correo</option>
                        <option value="other" @selected(old('source', $customer->source) === 'other')>Otro</option>
                    </select>
                    @error('source')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Vendedor asociado</span>
                    <select name="seller_id">
                        <option value="">Sin vendedor / Venta directa</option>
                        @foreach($sellers as $seller)
                            <option value="{{ $seller->id }}" @selected(old('seller_id', $customer->seller_id) == $seller->id)>{{ $seller->name }}</option>
                        @endforeach
                    </select>
                    @error('seller_id')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Código de referido</span>
                    <input type="text" name="referral_code" value="{{ old('referral_code', $customer->referral_code) }}">
                </label>
                <label class="admin-form__field admin-form__field--full">
                    <span>Notas internas</span>
                    <textarea name="notes" rows="3">{{ old('notes', $customer->notes) }}</textarea>
                </label>
            </div>
        </fieldset>

        <div class="admin-form__actions">
            <a href="{{ route('admin.customers.index') }}" class="admin-btn admin-btn--ghost">Cancelar</a>
            <button type="submit" class="admin-btn admin-btn--primary">Guardar cambios</button>
        </div>
    </form>
</x-layouts.admin>
