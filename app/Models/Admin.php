<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;
    /**
     * Mass assignable attributes for admin.
     */

    protected $fillable = [
        'name',
        'username',
        'password',
        'phone',
        'type',
        'status',
    ];
        
    /**
     * Hide sensitive fields when model is serialized.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
