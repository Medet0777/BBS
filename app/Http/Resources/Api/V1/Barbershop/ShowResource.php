<?php

namespace App\Http\Resources\Api\V1\Barbershop;

use App\Http\Resources\Api\V1\Barber\BarberResource;
use App\Http\Resources\Api\V1\Review\ReviewResource;
use App\Http\Resources\Api\V1\Service\ServiceItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

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
        $currentTime = Carbon::now()->format('H:i');
        $isOpen      = $this->opens_at <= $currentTime && $this->closes_at >= $currentTime;

        $groupedServices = $this->services
            ->groupBy('service_category_id')
            ->map(fn ($services) => [
                'category_id'   => $services->first()->service_category_id,
                'category_name' => $services->first()->serviceCategory?->name,
                'items'         => ServiceItemResource::collection($services),
            ])
            ->values();

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'logo'          => $this->logo,
            'phone'         => $this->phone,
            'address'       => $this->address,
            'latitude'      => $this->latitude,
            'longitude'     => $this->longitude,
            'rating'        => $this->avg_rating !== null ? round((float) $this->avg_rating, 1) : 0,
            'reviews_count' => (int) $this->reviews_count,
            'opens_at'      => $this->opens_at,
            'closes_at'     => $this->closes_at,
            'status'        => $isOpen ? 'open' : 'closed',
            'barbers'       => BarberResource::collection($this->barbers),
            'services'      => $groupedServices,
            'reviews'       => ReviewResource::collection($this->reviews),
        ];
    }
}
