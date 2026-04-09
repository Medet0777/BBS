<?php

namespace App\Http\Resources\Api\V1\Barbershop;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int    $id
 * @property string $name
 * @property string $slug
 * @property string $logo
 * @property string $address
 * @property float  $rating
 * @property string $opens_at
 * @property string $closes_at
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
            'id'        => $this->id,
            'name'      => $this->name,
            'slug'      => $this->slug,
            'logo'      => $this->logo,
            'address'   => $this->address,
            'rating'    => $this->rating,
            'opens_at'  => $this->opens_at,
            'closes_at' => $this->closes_at,
        ];
    }
}
