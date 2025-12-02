<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $fillable = ['title', 'body', 'data', 'seen_by_admin_id'];

    protected $casts = [
        'data' => 'array',
    ];
}
