<?php

namespace App\Http\Controllers;

use App\Jobs\NotifyTripAdmins;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\CarType;
use App\Models\User;
use App\Models\Zone;
use \App\Models\Payment;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\FindDriversForTrip;
use App\Notifications\DriverTripNotification;
use Morilog\Jalali\Jalalian;


class TripController extends Controller
{
    /**
    * Store a new trip in the database.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\RedirectResponse
    */

     public function store(Request $request, FindDriversForTrip $finder)
    {
        $validator = Validator::make($request->all(), [
            'start_date'       => ['required', 'date'],
            'trip_type'        => ['required', 'in:oneway,round'], 
            'waiting_hours'    => ['nullable', 'integer', 'min:0'],
            'has_pet'          => ['nullable', 'boolean'], 
            'passenger_count'  => ['nullable', 'integer', 'min:1', 'max:5'],
            'luggage_count'    =>  ['nullable', 'integer', 'min:0'],
            'origins'          => ['required', 'string'], 
            'destinations'     => ['required', 'string'],
            'car_type_id'      => ['required', 'exists:car_types,id'],
            'trip_time'        => ['nullable', 'string'],      
            'trip_distance'    => ['required', 'string'],      
            'special_distance'    => ['required', 'string'],      
            'normal_distance'    => ['required', 'string'],      
            'cost'             => ['required', 'numeric', 'min:0'],
            'caption'          => ['nullable', 'string', 'max:500'],
        ]);
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login')->with('error', 'برای ثبت سفر وارد شوید.');
        }

        $user = Auth::user();

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $origins = json_decode($request->origins, true);
        $destinations = json_decode($request->destinations, true);

        $normalDistance = 0;
        $specialDistance = 0;
        $totalDuration = 0;
        $points = array_merge($origins, $destinations);
        
        $tripDistance     = floatval($request->trip_distance);      // km
        $specialDistance  = floatval($request->special_distance);   // km
        $normalDistance   = floatval($request->normal_distance);    // km

        $car = CarType::find($request->car_type_id);
        $normalPrice   = $car->price_per_km;
        $specialPrice  = $car->price_per_km * tariff('area_coef');

        $totalPrice =
            ($normalDistance * $normalPrice) +
            ($specialDistance * $specialPrice);


        
        if ($request->trip_type === 'round') {
            $totalPrice *= 2;
        }
        
        $waitingHours = $request->waiting_hours ?? 0;
        $pricePerHour =  tariff('waiting_fee'); 
        $totalPrice += $waitingHours * $pricePerHour;

        $totalPrice = ceil($totalPrice / 1000) * 1000;

        $finalCost = $totalPrice > $request->cost ? $totalPrice : $request->cost;
        $startDateJalali = $request->start_date; // "1404/09/04 10:30:00"

        $startDateTehran = Jalalian::fromFormat('Y/m/d H:i:s', $startDateJalali)
                                    ->toCarbon()
                                    ->setTimezone('Asia/Tehran');

        $startDateServer = $startDateTehran->copy()->setTimezone(config('app.timezone'));
        $startDateForDB = $startDateServer->toDateString();
        $trip = Trip::create([
            'start_date'       => $startDateForDB,
            'trip_type'        => $request->trip_type,
            'waiting_hours'    => $waitingHours,
            'has_pet'          => $request->has_pet ?? false,
            'passenger_count'  => $request->passenger_count ?? 1,
            'luggage_count'    => $request->luggage_count ?? 0,
            'origins'          => $request->origins,
            'destinations'     => $request->destinations,
            'car_type_id'      => $request->car_type_id,
            'trip_time'        => $request->trip_time,
            'trip_distance'    => $normalDistance + $specialDistance,
            'cost'             => $finalCost,
            'caption'          => $request->caption,
            'status'           => 'pending',
            'driver_id'        => null, 
            'passenger_id'     => $user->id,
        ]);
        
        $drivers = $finder->getDrivers($trip);

        foreach ($drivers as $driver) {
            $data = [
                'trip_id'   => $trip->id,
                'driver_id' => $driver->id,
                'is_sent'   => false,
            ];

            $notification = new DriverTripNotification($data);
            $driver->notify($notification);
        
        }
         
