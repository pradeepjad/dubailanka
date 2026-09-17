<?php

namespace App\Http\Requests\Seller;

use App\Models\Store;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $store = $this->route('store');
        $storeId = $store instanceof Store ? $store->id : null;

        return [
            'business_entity_id' => ['required', 'integer', 'exists:business_entities,id'],
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'slug' => ['required', 'string', 'min:3', 'max:120', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('stores', 'slug')->ignore($storeId)],
            'country_code' => ['required', 'string', 'size:2', Rule::in(['AE', 'LK'])],
            'city' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'business_hours' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $clean = fn (string $key): ?string => ($value = trim((string) $this->input($key))) !== '' ? $value : null;

        $this->merge([
            'name' => $clean('name'),
            'slug' => strtolower((string) $clean('slug')),
            'country_code' => strtoupper((string) $clean('country_code')),
            'city' => $clean('city'),
            'address' => $clean('address'),
            'phone' => $clean('phone'),
            'whatsapp' => $clean('whatsapp'),
            'email' => mb_strtolower((string) $clean('email')),
            'website' => $clean('website'),
            'description' => $clean('description'),
            'business_hours' => $clean('business_hours'),
        ]);
    }

    public function messages(): array
    {
        return [
            'business_entity_id.required' => 'Please choose the business that owns this store.',
            'name.required' => 'Please enter your store name.',
            'slug.required' => 'Please choose your unique store address.',
            'slug.regex' => 'Store address can use lowercase letters, numbers and hyphens only.',
            'slug.unique' => 'This store address is already taken. Please try another one.',
            'country_code.in' => 'Please select UAE or Sri Lanka for the current launch.',
            'phone.required' => 'Please enter a store phone number.',
            'email.required' => 'Please enter a store email address.',
            'website.url' => 'Please enter a full website address beginning with http:// or https://.',
            'logo.max' => 'Store logo must be 3 MB or smaller.',
            'cover.max' => 'Cover image must be 5 MB or smaller.',
        ];
    }
}
