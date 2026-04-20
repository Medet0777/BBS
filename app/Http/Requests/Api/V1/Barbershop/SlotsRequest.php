<?php

namespace App\Http\Requests\Api\V1\Barbershop;

use Illuminate\Foundation\Http\FormRequest;

class SlotsRequest extends FormRequest
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
            'date'       => 'required|date_format:Y-m-d|after_or_equal:today',
            'barber_id'  => 'nullable|integer|exists:barbers,id',
            'service_id' => 'nullable|integer|exists:services,id',
        ];
    }
}
