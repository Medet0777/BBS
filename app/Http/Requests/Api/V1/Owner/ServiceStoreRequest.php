<?php

namespace App\Http\Requests\Api\V1\Owner;

use Illuminate\Foundation\Http\FormRequest;

class ServiceStoreRequest extends FormRequest
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
            'name'             => 'required|string|max:100',
            'category_name'    => 'required|string|max:100',
            'price'            => 'required|integer|min:0',
            'duration_minutes' => 'required|integer|min:5',
        ];
    }
}
