<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static where(string $string, mixed $value)
 * @method static create(array $data)
 */
class ServiceCategory extends Model
{

    use CrudTrait;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'icon',
        'sort_order',
    ];

    /**
     * @return HasMany
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
