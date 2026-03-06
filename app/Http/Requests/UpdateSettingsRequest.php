<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
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
            'settings' => ['required', 'array'],
            'settings.commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'settings.points_unit_value' => ['nullable', 'numeric', 'min:0'],
            'settings.points_per_unit' => ['nullable', 'numeric', 'min:0'],
            'settings.reward_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'settings.timezone' => ['nullable', 'string', Rule::in(timezone_identifiers_list())],
            'settings.*' => ['nullable'],
        ];
    }
}
