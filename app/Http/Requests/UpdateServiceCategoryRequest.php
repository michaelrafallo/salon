<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceCategoryRequest extends FormRequest
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
        $categoryId = $this->route('service_category')?->id;

        return [
            'name' => ['sometimes', 'string', 'max:255', 'unique:service_categories,name,' . $categoryId],
        ];
    }
}
