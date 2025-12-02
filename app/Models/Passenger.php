<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Models\User;

class Passenger extends Model
{
    protected $fillable = [
        'name', 'national_code', 'birth_date',
    ];


    /**
     * MorphOne back to User
     */
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }
}
