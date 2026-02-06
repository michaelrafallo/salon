<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'type' => ['nullable', 'string', 'in:walk-in,booked'],
            'status' => ['nullable', 'string', 'in:waiting,in-progress,completed'],
            'appointment_datetime' => ['nullable', 'date', 'required_if:type,booked'],
            'assigned_technician' => ['nullable', 'array'],
            'assigned_technician.*' => ['integer', 'exists:users,id'],
        ];
    }
}
