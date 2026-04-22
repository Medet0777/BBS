<?php

namespace App\Http\Resources\Api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string|null $email_verified_at
 */
class UserResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        $ownedBarbershop = \App\Models\Barbershop::where('owner_id', $this->id)->first(['id', 'slug']);

        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'email'             => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'barbershop_id'     => $ownedBarbershop?->id,
            'barbershop_slug'   => $ownedBarbershop?->slug,
        ];
    }
}
