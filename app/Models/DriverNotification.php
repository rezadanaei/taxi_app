<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverNotification extends Model
{
    protected $fillable = ['driver_id', 'message', 'seen_by_admin_id'];

    
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
