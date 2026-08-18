<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\CouponScope;
use App\Enums\DiscountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9\-]+$/',
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\Coupon::query()
                        ->whereRaw('UPPER(code) = ?', [strtoupper($value)])
                        ->exists();

                    if ($exists) {
                        $fail('El código del cupón ya existe.');
                    }
                },
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'discount_type' => ['required', Rule::enum(DiscountType::class)],
            'discount_value' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    if ($this->input('discount_type') === DiscountType::Percentage->value && $value > 100) {
                        $fail('El porcentaje no puede ser mayor a 100.');
                    }
                },
            ],
            'scope' => ['required', Rule::enum(CouponScope::class)],
            'package_ids' => [
                'required_if:scope,' . CouponScope::Packages->value,
                'array',
                function ($attribute, $value, $fail) {
                    if ($this->input('scope') === CouponScope::Packages->value && empty($value)) {
                        $fail('Debes seleccionar al menos un paquete.');
                    }
                },
            ],
            'package_ids.*' => ['integer', 'exists:packages,id'],
            'seller_id' => [
                'nullable',
                'integer',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if ($value && !\App\Models\User::query()
                        ->where('id', $value)
                        ->whereHas('roles', fn ($q) => $q->where('slug', 'seller'))
                        ->exists()) {
                        $fail('El usuario seleccionado no es un vendedor.');
                    }
                },
            ],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => [
                'nullable',
                'date',
                'after_or_equal:starts_at',
                function ($attribute, $value, $fail) {
                    if ($this->input('starts_at') && $value < $this->input('starts_at')) {
                        $fail('La fecha de vencimiento debe ser posterior a la fecha de inicio.');
                    }
                },
            ],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'usage_limit_per_customer' => ['nullable', 'integer', 'min:1'],
            'minimum_amount' => ['nullable', 'numeric', 'min:0'],
            'maximum_discount' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($this->input('discount_type') === DiscountType::Fixed->value && $value) {
                        $fail('El descuento máximo solo aplica para cupones porcentuales.');
                    }
                },
            ],
            'is_active' => ['boolean'],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated($key, $default);

        if (isset($data['code'])) {
            $data['code'] = strtoupper(trim($data['code']));
        }

        if (isset($data['discount_type'])) {
            $data['discount_type'] = DiscountType::from($data['discount_type']);
        }

        if (isset($data['scope'])) {
            $data['scope'] = CouponScope::from($data['scope']);
        }

        return $data;
    }
}
