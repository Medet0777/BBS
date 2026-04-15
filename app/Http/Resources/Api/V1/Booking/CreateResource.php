<?php

namespace App\Http\Resources\Api\V1\Booking;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int         $id
 * @property mixed       $scheduled_at
 * @property float       $total_price
 * @property int         $total_duration_minutes
 * @property string|null $comment
 * @property bool        $reminder_enabled
 */
class CreateResource extends JsonResource
{

    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'barbershop_name'        => $this->barbershop?->name,
            'barbershop_address'     => $this->barbershop?->address,
            'barber_name'            => $this->barber?->name,
            'scheduled_at'           => $this->scheduled_at,
            'status'                 => $this->status->value,
            'services'               => $this->services->map(fn ($service) => [
                'id'               => $service->id,
                'name'             => $service->name,
                'price'            => $service->pivot->price_snapshot,
                'duration_minutes' => $service->pivot->duration_snapshot,
            ]),
            'total_price'            => $this->total_price,
            'total_duration_minutes' => $this->total_duration_minutes,
            'comment'                => $this->comment,
            'reminder_enabled'       => $this->reminder_enabled,
        ];
    }
}
