<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(['admin', 'seller'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'active' => ['nullable', 'boolean'],
        ];

        if ($this->input('role') === 'seller') {
            $rules['referral_code'] = [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9]+$/',
                'unique:seller_profiles,referral_code',
            ];
            $rules['commission_type'] = ['required', Rule::in(['percentage', 'fixed'])];
            $rules['commission_value'] = [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($this->input('commission_type') === 'percentage' && $value > 100) {
                        $fail('El porcentaje no puede superar 100.');
                    }
                    if ($value <= 0) {
                        $fail('El valor debe ser mayor a 0.');
                    }
                },
            ];
            $rules['payment_method'] = ['nullable', 'string', 'max:120'];
            $rules['payment_details'] = ['nullable', 'string', 'max:500'];
            $rules['notes'] = ['nullable', 'string', 'max:1000'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Escribe un correo electrónico válido.',
            'email.unique' => 'Este correo ya está registrado.',
            'role.required' => 'Selecciona un rol.',
            'role.in' => 'El rol debe ser admin o vendedor.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'referral_code.required' => 'El código de referido es obligatorio para vendedores.',
            'referral_code.unique' => 'Este código de referido ya existe.',
            'referral_code.regex' => 'El código solo puede contener letras mayúsculas y números.',
            'commission_type.required' => 'Selecciona un tipo de comisión.',
            'commission_value.required' => 'Define el valor de comisión.',
        ];
    }
}
