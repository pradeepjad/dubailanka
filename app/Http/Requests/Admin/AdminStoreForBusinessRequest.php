<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminStoreForBusinessRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->platformAdmin?->status === 'active'; }

    public function rules(): array
    {
        return [
            'store_name' => ['required','string','min:2','max:150'],
            'store_slug' => ['required','string','min:3','max:120','regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/','unique:stores,slug'],
            'store_country_code' => ['required', Rule::in(['AE','LK'])],
            'store_city' => ['nullable','string','max:120'],
            'store_address' => ['nullable','string','max:500'],
            'store_phone' => ['required','string','max:30'],
            'store_whatsapp' => ['nullable','string','max:30'],
            'store_email' => ['nullable','email','max:255'],
            'store_website' => ['nullable','url:http,https','max:255'],
            'store_description' => ['nullable','string','max:2000'],
            'store_business_hours' => ['nullable','string','max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('store_email')) $this->merge(['store_email' => mb_strtolower(trim((string)$this->input('store_email')))]);
        $this->merge(['store_country_code' => strtoupper(trim((string)$this->input('store_country_code')))]);
        if ($this->filled('store_slug')) $this->merge(['store_slug' => strtolower(trim((string)$this->input('store_slug')))]);
    }
}
