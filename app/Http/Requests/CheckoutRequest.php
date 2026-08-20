<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $clean = fn ($value) => is_string($value) ? trim(strip_tags($value)) : $value;
        $this->merge(collect($this->only(['name', 'email', 'whatsapp', 'country', 'business_name']))
            ->map($clean)->all());
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'whatsapp' => ['required', 'string', 'regex:/^[0-9+()\-\s]{8,24}$/'],
            'country' => ['required', Rule::in(['MX'])],
            'business_name' => ['nullable', 'string', 'min:2', 'max:140'],
            'website' => ['nullable', 'prohibited'],
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'terms.accepted' => 'Necesitamos que aceptes el aviso de privacidad y los términos.',
            'whatsapp.regex' => 'Escribe un número de WhatsApp válido.',
        ];
    }
}
