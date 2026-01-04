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
use App\Facades\SMS;
use App\Models\TripLog;
use App\Models\AdminNotification;
use App\Services\PushNotificationService;
use App\Jobs\AutoStartTripJob;
use Illuminate\Support\Str;




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
            'luggage_count'    => ['nullable', 'integer', 'min:0'],
            'origins'          => ['required', 'string'], 
            'destinations'     => ['required', 'string'],
            'car_type_id'      => ['required', 'exists:car_types,id'],
            'trip_time'        => ['nullable', 'string'],      
            'trip_distance'    => ['required', 'string'],      
            'special_distance' => ['required', 'string'],      
            'normal_distance'  => ['required', 'string'],      
            'cost'             => ['required', 'numeric', 'min:0'],
            'caption'          => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        /** 🔴 اگر لاگین نیست */
        if (!Auth::guard('web')->check()) {
            // ذخیره موقت اطلاعات سفر
            session()->put('pending_trip', $request->all());
            return redirect()->route('login')->with('error', 'برای ثبت سفر وارد شوید.');
        }
        if (Auth::user()->type !== 'passenger') {
            return redirect()->route('home')->with('error', 'شما به عنوان مسافر مجاز به ثبت سفر نیستید.');
        }

        $user = Auth::user();

        $origins = json_decode($request->origins, true);
        $destinations = json_decode($request->destinations, true);

        $tripDistance    = floatval($request->trip_distance);
        $specialDistance = floatval($request->special_distance);
        $normalDistance  = floatval($request->normal_distance);

        $car = CarType::find($request->car_type_id);
        $normalPrice  = $car->price_per_km;
        $specialPrice = $car->price_per_km * tariff('area_coef');

        $totalPrice =
            ($normalDistance * $normalPrice) +
            ($specialDistance * $specialPrice);

        if ($request->trip_type === 'round') {
            $totalPrice *= 2;
        }

        $waitingHours = $request->waiting_hours ?? 0;
        $pricePerHour = tariff('waiting_fee'); 
        $totalPrice += $waitingHours * $pricePerHour;

        $totalPrice = ceil($totalPrice / 1000) * 1000;
        $finalCost = $totalPrice > $request->cost ? $totalPrice : $request->cost;

        $startDateTehran = Jalalian::fromFormat('Y/m/d H:i:s', $request->start_date)
            ->toCarbon()
            ->setTimezone('Asia/Tehran');

        $startDateForDB = $startDateTehran
            ->copy()
            ->setTimezone(config('app.timezone'))
            ->toDateTimeString();

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
            $driver->notify(new DriverTripNotification([
                'trip_id' => $trip->id,
                'driver_id' => $driver->id,
                'is_sent' => false,
            ]));
        }

        $tripStart = Carbon::parse($startDateForDB);
        $now = now();
        $runAt = $now->diffInMinutes($tripStart, false) > 90
            ? $tripStart->copy()->subHour()
            : $now->copy()->addMinutes(30);

        if ($runAt->lte($now)) {
            $runAt = $now->copy()->addMinute();
        }

        NotifyTripAdmins::dispatch($trip)->delay($runAt);

        return redirect()->back()->with('success', 'سفر با موفقیت ثبت شد.');
    }


    public function storeAfterLogin(FindDriversForTrip $finder)
    {
        if (!session()->has('pending_trip')) {
            return redirect()->route('home');
        }

        $tripData = session()->get('pending_trip');

        $request = new Request($tripData);

        return $this->store($request, $finder);
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

        $driverTrips = Trip::with([
                'carType',
                'driver',
                'passenger.userable'
            ])
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

    public function acceptTrip(Request $request)
    {
        $tripId = $request->query('trip_id');
        $trip = Trip::find($tripId);
        $user = Auth::user();

        if (!$trip) {
            return response()->json(['success' => false, 'message' => 'سفر یافت نشد.'], 404);
        }

        if ($user->type !== 'driver') {
            return response()->json(['success' => false, 'message' => 'شما اجازه انجام این عملیات را ندارید.'], 403);
        }

        $trip->driver_id = $user->id;
        $trip->status = 'pending-payment';
        $trip->save();

        $site_name = setting('site_name');
        $passenger = $trip->passenger;
       
        if ($passenger && $passenger->userable) {
            $firstName = $passenger->userable->first_name;
            $lastName  = $passenger->userable->last_name;
            if (!empty($firstName) || !empty($lastName)) {
                $fullName = trim($firstName . ' ' . $lastName);
            } else {
                $fullName = "شماره " . $passenger->id;
            }
            TripLog::create([
                'trip_id'     => $trip->id,
                'action'      => 'accept',
                'description' => 'پذیرش سفر توسط راننده',
                'actor_id'    => $user->id,
                'actor_type'  => get_class($user),
            ]);
            // SMS::sendPattern($passenger->phone, [$fullName, $trip->id], '401678');
            
            $pushService = new PushNotificationService();

            $pushService->sendToUsers(
                userId: $passenger->id,
                title: 'سفر شما پذیرفته شد',
                body: 'راننده ای درخواست سفر شما را قبول کرد.',
                data: [
                    'type'      => 'trip_accepted',
                    'trip_id'   => $trip->id,
                    'driver_id' => $trip->driver_id,
                    'accepted_at' => now(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'راننده با موفقیت به سفر اختصاص داده شد.'
            ]);
        }
    }



    public function cancelTrip(Request $request)
    {
        $request->validate([
            'trip_id' => 'required|integer|exists:trips,id',
        ]);

        $trip = Trip::find($request->trip_id);
        if (!$trip) {
            return back()->with('error', 'سفر پیدا نشد.');
        }

        $user = auth()->user(); 

        $now = Carbon::now();
        $startTime = Carbon::parse($trip->start_date); 

        $oldStatus = $trip->status;

        if ($user->type === 'passenger') {
            if ($now->gte($startTime)) {
                return back()->with('error', 'سفر بعد از شروع قابل لغو نیست.');
            }
        } elseif ($user->type === 'driver') {
            if ($now->gte($startTime->subHour())) {
                return back()->with('error', 'زمان لغو توسط راننده گذشته است.');
            }

            $trip->driver_id = null;
        }

        if ($user->type === 'passenger') {
            $trip->status = 'cancelled';
        }

        $trip->save();

        TripLog::create([
            'trip_id'     => $trip->id,
            'action'      => 'cancel',
            'description' => $user->type === 'driver'
                ? 'لغو سفر توسط راننده'
                : 'لغو سفر توسط مسافر',
            'meta'        => [
                'status_before' => $oldStatus,
                'status_after' => $trip->status ?? 'driver_cancelled',
                'driver_id'     => $trip->driver_id,
            ],
            'actor_id'   => $user->id,
            'actor_type' => get_class($user),
        ]);


        if ($user->type === 'driver') {
            $driverLogsCount = TripLog::where('actor_id', $user->id)
                ->where('actor_type', get_class($user))
                ->count();

            if ($driverLogsCount % 3 === 0) {
                $driverName = $user->userable ? trim($user->userable->first_name . ' ' . $user->userable->last_name)  : "راننده شماره {$user->id}";

                $body = "راننده {$driverName} سفر {$trip->id} را لغو کرده و تعداد لاگ‌ها به مضرب ۳ رسید.";
                AdminNotification::create([
                    'title' => 'راننده سفر لغو کرد',
                    'body'  => $body,
                    'data'  => [
                        'driver_id' => $user->id,
                        'trip_id'   => $trip->id,
                        'logs_count'=> $driverLogsCount,
                    ],
                    'seen_by_admin_id' => null, 
                ]);
            }
        }

        return back()->with('success', 'سفر با موفقیت لغو شد.');
    }

    public function arrived(Request $request, Trip $trip)
    {
        $driver = auth()->user();

        /* =====================
           Authorization
        ===================== */
        if ($trip->driver_id !== $driver->id) {
            return response()->json([
                'status' => false,
                'message' => 'دسترسی غیرمجاز'
            ], 403);
        }

        /* =====================
           Trip Status Check
        ===================== */
        if ($trip->status !== 'paid') {
            return response()->json([
                'status' => false,
                'message' => 'امکان اعلام رسیدن برای این سفر وجود ندارد'
            ], 422);
        }

        

        /* =====================
           Push Notification
           To Passenger
        ===================== */
        if ($trip->passenger_id) {

            $pushService = new PushNotificationService();

            $pushService->sendToUsers(
                userId: $trip->passenger_id,
                title: 'راننده به محل رسید',
                body: 'راننده در محل مبدا حضور دارد.',
                data: [
                    'type'      => 'driver_arrived',
                    'trip_id'   => $trip->id,
                    'driver_id' => $trip->driver_id,
                    'arrived_at'=> $trip->driver_arrived_at,
                ]
            );
        }

        return response()->json([
            'status'  => true,
            'message' => 'اعلام رسیدن با موفقیت ثبت شد'
        ]);
    }

    public function start(Request $request, Trip $trip){
        $driver = auth()->user();
        $now = now();

        if ($trip->driver_id !== $driver->id) {
            return response()->json([
                'status' => false,
                'message' => 'دسترسی غیرمجاز'
            ], 403);
        }

        /* ===================== Status Check ===================== */
         if ($trip->status !== 'paid') {
            return response()->json([ 
                'status' => false, 
                'message' => 'شروع سفر برای این وضعیت امکان‌پذیر نیست' ],
                 422); 
        }

        /* ===================== Time Check (15 min before) ===================== */

        $startTime = Carbon::parse($trip->start_date);
        $allowedTime = $startTime->copy()->subMinutes(15);

        if ($now->lt($allowedTime)) {
            return response()->json([
                'status' => false,
                'message' => 'درخواست شروع سفر فقط از ۱۵ دقیقه قبل از زمان برنامه‌ریزی‌شده امکان‌پذیر است.'
            ], 422);
        }

        /* =====================
        Log: Start Requested
        ===================== */
        TripLog::create([
            'trip_id'     => $trip->id,
            'action'      => 'start_requested',
            'description' => 'درخواست شروع سفر توسط راننده',
            'meta'        => [
                'status_before' => $trip->status,
                'driver_id'     => $driver->id,
                'lat'           => $request->lat,
                'lng'           => $request->lng,
            ],
            'actor_id'   => $driver->id,
            'actor_type' => get_class($driver),
        ]);

        /* =====================
        Push Notification to Passenger
        ===================== */
        if ($trip->passenger_id) {
            $pushService = new PushNotificationService();
            $requestId = Str::uuid()->toString();
            $pushService->sendToUsers(
            userId: $trip->passenger_id,
            title: 'درخواست شروع سفر',
            body: "راننده سفر {$trip->id} را شروع کرد",
            data: [
                'type'       => 'trip_start_requested',
                'trip_id'    => $trip->id,
                'request_id' => $requestId,
                'route'      => route('trip.start.response', [
                    'trip' => $trip->id,
                    'rid'  => $requestId
                ])
            ]
        );

        }

        AutoStartTripJob::dispatch($trip->id)
            ->delay(now()->addMinutes(3));

        return response()->json([
            'status' => true,
            'message' => 'درخواست شروع سفر ارسال شد'
        ]);



    }

    public function startResponse(Request $request, Trip $trip, string $rid)
    {
        $user = auth()->user();

        /* =====================
           Authorization
        ===================== */
        
        if ($trip->passenger_id !== $user->id) {
            abort(403, 'دسترسی غیرمجاز');
        }

        /* =====================
           Prevent Duplicate Logs
        ===================== */
        $alreadyLogged = TripLog::where('trip_id', $trip->id)
            ->where('action', 'passenger_interacted')
            ->where('meta->request_id', $rid)
            ->exists();

        if (! $alreadyLogged) {
            TripLog::create([
                'trip_id' => $trip->id,
                'action'  => 'passenger_interacted',
                'description' => 'مسافر درخواست شروع سفر را رد کرد',
                'meta' => [
                    'request_id' => $rid,
                    'passenger_id' => $user->id,
                    'ip' => $request->ip(),
                ],
                'actor_id'   => $user->id,
                'actor_type' => get_class($user),
            ]);
        }

        /* =====================
           Redirect / Response
        ===================== */
        return redirect()->back()->with('success', 'عملیات با موفقیت انجام شد.');
    }

    public function end(Request $request, Trip $trip)
    {
        $driver = auth()->user();
        $now = now();

        /* ===================== Authorization ===================== */
        if ($trip->driver_id !== $driver->id) {
            return response()->json([
                'status' => false,
                'message' => 'دسترسی غیرمجاز'
            ], 403);
        }

        /* ===================== Status Check ===================== */
        if ($trip->status !== 'ongoing') {
            return response()->json([
                'status' => false,
                'message' => 'پایان سفر فقط برای سفرهای در حال انجام امکان‌پذیر است'
            ], 422);
        }

        /* ===================== Log: End Requested ===================== */
        TripLog::create([
            'trip_id'     => $trip->id,
            'action'      => 'end_requested',
            'description' => 'درخواست پایان سفر توسط راننده',
            'meta'        => [
                'status_before' => $trip->status,
                'driver_id'     => $driver->id,
                'lat'           => $request->lat,
                'lng'           => $request->lng,
            ],
            'actor_id'   => $driver->id,
            'actor_type' => get_class($driver),
        ]);

        /* ===================== Push Notification to Passenger ===================== */
        if ($trip->passenger_id) {
            $requestId = Str::uuid()->toString();
            $pushService = new PushNotificationService();

            $pushService->sendToUsers(
                userId: $trip->passenger_id,
                title: 'درخواست پایان سفر',
                body: "راننده سفر {$trip->id} را پایان داد",
                data: [
                    'type'       => 'trip_end_requested',
                    'trip_id'    => $trip->id,
                    'request_id' => $requestId,
                    'route'      => route('trip.end.response', [
                        'trip' => $trip->id,
                        'rid'  => $requestId
                    ])
                ]
            );
        }

        AutoEndTripJob::dispatch($trip->id)
            ->delay(now()->addMinutes(3));

        return response()->json([
            'status' => true,
            'message' => 'درخواست پایان سفر ارسال شد'
        ]);
    }

    public function endResponse(Request $request, Trip $trip, string $rid)
    {
        $user = auth()->user();

        /* ===================== Authorization ===================== */
        if ($trip->passenger_id !== $user->id) {
            abort(403, 'دسترسی غیرمجاز');
        }

        /* ===================== Prevent Duplicate Logs ===================== */
        $alreadyLogged = TripLog::where('trip_id', $trip->id)
            ->where('action', 'passenger_end_interacted')
            ->where('meta->request_id', $rid)
            ->exists();

        if (! $alreadyLogged) {
            TripLog::create([
                'trip_id'     => $trip->id,
                'action'      => 'passenger_end_interacted',
                'description' => 'مسافر درخواست پایان سفر را رد کرد',
                'meta' => [
                    'request_id'   => $rid,
                    'passenger_id' => $user->id,
                    'ip'           => $request->ip(),
                ],
                'actor_id'   => $user->id,
                'actor_type' => get_class($user),
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'پاسخ ثبت شد'
        ]);
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
