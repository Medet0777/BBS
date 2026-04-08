<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static where(string $string, mixed $value)
 * @method static create(array $data)
 */
class Barbershop extends Model
{

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'phone',
        'address',
        'latitude',
        'longitude',
        'rating',
        'opens_at',
        'closes_at',
        'is_active',
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'latitude'  => 'decimal:7',
            'longitude' => 'decimal:7',
            'rating'    => 'decimal:1',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
