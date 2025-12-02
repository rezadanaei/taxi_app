<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverTripNotification extends Model
{
    protected $fillable = [
        'driver_id',
        'trip_id',
        'is_sent'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
