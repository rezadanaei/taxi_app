<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use App\Models\Car;

class CarType extends Model
{
    protected $fillable = [
        'title', 'description', 'price_per_km', 'header_image',
    ];

    protected $casts = [
        'price_per_km' => 'float',
    ];

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    protected static function booted()
    {
        static::deleting(function ($carType) {
            if ($carType->header_image && Storage::disk('public')->exists($carType->header_image)) {
                Storage::disk('public')->delete($carType->header_image);
            }
        });

        static::updating(function ($carType) {
            if ($carType->isDirty('header_image')) { 
                $oldImage = $carType->getOriginal('header_image');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });
    }
}
