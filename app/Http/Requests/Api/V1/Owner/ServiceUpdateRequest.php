<?php

namespace App\Http\Requests\Api\V1\Owner;

use Illuminate\Foundation\Http\FormRequest;

class ServiceUpdateRequest extends FormRequest
{

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'             => 'nullable|string|max:100',
            'category_name'    => 'nullable|string|max:100',
            'price'            => 'nullable|integer|min:0',
            'duration_minutes' => 'nullable|integer|min:5',
        ];
    }
}
