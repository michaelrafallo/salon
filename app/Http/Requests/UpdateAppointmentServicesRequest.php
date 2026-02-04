<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentServicesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->session()->has('salon_authenticated');
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'services' => ['required', 'array'],
            'services.*.service' => ['required', 'string', Rule::exists('service_categories', 'slug')],
            'services.*.service_id' => ['nullable', 'integer', Rule::exists('services', 'id')],
            'services.*.technician_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'technician'),
            ],
            'services.*.quantity' => ['nullable', 'integer', 'min:1'],
            'services.*.unit_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
