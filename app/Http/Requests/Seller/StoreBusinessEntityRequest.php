<?php

namespace App\Http\Requests\Seller;

use App\Models\BusinessEntity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBusinessEntityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $registered = $this->input('type') === BusinessEntity::TYPE_REGISTERED;

        return [
            'type' => ['required', Rule::in([
                BusinessEntity::TYPE_PERSONAL,
                BusinessEntity::TYPE_REGISTERED,
            ])],
            'legal_name' => ['required', 'string', 'min:2', 'max:150'],
            'trading_name' => ['nullable', 'string', 'max:150'],
            'registration_number' => [
                Rule::requiredIf($registered),
                'nullable',
                'string',
                'max:100',
            ],
            'country_code' => ['required', 'string', 'size:2', Rule::in(['AE', 'LK'])],
            'address' => [$registered ? 'required' : 'nullable', 'nullable', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $clean = fn (string $key): ?string => ($value = trim((string) $this->input($key))) !== '' ? $value : null;

        $this->merge([
            'type' => $clean('type'),
            'legal_name' => $clean('legal_name'),
            'trading_name' => $clean('trading_name'),
            'registration_number' => $clean('registration_number'),
            'country_code' => strtoupper((string) $clean('country_code')),
            'address' => $clean('address'),
            'phone' => $clean('phone'),
            'whatsapp' => $clean('whatsapp'),
            'email' => mb_strtolower((string) $clean('email')),
        ]);
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Please choose how you are selling on Dubai Lanka.',
            'type.in' => 'Please choose Personal Business or Registered Company.',
            'legal_name.required' => 'Please enter your business name.',
            'legal_name.min' => 'Business name must be at least 2 characters.',
            'registration_number.required' => 'Please enter the company registration number.',
            'country_code.required' => 'Please select your business country.',
            'country_code.in' => 'Please select UAE or Sri Lanka for the current launch.',
            'address.required' => 'Please enter the registered business address.',
            'phone.required' => 'Please enter a business phone number.',
            'email.required' => 'Please enter a business email address.',
            'email.email' => 'Please enter a valid business email address.',
        ];
    }
}
