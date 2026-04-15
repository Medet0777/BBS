<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static where(string $string, mixed $value)
 * @method static create(array $data)
 */
class Review extends Model
{

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'barbershop_id',
        'rating',
        'comment',
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function barbershop(): BelongsTo
    {
        return $this->belongsTo(Barbershop::class);
    }
}
