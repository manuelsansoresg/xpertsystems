<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StorePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:packages,slug'],
            'short_description' => ['required', 'string'],
            'long_description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'price_type' => ['required', Rule::in(['fixed', 'starting_at', 'quote'])],
            'direct_checkout' => ['nullable', 'boolean'],
            'requires_quote' => ['nullable', 'boolean'],
            'deposit_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'featured' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'badge' => ['nullable', 'string', 'max:120'],
            'note' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
            'button_text' => ['nullable', 'string', 'max:120'],
            'public_visibility' => ['nullable', 'boolean'],
            'renewal_required' => ['nullable', 'boolean'],
            'renewal_enabled' => ['nullable', 'boolean'],
            'renewal_price' => ['nullable', 'numeric', 'min:0'],
            'renewal_period' => ['nullable', Rule::in(['yearly', 'monthly', 'semiannual'])],
            'renewal_after_months' => ['nullable', 'integer', 'min:1'],
            'renewal_includes' => ['nullable', 'array'],
            'renewal_includes.*' => ['string'],
            'renewal_public_text' => ['nullable', 'string'],
            'show_renewal_price' => ['nullable', 'boolean'],
            'features' => ['nullable', 'array'],
            'features.*.title' => ['required', 'string', 'max:255'],
            'features.*.description' => ['nullable', 'string'],
            'features.*.visible_summary' => ['nullable', 'boolean'],
        ];

        if ($this->input('price_type') !== 'quote') {
            $rules['price'][] = 'required';
            $rules['price'][] = 'gt:0';
        }

        if ($this->boolean('renewal_enabled')) {
            $rules['renewal_price'][] = 'required';
            $rules['renewal_price'][] = 'gt:0';
            $rules['renewal_period'][] = 'required';
            $rules['renewal_after_months'][] = 'required';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'slug.unique' => 'El slug ya existe.',
            'short_description.required' => 'La descripción corta es obligatoria.',
            'price.required' => 'El precio es obligatorio para este tipo.',
            'price.gt' => 'El precio debe ser mayor a 0.',
            'renewal_price.required' => 'El precio de renovación es obligatorio cuando está activado.',
            'renewal_period.required' => 'El periodo de renovación es obligatorio.',
        ];
    }
}
