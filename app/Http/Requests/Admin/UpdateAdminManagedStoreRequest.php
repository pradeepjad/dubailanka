<?php

namespace App\Http\Requests\Admin;

use App\Models\Store;
use App\Models\StoreChangeRequest;
use App\Models\StoreSlugHistory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateAdminManagedStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->platformAdmin?->status === 'active';
    }

    public function rules(): array
    {
        $store = $this->route('store');
        $storeId = $store instanceof Store ? $store->id : null;

        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'slug' => ['required', 'string', 'min:3', 'max:120', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('stores', 'slug')->ignore($storeId)],
            'country_code' => ['required', Rule::in(['AE', 'LK'])],
            'city' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'business_hours' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $slug = (string) $this->input('slug');
            $store = $this->route('store');
            $storeId = $store instanceof Store ? $store->id : null;

            if ($slug === '' || $validator->errors()->has('slug')) return;

            $historical = StoreSlugHistory::where('slug', $slug)->exists();
            $pending = StoreChangeRequest::where('proposed_slug', $slug)
                ->whereIn('status', ['pending', 'under_review', 'needs_changes'])
                ->when($storeId, fn ($q) => $q->where('store_id', '!=', $storeId))
                ->exists();

            if ($historical || $pending) {
                $validator->errors()->add('slug', 'This Store URL is already reserved or was previously used. Please choose another one.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter the Store name.',
            'slug.required' => 'Please choose a Store URL.',
            'slug.min' => 'Store URL must contain at least 3 characters.',
            'slug.regex' => 'Store URL can use letters, numbers and hyphens only.',
            'slug.unique' => 'This Store URL is already in use. Please choose another one.',
            'phone.required' => 'Please enter a Store phone number.',
            'website.url' => 'Please enter a full website address beginning with http:// or https://.',
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
            'email' => ($email = $clean('email')) ? mb_strtolower($email) : null,
            'website' => $clean('website'),
            'description' => $clean('description'),
            'business_hours' => $clean('business_hours'),
        ]);
    }
}
