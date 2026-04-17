<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static where(string $string, mixed $value)
 * @method static create(array $data)
 */
class Barber extends Model
{

    use CrudTrait;

    /**
     * @var string[]
     */
    protected $fillable = [
        'barbershop_id',
        'user_id',
        'name',
        'avatar',
        'specialization',
        'rating',
        'experience_years',
        'is_active',
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'rating'           => 'decimal:1',
            'experience_years' => 'integer',
            'is_active'        => 'boolean',
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
