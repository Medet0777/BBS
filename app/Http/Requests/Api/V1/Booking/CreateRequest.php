<?php

namespace App\Http\Requests\Api\V1\Booking;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            'barbershop_id'    => 'required|integer|exists:barbershops,id',
            'barber_id'        => 'required|integer|exists:barbers,id',
            'scheduled_at'     => 'required|date|after:now',
            'service_ids'      => 'required|array|min:1',
            'service_ids.*'    => 'integer|exists:services,id',
            'comment'          => 'nullable|string|max:1000',
            'reminder_enabled' => 'nullable|boolean',
        ];
    }
}
