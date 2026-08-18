<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'max:2'],
            'currency' => ['nullable', 'string', 'max:3'],
            'seller_id' => ['nullable', 'exists:users,id', function ($attribute, $value, $fail) {
                if ($value && !\App\Models\User::query()->where('id', $value)->whereHas('roles', fn ($q) => $q->where('slug', 'seller'))->exists()) {
                    $fail('El usuario seleccionado no es un vendedor.');
                }
            }],
            'referral_code' => ['nullable', 'string', 'max:20'],
            'source' => ['required', Rule::in(['direct', 'referral', 'coupon', 'whatsapp', 'facebook', 'instagram', 'google', 'email', 'other'])],
            'status' => ['required', Rule::in(['lead', 'customer', 'inactive'])],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'El nombre es obligatorio.',
            'email.email' => 'Escribe un correo electrónico válido.',
            'seller_id.exists' => 'El vendedor seleccionado no existe.',
            'source.required' => 'Selecciona un origen.',
            'source.in' => 'El origen seleccionado no es válido.',
            'status.required' => 'Selecciona un estado.',
            'status.in' => 'El estado seleccionado no es válido.',
        ];
    }
}
