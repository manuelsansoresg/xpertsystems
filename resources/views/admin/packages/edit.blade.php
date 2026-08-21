<x-layouts.admin title="Editar paquete">
    <section class="admin-crud-head">
        <div>
            <span class="admin-eyebrow">Comercial > Paquetes</span>
            <h1>Editar paquete</h1>
            <p>{{ $package->name }}</p>
        </div>
        <a href="{{ route('admin.packages.index') }}" class="admin-btn admin-btn--ghost">← Volver</a>
    </section>

    @php
        $features = old('features', $package->featureItems
            ->map(fn ($feature) => [
                'title' => $feature->title,
                'visible_summary' => $feature->visible_summary,
            ])
            ->values()
            ->all());
    @endphp

    <form method="POST" action="{{ route('admin.packages.update', $package) }}" class="admin-form" x-data="packageForm({{ Js::from($features) }})">
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
                    <span>Nombre <em>*</em></span>
                    <input type="text" name="name" value="{{ old('name', $package->name) }}" required>
                    @error('name')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Slug</span>
                    <input type="text" name="slug" value="{{ old('slug', $package->slug) }}">
                    @error('slug')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field admin-form__field--full">
                    <span>Descripción corta <em>*</em></span>
                    <textarea name="short_description" rows="2" required>{{ old('short_description', $package->short_description) }}</textarea>
                    @error('short_description')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field admin-form__field--full">
                    <span>Descripción larga</span>
                    <textarea name="long_description" rows="4">{{ old('long_description', $package->long_description) }}</textarea>
                </label>
            </div>
        </fieldset>

        <fieldset class="admin-form__section">
            <legend>Precio</legend>
            <div class="admin-form__grid">
                <label class="admin-form__field">
                    <span>Tipo de precio <em>*</em></span>
                    <select name="price_type" required>
                        <option value="">Selecciona</option>
                        <option value="fixed" @selected(old('price_type', $package->price_type) === 'fixed')>Fijo</option>
                        <option value="starting_at" @selected(old('price_type', $package->price_type) === 'starting_at')>Desde</option>
                        <option value="quote" @selected(old('price_type', $package->price_type) === 'quote')>Cotizar</option>
                    </select>
                    @error('price_type')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Precio</span>
                    <input type="number" name="price" value="{{ old('price', $package->price) }}" step="0.01" min="0">
                    @error('price')<small class="admin-field-error">{{ $message }}</small>@enderror
                </label>
                <label class="admin-form__field">
                    <span>Moneda</span>
                    <input type="text" name="currency" value="{{ old('currency', $package->currency) }}" maxlength="3">
                </label>
                <label class="admin-form__field">
                    <span>Texto del botón</span>
                    <input type="text" name="button_text" value="{{ old('button_text', $package->button_text) }}">
                </label>
            </div>
        </fieldset>

        <fieldset class="admin-form__section">
            <legend>Publicación</legend>
            <div class="admin-form__grid">
                <label class="admin-form__field admin-form__field--check">
                    <input type="checkbox" name="active" value="1" @checked(old('active', $package->active))>
                    <span>Activo</span>
                </label>
                <label class="admin-form__field admin-form__field--check">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $package->is_featured))>
                    <span>Destacado</span>
                </label>
                <label class="admin-form__field admin-form__field--check">
                    <input type="checkbox" name="public_visibility" value="1" @checked(old('public_visibility', $package->public_visibility))>
                    <span>Visible públicamente</span>
                </label>
                <label class="admin-form__field">
                    <span>Orden</span>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $package->sort_order) }}" min="0">
                </label>
            </div>
        </fieldset>

        <fieldset class="admin-form__section">
            <legend>Renovación</legend>
            <div class="admin-form__grid">
                <label class="admin-form__field admin-form__field--check">
                    <input type="checkbox" name="renewal_enabled" value="1" x-model="renewalEnabled" @change="toggleRenewal()" @checked(old('renewal_enabled', $package->renewal_enabled))>
                    <span>Requiere renovación</span>
                </label>
                <label class="admin-form__field" x-show="renewalEnabled" x-cloak>
                    <span>Precio de renovación</span>
                    <input type="number" name="renewal_price" value="{{ old('renewal_price', $package->renewal_price) }}" step="0.01" min="0">
                </label>
                <label class="admin-form__field" x-show="renewalEnabled" x-cloak>
                    <span>Periodo</span>
                    <select name="renewal_period">
                        <option value="yearly" @selected(old('renewal_period', $package->renewal_period) === 'yearly')>Anual</option>
                    </select>
                </label>
                <label class="admin-form__field" x-show="renewalEnabled" x-cloak>
                    <span>Renueva después de (meses)</span>
                    <input type="number" name="renewal_after_months" value="{{ old('renewal_after_months', $package->renewal_after_months ?? 12) }}" min="1">
                </label>
                <label class="admin-form__field admin-form__field--check" x-show="renewalEnabled" x-cloak>
                    <input type="checkbox" name="show_renewal_price" value="1" @checked(old('show_renewal_price', $package->show_renewal_price))>
                    <span>Mostrar precio públicamente</span>
                </label>
                <label class="admin-form__field admin-form__field--full" x-show="renewalEnabled" x-cloak>
                    <span>Texto público de renovación</span>
                    <textarea name="renewal_public_text" rows="2">{{ old('renewal_public_text', $package->renewal_public_text) }}</textarea>
                </label>
            </div>
        </fieldset>

        <fieldset class="admin-form__section">
            <legend>Características</legend>
            <div class="admin-form__features">
                <template x-for="(feature, index) in features" :key="index">
                    <div class="admin-form__feature-row">
                        <input type="text" x-model="feature.title" :name="'features['+index+'][title]'" placeholder="Característica" style="flex: 2">
                        <label class="admin-form__feature-check">
                            <input type="checkbox" x-model="feature.visible_summary" :name="'features['+index+'][visible_summary]'" value="1">
                            <span>Resumen</span>
                        </label>
                        <button type="button" @click="removeFeature(index)" class="admin-btn admin-btn--ghost">✕</button>
                    </div>
                </template>
                <button type="button" @click="addFeature()" class="admin-btn admin-btn--ghost">+ Agregar característica</button>
            </div>
        </fieldset>

        <div class="admin-form__actions">
            <a href="{{ route('admin.packages.index') }}" class="admin-btn admin-btn--ghost">Cancelar</a>
            <button type="submit" class="admin-btn admin-btn--primary">Guardar cambios</button>
        </div>
    </form>
</x-layouts.admin>
