<?php

namespace App\Http\Requests\Api\V1\Barbershop;

use Illuminate\Foundation\Http\FormRequest;

class ListRequest extends FormRequest
{

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_open')) {
            $this->merge([
                'is_open' => filter_var($this->input('is_open'), FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'order_by' => 'nullable|string|in:rating,distance',
            'per_page' => 'nullable|integer|min:1|max:500',
            'user_lat' => 'nullable|numeric|between:-90,90|required_with:user_lng',
            'user_lng' => 'nullable|numeric|between:-180,180|required_with:user_lat',
            'is_open'  => 'nullable|boolean',
            'search'   => 'nullable|string|max:255',
        ];
    }
}
