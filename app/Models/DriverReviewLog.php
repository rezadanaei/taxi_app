<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverReviewLog extends Model
{
    protected $fillable = [
        'driver_id',
        'admin_id',
        'status',
        'message',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
