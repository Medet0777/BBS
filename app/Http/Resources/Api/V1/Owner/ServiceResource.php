<?php

namespace App\Http\Resources\Api\V1\Owner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int         $id
 * @property string      $name
 * @property int         $service_category_id
 * @property float       $price
 * @property int         $duration_minutes
 */
class ServiceResource extends JsonResource
{

    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'category_id'      => $this->service_category_id,
            'category_name'    => $this->serviceCategory?->name,
            'price'            => (int) $this->price,
            'duration_minutes' => $this->duration_minutes,
        ];
    }
}
