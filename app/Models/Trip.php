<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\CarType;

class Trip extends Model
{
    protected $fillable = [
        'start_date',
        'trip_type',
        'waiting_hours',
        'has_pet',
        'passenger_count',
        'luggage_count',
        'origins',
        'destinations',
        'car_type_id',
        'trip_time',
        'trip_distance',
        'cost',
        'caption',
        'status',
        'driver_id',
        'passenger_id',
    ];

    
    public function carType(): BelongsTo
    {
        return $this->belongsTo(CarType::class, 'car_type_id');
    }

    
    public function passenger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    
    public static function statuses(): array
    {
        return [
            'pending',
            'ongoing',
            'completed',
            'cancelled',
            'rejected',
            'no-show',
            'paid',
            'refunded',
            'pending-payment',
        ];
    }
}
