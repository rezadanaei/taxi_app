<?php

namespace App\Services;

use App\Models\User;
use App\Models\Trip;
use App\Models\Driver;
use App\Models\Car;

class FindDriversForTrip
{
    /**
     * دریافت رانندگان مناسب برای سفر
     *
     * @param Trip $trip
     */

    public function getDrivers(Trip $trip)
    {
        return User::select('users.*')
        ->join('drivers', 'drivers.id', '=', 'users.userable_id')
        ->join('cars', 'cars.id', '=', 'drivers.car_id')
        ->where('users.userable_type', Driver::class)
        ->where('users.type', 'driver')
        ->where('users.status', 'active')
        ->where('cars.car_type_id', $trip->car_type_id)
        ->with('userable')
        ->get();
    }

}
