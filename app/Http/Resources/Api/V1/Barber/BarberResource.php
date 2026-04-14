<?php

namespace App\Http\Resources\Api\V1\Barber;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int    $id
 * @property string $name
 * @property string $avatar
 * @property string $specialization
 * @property float  $rating
 * @property int    $experience_years
 */
class BarberResource extends JsonResource
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
            'avatar'           => $this->avatar,
            'specialization'   => $this->specialization,
            'rating'           => $this->rating,
            'experience_years' => $this->experience_years,
        ];
    }
}