        $tripStart = $startDateServer;
        $now = now();
        

        $diffInMinutes = $now->diffInMinutes($tripStart, false);

        if ($diffInMinutes > 90) {
            $runAt = $tripStart->copy()->subHour();

            if ($runAt->lt($now)) {
                $runAt = $now->copy()->addMinutes(30);
            }
        } else {
            $runAt = $now->copy()->addMinutes(30);
        }

        
        NotifyTripAdmins::dispatch($trip)->delay(delay: $runAt);

        return redirect()->back()->with('success', 'سفر با موفقیت ثبت شد.');
    }



    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->status !== 'active') {
            return response()->json([
                'status' => false,
                'message' => 'راننده فعال نیست.'
            ], 403);
        }

        $driverId = $user->id;
        $driverCarTypeId = $user->userable->car->car_type_id ?? null;

        $perPage = 24; 
        $page = $request->get('page', 1);

        $tripsWithoutDriver = Trip::with(['carType', 'passenger', 'driver'])
            ->whereNull('driver_id')
            ->whereHas('carType', function ($q) use ($driverCarTypeId) {
                $q->where('id', $driverCarTypeId);
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        $driverTrips = Trip::with(['carType', 'passenger', 'driver'])
            ->where('driver_id', $driverId)
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);


        $tripsWithoutDriver->getCollection()->transform(function ($trip) {
            $tripDT = tripDate($trip->start_date);
            $trip->formatted_date = $tripDT['date'];
            $trip->formatted_time = $tripDT['time'];
            return $trip;
        });

        $driverTrips->getCollection()->transform(function ($trip) {
            $tripDT = tripDate($trip->start_date);
            $trip->formatted_date = $tripDT['date'];
            $trip->formatted_time = $tripDT['time'];
            return $trip;
        });

        return response()->json([
            'status' => true,
            'tripsWithoutDriver' => $tripsWithoutDriver,
            'driverTrips' => $driverTrips
        ]);
    }


    public function status(Request $request, $type = "user"){
        if ($type == "user") {
    
            if ($request['id']) {
                $id = $request['id'];
                $trip = Trip::find($id);
                if ($trip) {
                    if ($trip->status == 'pending') {
                        return view('user-state-white');
                    } else if ($trip->status == 'accepted') {
                        return view('user-state-acceped');
                    } else if ($trip->status == 'rejected') {
                        return view('user-state-rejected');
                    }
                }
            }
        } elseif ($type == "driver") {
            
        }
    }
    private function normalizeTariff($value)
    {
        if (is_numeric($value) && floor($value) != $value) {
            return floatval($value);
        }

        return intval($value);
    }


    public function payment(Request $request, \App\Services\ZarinpalService $zarinpal)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'trip_id' => 'required|integer|exists:trips,id',
        ]);

        $trip = Trip::findOrFail($validated['trip_id']);

        $commissionPercent = $this->normalizeTariff(tariff('commission'));
        $commissionToman = $trip->cost * ($commissionPercent / 100);
        $amount = round($commissionToman * 10); // ریال

        $payment = Payment::create([
            'user_id' => $user->id,
            'payable_id' => $trip->id,
            'payable_type' => Trip::class,
            'amount' => $amount,
            'status' => 'pending',
            'type' => 'trip',
        ]);

        $result = $zarinpal->requestPayment([
            'amount' => $amount,
            'description' => 'پرداخت کمیسیون سفر شماره ' . $trip->id,
            'callback_url' => route('payment.verify'), 
            'mobile' => $user->phone ?? null,
            'email' => $user->email ?? null,
        ]);

        if (!$result['success']) {
            $payment->update(['status' => 'failed']);
            return response()->json(['error' => $result['message']], 500);
        }

        $payment->update(['authority' => $result['authority']]);

        return redirect($result['payment_url']);
    }

    public function neshanAPI(Request $request)
    {
        $csrf = $request->header('X-CSRF-TOKEN');
        $expected = csrf_token(); 

        if ($csrf !== $expected) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'API_KEY_WEB' => setting('nashan_web_key'),
            'API_KEY_SERVICE' => setting('nashan_service_key'),
            
        ]);
    }
   

}
