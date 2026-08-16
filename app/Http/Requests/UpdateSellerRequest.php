<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateSellerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $sellerProfile = $this->route('seller');

        return [
            'name' => ['required', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'email' => [
                'required',
                'email:rfc',
                'max:255',
                Rule::unique('users')->ignore($sellerProfile?->user_id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'active' => ['nullable', 'boolean'],
            'referral_code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9]+$/',
                Rule::unique('seller_profiles', 'referral_code')->ignore($sellerProfile?->id),
            ],
            'commission_type' => ['required', Rule::in(['percentage', 'fixed'])],
            'commission_value' => ['required', 'numeric', 'min:0', 'max:10000'],
            'payment_method' => ['nullable', 'string', 'max:120'],
            'payment_details' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Escribe un correo electrónico válido.',
            'email.unique' => 'Este correo ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'referral_code.required' => 'El código de referido es obligatorio.',
            'referral_code.unique' => 'Este código de referido ya existe.',
            'referral_code.regex' => 'El código solo puede contener letras mayúsculas y números.',
            'commission_type.required' => 'Selecciona un tipo de comisión.',
            'commission_value.required' => 'Define el valor de comisión.',
        ];
    }
}
