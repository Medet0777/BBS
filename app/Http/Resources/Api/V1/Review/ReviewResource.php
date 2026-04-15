<?php

namespace App\Http\Resources\Api\V1\Review;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int         $id
 * @property int         $rating
 * @property string|null $comment
 * @property mixed       $created_at
 */
class ReviewResource extends JsonResource
{

    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'user_name'  => $this->user?->name,
            'rating'     => $this->rating,
            'comment'    => $this->comment,
            'created_at' => $this->created_at,
        ];
    }
}
