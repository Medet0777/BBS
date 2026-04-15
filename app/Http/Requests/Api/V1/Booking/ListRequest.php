<?php

namespace App\Http\Requests\Api\V1\Booking;

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
     * @return array
     */
    public function rules(): array
    {
        return [
            'filter'   => 'nullable|string|in:upcoming,past',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
