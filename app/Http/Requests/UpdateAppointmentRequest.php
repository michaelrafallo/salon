<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentRequest extends FormRequest
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
            'customer_id' => ['nullable', 'integer', Rule::exists('customers', 'id')],
            'appointment_datetime' => ['nullable', 'string', 'date'],
            'assigned_technician' => ['nullable', 'array'],
            'assigned_technician.*' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'technician'),
            ],
            'status' => ['nullable', 'string', Rule::in(['waiting', 'in-progress', 'completed', 'unpaid', 'paid', 'cancelled', 'refunded', 'closed', 'no-show'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'assigned_technician.required' => 'Please assign at least one technician.',
            'assigned_technician.*.exists' => 'One or more selected users are not valid technicians.',
        ];
    }
}
