<x-layouts.admin title="Editar cupón">
    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Comercial > Cupones</span>
            <h1>Editar {{ $coupon->code }}</h1>
            <p>{{ $coupon->name }}</p>
        </div>
        <div class="admin-crud-head__actions">
            <a href="{{ route('admin.coupons.show', $coupon) }}" class="admin-btn admin-btn--ghost">Ver detalle</a>
            <a href="{{ route('admin.coupons.index') }}" class="admin-btn admin-btn--ghost"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Volver</a>
        </div>
    </section>

    <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}" class="admin-form" x-data="{
        discountType: '{{ old('discount_type', $coupon->discount_type->value) }}',
        scope: '{{ old('scope', $coupon->scope->value) }}'
    }">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="admin-toast admin-toast--error" role="alert">
                Revisa los campos marcados.
            </div>
        @endif

        <fieldset class="admin-form__section">
            <legend>Información general</legend>
            <div class="admin-form__grid">
                <label class="admin-form__field">
                    <span>Código <em>*</em></span>
                    <input type="text" name="code" value="{{ old('code', $coupon->code) }}" required style="text-transform:uppercase">
                    @error('code')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Nombre interno <em>*</em></span>
                    <input type="text" name="name" value="{{ old('name', $coupon->name) }}" required>
                    @error('name')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field admin-form__field--full">
                    <span>Descripción</span>
                    <textarea name="description" rows="2">{{ old('description', $coupon->description) }}</textarea>
                </label>
            </div>
        </fieldset>

        <fieldset class="admin-form__section">
            <legend>Descuento</legend>
            <div class="admin-form__grid">
                <label class="admin-form__field">
                    <span>Tipo <em>*</em></span>
                    <select name="discount_type" x-model="discountType" required>
                        <option value="percentage">Porcentaje</option>
                        <option value="fixed">Monto fijo</option>
                    </select>
                    @error('discount_type')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Valor <em>*</em></span>
                    <input type="number" name="discount_value" value="{{ old('discount_value', $coupon->discount_value) }}" required min="0.01" step="0.01">
                    @error('discount_value')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field" x-show="discountType === 'percentage'">
                    <span>Descuento máximo</span>
                    <input type="number" name="maximum_discount" value="{{ old('maximum_discount', $coupon->maximum_discount) }}" min="0" step="0.01">
                    @error('maximum_discount')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Compra mínima</span>
                    <input type="number" name="minimum_amount" value="{{ old('minimum_amount', $coupon->minimum_amount) }}" min="0" step="0.01">
                    @error('minimum_amount')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
            </div>
        </fieldset>

        <fieldset class="admin-form__section">
            <legend>Aplicación</legend>
            <div class="admin-form__grid">
                <label class="admin-form__field">
                    <span>Aplica a <em>*</em></span>
                    <select name="scope" x-model="scope" required>
                        <option value="global">Todos los paquetes</option>
                        <option value="packages">Paquetes específicos</option>
                    </select>
                    @error('scope')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <div class="admin-form__field admin-form__field--full" x-show="scope === 'packages'" x-cloak>
                    <span>Seleccionar paquetes <em>*</em></span>
                    <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.5rem">
                        @foreach($packages as $package)
                            <label style="display:flex;align-items:center;gap:.35rem;padding:.4rem .7rem;border:1px solid var(--border);border-radius:6px;cursor:pointer;font-size:.82rem">
                                <input type="checkbox" name="package_ids[]" value="{{ $package->id }}" @checked(in_array($package->id, old('package_ids', $selectedPackageIds)))>
                                {{ $package->name }}
                            </label>
                        @endforeach
                    </div>
                    @error('package_ids')<small class="admin-field-error">{{ $message }}</small>@enderror
                </div>
            </div>
        </fieldset>

        <fieldset class="admin-form__section">
            <legend>Asignación comercial</legend>
            <div class="admin-form__grid">
                <label class="admin-form__field">
                    <span>Vendedor asociado</span>
                    <select name="seller_id">
                        <option value="">Cupón general</option>
                        @foreach($sellers as $seller)
                            <option value="{{ $seller->id }}" @selected(old('seller_id', $coupon->seller_id) == $seller->id)>
                                {{ $seller->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('seller_id')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
            </div>
        </fieldset>

        <fieldset class="admin-form__section">
            <legend>Vigencia y límites</legend>
            <div class="admin-form__grid">
                <label class="admin-form__field">
                    <span>Fecha de inicio</span>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}">
                    @error('starts_at')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Fecha de vencimiento</span>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}">
                    @error('expires_at')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Límite total de usos</span>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1">
                    @error('usage_limit')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Límite por cliente</span>
                    <input type="number" name="usage_limit_per_customer" value="{{ old('usage_limit_per_customer', $coupon->usage_limit_per_customer) }}" min="1">
                    @error('usage_limit_per_customer')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field" style="display:flex;align-items:center;gap:.5rem;padding-top:1.5rem">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active)) id="is_active">
                    <label for="is_active" style="font-size:.85rem;cursor:pointer">Cupón activo</label>
                </label>
            </div>
        </fieldset>

        <div class="admin-form__actions">
            <a href="{{ route('admin.coupons.index') }}" class="admin-btn admin-btn--ghost">Cancelar</a>
            <button type="submit" class="admin-btn admin-btn--primary">Guardar cambios</button>
        </div>
    </form>
</x-layouts.admin>
