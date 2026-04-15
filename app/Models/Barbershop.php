<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static where(string $string, mixed $value)
 * @method static create(array $data)
 */
class Barbershop extends Model
{

    use CrudTrait;

    /**
     * @var string[]
     */
    protected $fillable = [
        'owner_id',
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

    /**
     * @return HasMany
     */
    public function barbers(): HasMany
    {
        return $this->hasMany(Barber::class);
    }

    /**
     * @return HasMany
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * @return HasMany
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
