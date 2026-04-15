<?php

namespace App\Http\Resources\Api\V1\Booking;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int   $id
 * @property mixed $scheduled_at
 * @property float $total_price
 */
class ListResource extends JsonResource
{

    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'barbershop_name' => $this->barbershop?->name,
            'barbershop_logo' => $this->barbershop?->logo,
            'barber_name'     => $this->barber?->name,
            'scheduled_at'    => $this->scheduled_at,
            'total_price'     => $this->total_price,
            'status'          => $this->status->value,
        ];
    }
}
