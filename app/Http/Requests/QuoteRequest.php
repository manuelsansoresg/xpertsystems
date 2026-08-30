<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $fields = collect($this->only(['name', 'email', 'whatsapp', 'business_name', 'message']))
            ->map(fn ($value) => is_string($value) ? trim(strip_tags($value)) : $value)->all();
        $this->merge($fields);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'whatsapp' => ['required', 'string', 'regex:/^[0-9+()\-\s]{8,24}$/'],
            'business_name' => ['nullable', 'string', 'max:140'],
            'message' => ['nullable', 'string', 'max:1200'],
            'website' => ['nullable', 'prohibited'],
        ];
    }
}
