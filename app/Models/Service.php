<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static where(string $string, mixed $value)
 * @method static create(array $data)
 */
class Service extends Model
{

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'barbershop_id',
        'service_category_id',
        'price',
        'duration_minutes',
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'price'            => 'decimal:2',
            'duration_minutes' => 'integer',
        ];
    }

    /**
     * @return BelongsTo
     */
    public function barbershop(): BelongsTo
    {
        return $this->belongsTo(Barbershop::class);
    }

    /**
     * @return BelongsTo
     */
    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }
}
