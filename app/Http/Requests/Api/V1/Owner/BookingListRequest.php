<?php

namespace App\Http\Requests\Api\V1\Owner;

use Illuminate\Foundation\Http\FormRequest;

class BookingListRequest extends FormRequest
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
            'filter'   => 'nullable|string|in:all,pending,confirmed,cancelled,completed',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
