<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method static where(string $string, mixed $value)
 * @method static create(array $data)
 */
class Booking extends Model
{

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'barbershop_id',
        'barber_id',
        'scheduled_at',
        'status',
        'comment',
        'reminder_enabled',
        'total_price',
        'total_duration_minutes',
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'scheduled_at'           => 'datetime',
            'status'                 => BookingStatus::class,
            'reminder_enabled'       => 'boolean',
            'total_price'            => 'decimal:2',
            'total_duration_minutes' => 'integer',
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

    /**
     * @return BelongsTo
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }

    /**
     * @return BelongsToMany
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'booking_service')
            ->withPivot(['price_snapshot', 'duration_snapshot'])
            ->withTimestamps();
    }
}
