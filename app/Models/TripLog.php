<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripLog extends Model
{
    protected $fillable = [
        'trip_id',
        'action',
        'description',
        'meta',
        
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function actor()
    {
        return $this->morphTo();
    }

}
