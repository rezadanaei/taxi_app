<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CarType;
use App\Models\Driver;

class Car extends Model
{
    protected $fillable = [
        'name', 'car_identifier', 'car_type_id',
    ];

    /**
     * Car belongs to a CarType
     */
    public function carType(): BelongsTo
    {
        return $this->belongsTo(CarType::class);
    }

    /**
     * Car has many drivers
     */
    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }
}
