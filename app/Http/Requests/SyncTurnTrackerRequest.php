<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncTurnTrackerRequest extends FormRequest
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
            'entries' => ['required', 'array'],
            'entries.*.user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'technician'),
            ],
            'entries.*.services' => ['required', 'integer', 'min:0'],
        ];
    }
}
