<?php

namespace App\Http\Resources\Api\V1\Barbershop;

use App\Http\Resources\Api\V1\Service\ServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int    $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $logo
 * @property string $phone
 * @property string $address
 * @property float  $latitude
 * @property float  $longitude
 * @property float  $rating
 * @property string $opens_at
 * @property string $closes_at
 */
class ShowResource extends JsonResource
{

    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'logo'        => $this->logo,
            'phone'       => $this->phone,
            'address'     => $this->address,
            'latitude'    => $this->latitude,
            'longitude'   => $this->longitude,
            'rating'      => $this->rating,
            'opens_at'    => $this->opens_at,
            'closes_at'   => $this->closes_at,
            'services'    => ServiceResource::collection($this->services),
        ];
    }
}
