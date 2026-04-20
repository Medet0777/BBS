<?php

namespace App\Http\Resources\Api\V1\Barbershop;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property string      $name
 * @property string      $slug
 * @property string      $logo
 * @property string      $address
 * @property float       $rating
 * @property string      $opens_at
 * @property string      $closes_at
 * @property float|null  $distance_km
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
        $currentTime = Carbon::now()->format('H:i');
        $isOpen      = $this->opens_at <= $currentTime && $this->closes_at >= $currentTime;

        $minPrice = $this->min_price !== null ? (int) $this->min_price : null;

        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'logo'              => $this->logo,
            'address'           => $this->address,
            'rating'            => $this->avg_rating !== null ? round((float) $this->avg_rating, 1) : 0,
            'reviews_count'     => (int) $this->reviews_count,
            'opens_at'          => $this->opens_at,
            'closes_at'         => $this->closes_at,
            'status'            => $isOpen ? 'open' : 'closed',
            'min_price'         => $minPrice,
            'min_price_display' => $minPrice !== null ? 'от ' . $minPrice . ' ₸' : null,
            'distance_km'       => isset($this->distance_km) ? round((float) $this->distance_km, 2) : null,
        ];
    }
}
