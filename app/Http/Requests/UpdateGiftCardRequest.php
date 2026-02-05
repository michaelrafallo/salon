<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGiftCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->session()->has('salon_authenticated');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $giftCard = $this->route('gift_card');
        $giftCardId = $giftCard ? $giftCard->id : null;

        return [
            'code' => ['sometimes', 'string', 'max:64', Rule::unique('gift_cards', 'code')->ignore($giftCardId)],
            'description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pin' => ['sometimes', 'nullable', 'string', 'max:16'],
            'initial_value' => ['sometimes', 'numeric', 'min:0'],
            'balance' => ['sometimes', 'numeric', 'min:0'],
            'active' => ['sometimes', Rule::in([true, false, 1, 0, '1', '0'])],
        ];
    }
}
