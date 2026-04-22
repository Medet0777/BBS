<?php

namespace App\Http\Resources\Api\V1\Owner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int    $id
 * @property mixed  $scheduled_at
 * @property float  $total_price
 */
class BookingResource extends JsonResource
{

    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        $firstService = $this->services->first();

        return [
            'id'             => $this->id,
            'client_name'    => $this->user?->name,
            'barber_name'    => $this->barber?->name,
            'service_name'   => $firstService?->name,
            'services_count' => $this->services->count(),
            'scheduled_at'   => $this->scheduled_at,
            'total_price'    => (int) $this->total_price,
            'status'         => $this->status->value,
        ];
    }
}
