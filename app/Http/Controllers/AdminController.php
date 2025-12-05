<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; 
use App\Facades\SMS;
use App\Models\Tariff;
use App\Models\CarType;
use App\Models\Car;
use App\Models\Admin;
use App\Models\DriverNotification;
use App\Models\Zone;
use App\Models\Setting; 
use App\Models\User;
use App\Models\Passenger;
use App\Models\Driver;
use App\Models\DriverReviewLog;
use App\Models\Trip;

use function Pest\Laravel\json;

class AdminController extends Controller
{
    public function login()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin-login');
    }

    public function loginVerification(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->intended('/admin/dashboard'); 
        }

        return back()->withErrors([
            'username' => 'نام کاربری یا رمز عبور اشتباه است.',
        ]);
    }
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login'); 
    }
    public function dashboard(Request $request)
    {
        $tab = $request->query('tab', 'dashboard'); 

        return view('admin-profile', compact('tab'));
    }

    public function loadAdminPage($slug)
    {
        $allowedPages = [
            'dashboard'       => 'admin.dashboard-content', 
            'travels'         => 'admin.travels',           
            'pricing'         => 'admin.pricing',
            'cars'            => 'admin.cars',
            'users'           => 'admin.users',
            'drivers'         => 'admin.drivers',
            'driver-docs'     => 'admin.driver-docs',
            'driver-reports'  => 'admin.driver-reports',
            'zones'           => 'admin.zones',
            'admins'          => 'admin.admins',
            'notifications'   => 'admin.notifications',
            'setting'         => 'admin.setting',
        ];

        if (!array_key_exists($slug, $allowedPages)) {
            abort(404);
        }

        $view = $allowedPages[$slug];

        if ($slug == 'cars') {
            $carTypes = CarType::all();
            $cars = Car::all();
            return view($view, compact('carTypes', 'cars'));
        }
        
        if ($slug == 'admins') {
            $admins = Admin::all();
            return view($view, compact('admins'));
        }

        if ($slug == 'zones') {
            $zones = Zone::all();
            return view($view, compact('zones'));
        }
        if ($slug == 'users') {
            $users = User::where('type', 'passenger')
                ->with('userable') 
                ->paginate(24);
            return view($view, compact('users'));
        }
        if ($slug == 'driver-docs') {
            $notifications = DriverNotification::whereNull('seen_by_admin_id')
                ->with('driver.userable')
                ->get();

            $cars = Car::all();

            return view($view, compact('notifications', 'cars'));
        
        }

        if ($slug == 'drivers') {
           $drivers = User::where('type', 'driver')
               ->whereIn('status', ['active', 'inactive'])
               ->with('userable')
               ->get();

            return view($view, compact('drivers'));
        }

        if ($slug == 'travels') {
            $trips = Trip::with(['passenger.userable', 'driver.userable'])->orderBy('created_at', 'desc')->paginate(30);
           

            return view($view, compact('trips'));
        }

         
        if ($slug == 'dashboard' || is_null($slug) || $slug === '' || empty($slug)) {
            $driversCount = User::where('type', 'driver')
            ->where('status', 'active')
            ->count();
            $passengersCount = User::where('type', 'passenger')->count();
            $tripsCount = Trip::count();
            $tripsOngoingCount = Trip::whereIn('status', ['ongoing','paid', ])->count();
            return view($view, data: compact('driversCount', 'passengersCount', 'tripsCount', 'tripsOngoingCount'));
        }
        return view($view);
    }       

    // update pricing settings
    public function updatePricing(Request $request)
    {
         $request->validate([
            'commission' => 'required|numeric|min:0',
            'area_coef'  => 'required|numeric|min:0',
            'waiting_fee'=> 'required|numeric|min:0',
        ]);

        $tariff = Tariff::first();
        if (!$tariff) {
            $tariff = new Tariff();
        }

        $tariff->commission = $request->input('commission');
        $tariff->area_coef = $request->input('area_coef');
        $tariff->waiting_fee = $request->input('waiting_fee');
        $tariff->save();

        cache()->put('tariff_settings', $tariff->toArray(), now()->addDays(30)); 

        return redirect()->route('admin.page', ['slug' => 'pricing'])->with('success', 'تنظیمات تعرفه‌ها با موفقیت به‌روزرسانی شد.');
    }
    
    // Save or update a car type
    public function saveCarType(Request $request)
    {
        $carTypeId = $request->input('carType_id');

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_km' => 'required|numeric',
            'header_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'carType_id' => 'nullable|exists:car_types,id',
        ]);

        if (empty($carTypeId)) {
            $carType = new CarType();
        } else {
            $carType = CarType::find($carTypeId);
            if (!$carType) {
                return back()->with('error', 'دسته خودرو پیدا نشد.');
            }
        }

        $carType->title = $request->input('title');
        $carType->description = $request->input('description');
        $carType->price_per_km = $request->input('price_per_km');

        if ($request->hasFile('header_image')) {
            $file = $request->file('header_image');
            $path = $file->store('car_types', 'public');
            $carType->header_image = $path;
        }

        $carType->save();

        $message = empty($carTypeId) ? 'دسته خودرو جدید اضافه شد.' : 'دسته خودرو ویرایش شد.';
        return back()->with('success', $message);
    }

    
    public function deleteCarType(Request $request)
    {
        $carTypeId = $request->input('carType_id');

        $carType = CarType::find($carTypeId);
        if (!$carType) {
            return back()->with('error', 'دسته خودرو پیدا نشد.');
        }

        $carType->delete();

        return back()->with('success', 'دسته خودرو با موفقیت حذف شد.');
    }

    public function saveCar(Request $request)
    {
        $carId = $request->input('car_id');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'car_type_id' => 'required|exists:car_types,id',
            'car_id' => 'nullable|exists:cars,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (empty($carId)) {
            $car = new Car();
        } else {
            $car = Car::find($carId);
            if (!$car) {
                return back()->with('error', 'ماشین پیدا نشد.');
            }
        }

        $car->name = $request->input('name');
        $car->car_type_id = $request->input('car_type_id');

        if (empty($carId)) { 
            $baseSlug = Str::slug($request->input('name'));
            $slug = $baseSlug;
            $counter = 1;

            while (Car::where('car_identifier', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $car->car_identifier = $slug;
        }

        $car->save();

        $message = empty($carId) ? 'ماشین جدید اضافه شد.' : 'ماشین ویرایش شد.';
        return back()->with('success', $message);
    }

    public function deleteCar(Request $request)
    {
        $carId = $request->input('car_id');

        $car = Car::find($carId);
        if (!$car) {
            return back()->with('error', 'ماشین پیدا نشد.');
        }

        $car->delete();

        return back()->with('success', 'ماشین با موفقیت حذف شد.');
    }

    // Admin management
    public function saveAdmin(Request $request)
    {
        $currentAdmin = Auth::guard('admin')->user();

        if (!$currentAdmin || $currentAdmin->type !== 'owner') {
            return back()->with('error', 'شما اجازه این عملیات را ندارید.');
        }

        $adminId = $request->input('admin_id');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins,username,' . $adminId,
            'password' => $adminId ? 'nullable|string|min:6' : 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'type' => 'required|in:owner,admin',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (empty($adminId)) {
            $admin = new Admin();
        } else {
            $admin = Admin::find($adminId);
            if (!$admin) {
                return back()->with('error', 'ادمین پیدا نشد.');
            }
        }

        $admin->name = $request->input('name');
        $admin->username = $request->input('username');
        $admin->phone = $request->input('phone');
        $admin->type = $request->input('type');

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->input('password'));
        }

        $admin->save();

        $message = empty($adminId) ? 'ادمین جدید اضافه شد.' : 'اطلاعات ادمین ویرایش شد.';
        return back()->with('success', $message);
    }

    public function deleteAdmin(Request $request)
    {
        $currentAdmin = Auth::guard('admin')->user();

        if (!$currentAdmin || $currentAdmin->type !== 'owner') {
            return back()->with('error', 'شما اجازه این عملیات را ندارید.');
        }

        $adminId = $request->input('admin_id');

        $admin = Admin::find($adminId);
        if (!$admin) {
            return back()->with('error', 'ادمین پیدا نشد.');
        }

        if ($admin->id == $currentAdmin->id) {
            return back()->with('error', 'شما نمی‌توانید خودتان را حذف کنید.');
        }

        $admin->delete();

        return back()->with('success', 'ادمین با موفقیت حذف شد.');
    }

    // Get tariff settings
    public function getTariffSettings()
    {
        $tariffSettings = tariff(); 

        return response()->json($tariffSettings);
    }

    // Zone management 
    public function saveZone(Request $request)
    {
        $zoneId = $request->input('zone_id');

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_km' => 'required|numeric|min:0',
            'zone_id' => 'nullable|exists:zones,id',
        ]); 
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        if (empty($zoneId)) {
            $zone = new Zone();
        } else {
            $zone = Zone::find($zoneId);
            if (!$zone) {
                return back()->with('error', 'منطقه پیدا نشد.');    

            }
        }
        $zone->name = $request->input('name');
        $zone->latitude = $request->input('latitude');
        $zone->longitude = $request->input('longitude');    
        $zone->radius_km = $request->input('radius_km');
        $zone->save();
        $message = empty($zoneId) ? 'منطقه جدید اضافه شد.' : 'منطقه ویرایش شد.';
        return back()->with('success', $message);
    }

    public function deleteZone(Request $request)
    {
        $zoneId = $request->input('zone_id');

        $zone = Zone::find($zoneId);
        if (!$zone) {
            return back()->with('error', 'منطقه پیدا نشد.');
        }

        $zone->delete();

        return back()->with('success', 'منطقه با موفقیت حذف شد.');
    }

    // Save settings
    public function saveSettings(Request $request)
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'support_phone' => 'nullable|string|max:20',
            'merchant_id' => 'nullable|string|max:255',
            'sms_panel_number' => 'nullable|string|max:255',
            'sms_panel_username' => 'nullable|string|max:255',
            'sms_panel_password' => 'nullable|string|max:255',
            'nashan_web_key' => 'nullable|string|max:255',  
            'nashan_service_key' => 'nullable|string|max:255',
            'colers_primary' => 'nullable|string|max:7'    ,
            'colers_secondary' => 'nullable|string|max:7',
            'colers_tertiary' => 'nullable|string|max:7',


        ]);
        $setting = Setting::first();
        if (!$setting) {
            $setting = new Setting();
        }
        $setting->site_name = $request->input('site_name');
        $setting->support_phone = $request->input('support_phone');
        $setting->merchant_id = $request->input('merchant_id');
        $setting->sms_panel_number = $request->input('sms_panel_number');
        $setting->sms_panel_username = $request->input('sms_panel_username');
        $setting->sms_panel_password = $request->input('sms_panel_password');
        $setting->nashan_web_key = $request->input('nashan_web_key');  
        $setting->nashan_service_key = $request->input('nashan_service_key');
        $setting->colers_primary = $request->input('colers_primary');
        $setting->colers_secondary = $request->input('colers_secondary');
        $setting->colers_tertiary = $request->input('colers_tertiary');
        $setting->save();
        cache()->put('site_settings', $setting->toArray(), now()->addDays(30));
        return redirect()->back()->with('success', 'تنظیمات با موفقیت ذخیره شد.');
        
    }   

    // user management
    public function saveUser(Request $request)
    {
        $userId = $request->input('id');

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $userId,
            'national_code' => 'nullable|string|max:10',
            'birth_date' => 'nullable|date',
            'id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (empty($userId)) {
            $user = new User();
        } else {
            $user = User::find($userId);
            if (!$user) {
                return back()->with('error', 'کاربر پیدا نشد.');
            }
        }

        $user->phone = $request->input('phone');
        $user->save();

        if ($user->type === 'passenger') {
            $passenger = $user->userable ?? new Passenger();
            $passenger->name = $request->input('name');
            $passenger->national_code = $request->input('national_code');
            $passenger->birth_date = $request->input('birth_date');
            $passenger->save();
            if (!$user->userable_id) {
                $user->userable()->associate($passenger);
                $user->save();
            }
        }

        $message = $userId ? 'اطلاعات کاربر ویرایش شد.' : 'کاربر جدید اضافه شد.';
        return back()->with('success', $message);
    }

    public function deleteUser(Request $request)
    {
        $userId = $request->input('id');

        $user = User::find($userId);
        if (!$user) {
            return back()->with('error', 'کاربر پیدا نشد.');
        }

        $user->delete();

        return back()->with('success', 'کاربر با موفقیت حذف شد.');
    }

    // Driver management
    public function approveDriverDocument(Request $request)
    {
        $request->validate([
            'note_id' => 'required|integer|exists:driver_notifications,id',
            'car_id' => 'required|integer|exists:cars,id',
        ]);

        $notification = DriverNotification::with('driver.userable')
            ->find($request->note_id);

        if (!$notification) {
            return back()->with('error', 'اطلاعیه پیدا نشد.');
        }

        $driver = User::with('userable')->find($notification->driver_id);

        if (!$driver) {
            return back()->with('error', 'راننده پیدا نشد.');
        }

        $adminId = auth('admin')->id();

        $notification->update([
            'seen_by_admin_id' => $adminId,
        ]);
        
        $driver->status = 'active';
        $driver->save();
        
        
        DriverReviewLog::create([
            'driver_id' => $driver->id,
            'admin_id' => $adminId,
            'status' => 'approved',
            'message' => 'مدارک شما توسط مدیریت تایید شد.',
        ]);
        if ($driver->userable) {
            $driver->userable->update([
                'car_id' => $request->car_id,
            ]);

            // ارسال پیامک با نام و نام خانوادگی راننده
            $result = SMS::sendPattern(
                $driver->phone,
                [$driver->userable->first_name, $driver->userable->last_name],
                401672
            );
        }
        
        return redirect()->route('admin.page', ['slug' => 'driver-docs'])
            ->with('success', 'درخواست راننده با موفقیت تایید شد.');
    }
    public function rejectDriverDocument(Request $request)
    {
        $request->validate([
            'note_id' => 'required|integer|exists:driver_notifications,id',
            'message' => 'nullable|string',
        ]);

        $notification = DriverNotification::with('driver.userable')
            ->find($request->note_id);

        if (!$notification) {
            return back()->with('error', 'اطلاعیه پیدا نشد.');
        }

        $driver = User::with('userable')->find($notification->driver_id);

        if (!$driver) {
            return back()->with('error', 'راننده پیدا نشد.');
        }

        $adminId = auth('admin')->id();

        $notification->update([
            'seen_by_admin_id' => $adminId,
        ]);

        
        $message = $request->input('message', 'مدارک شما توسط مدیریت رد شد.');
        
        DriverReviewLog::create([
            'driver_id' => $driver->id,
            'admin_id' => $adminId,
            'status' => 'rejected', 
            'message' => $message,
        ]);
        
        if ($driver->userable) {
            $driver->status = 'rejected';
            $driver->save();
            $result = SMS::sendPattern($driver->phone ,[$driver->userable->first_name, $driver->userable->last_name], 401675);
        }
        
        return redirect()->route('admin.page', ['slug' => 'driver-docs'])
            ->with('success', 'درخواست راننده با موفقیت رد شد.');
    }

    public function toggleStatus(Request $request)
    {
        $request->validate([
            'driver_id' => 'required|integer|exists:users,id',
        ]);

        $driver = User::where('type', 'driver')
            ->where('id', $request->driver_id)
            ->first();

        if (!$driver) {
            return back()->with('error', 'راننده پیدا نشد.');
        }
        if ($driver->status == 'inactive') {
            $driver->status = 'active';
            $driver->save();
        } else {
            $driver->status = 'inactive';
            $driver->save();
        }

        return back()->with('success', 'وضعیت راننده با موفقیت تغییر کرد.');
    }

    public function deleteDriver(Request $request)
    {
        $request->validate([
            'driver_id' => 'required|integer|exists:users,id',
        ]);
        $driver = User::where('type', 'driver')
            ->where('id', $request->driver_id)
            ->first();
        if (!$driver) {
            return back()->with('error', 'راننده پیدا نشد.');   
        }
        $driver->delete();
        return back()->with('success', 'راننده با موفقیت حذف شد.');

    }

    public function saveDriver(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:users,id',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'father_name' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'national_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',

            'id_card_front' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
            'id_card_back' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
            'id_card_selfie' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',

            'license_number' => 'nullable|string|max:50',
            'license_front' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
            'license_back' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',

            'car_type' => 'nullable|string|max:100',
            'car_plate' => 'nullable|string|max:20',
            'car_model' => 'nullable|string|max:100',
            'car_card_front' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
            'car_card_back' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
            'car_insurance' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
        ],[
            'id.required' => 'شناسه کاربر الزامی است.',
            'id.exists'   => 'کاربری با این شناسه پیدا نشد.',

            '*.image' => 'فایل انتخاب شده باید تصویر باشد.',
            '*.mimes' => 'فرمت تصویر باید JPG یا PNG باشد.',
            'id_card_front.max' => 'حجم تصویر کارت ملی جلو نباید بیشتر از 3 مگابایت باشد.',
            'id_card_back.max' => 'حجم تصویر کارت ملی پشت نباید بیشتر از 3 مگابایت باشد.',
            'id_card_selfie.max' => 'حجم تصویر سلفی کارت ملی نباید بیشتر از 3 مگابایت باشد.',
            'profile_photo.max' => 'حجم عکس پروفایل نباید بیشتر از 3 مگابایت باشد.',

            'first_name.max' => 'طول نام نباید بیشتر از 100 کاراکتر باشد.',
            'last_name.max' => 'طول نام خانوادگی نباید بیشتر از 100 کاراکتر باشد.',
            'father_name.max' => 'طول نام پدر نباید بیشتر از 100 کاراکتر باشد.',
            'address.max' => 'طول آدرس نباید بیشتر از 255 کاراکتر باشد.',

            'string' => 'مقدار وارد شده باید متن باشد.',
            'date'   => 'تاریخ وارد شده معتبر نیست.',
        ]);

        $uploadPath = 'drivers';

       foreach ([
            'id_card_front', 'id_card_back', 'id_card_selfie', 'profile_photo',
            'license_front', 'license_back',
            'car_card_front', 'car_card_back', 'car_insurance',
            'car_front_image', 'car_back_image', 'car_left_image', 'car_right_image', 'car_front_seats_image', 'car_back_seats_image'
        ] as $fileField) {
            if ($request->hasFile($fileField)) {
                $validated[$fileField] = $request->file($fileField)->store($uploadPath, 'public');
            }
        }

        $user_db = User::where('id', $validated['id'])->first(); 

        $driver = $user_db->userable;
        if (!$driver) {
            $driver = Driver::create($validated);
            $user_db->userable()->associate($driver);
            $user_db->save();
        } else {
            $driver->update($validated);
        }

        return redirect()->back()->with('success', 'اطلاعات با موفقیت ثبت شد.');
    
    }
    public function searchDriver(Request $request)
    {
        $query = $request->get('q', '');
        $results = collect();

        if (is_numeric($query)) {
            $users = User::where('userable_type', 'App\Models\Driver')
                ->where('userable_id', $query)
                ->with('userable') 
                ->limit(10)
                ->get();

            foreach ($users as $user) {
                if ($user->userable) {
                    $results->push([
                        'driver_id' => $user->userable->id,        
                        'user_id' => $user->id,                    
                        'first_name' => $user->userable->first_name,
                        'last_name' => $user->userable->last_name,
                    ]);
                }
            }
        } else {
            $drivers = Driver::where(function($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%");
                })
                ->with(['user']) 
                ->limit(10)
                ->get();

            foreach ($drivers as $driver) {
                $results->push([
                    'driver_id' => $driver->id,                       
                    'user_id' => $driver->user->id ?? null,           
                    'first_name' => $driver->first_name,
                    'last_name' => $driver->last_name,
                ]);
            }
        }

        return response()->json($results);
    }

    // Trips management
    public function assignDriver(Request $request)
    {
        $tripId = request()->query('trip_id');
        $driverId = request()->query('driver_id');

        $trip =Trip::find($tripId);
        if (!$trip) {
            return response()->json(['success' => false, 'message' => 'سفر پیدا نشد.'], 404); 
        }

        $driver = User::where('type', 'driver')
                ->where('id', $driverId)
                ->first();
        if (!$driver) {
            return response()->json(['success' => false, 'message' => 'راننده پیدا نشد.'], 404); 
        }
        $trip->driver_id = $driver->id;
        $trip->status = 'pending-payment';
        $trip->save();
        $site_name = setting('site_name');
        $message = "راننده محترم، سفر {$tripId} به شما اختصاص داده شد. لطفا وارد سامانه {$site_name} شوید.";
        SMS::sendPattern($driver->phone , $params = [$message , ''], '399329');
        return response()->json(['success' => true, 'message' => 'راننده با موفقیت به سفر اختصاص داده شد.']);
    }

    function cancelTrip(Request $request)
    {
        $request->validate([
            'trip_id' => 'required|integer|exists:trips,id',
        ]);

        $trip = Trip::find($request->trip_id);
        if (!$trip) {
            return back()->with('error', 'سفر پیدا نشد.');
        }

        $trip->status = 'cancelled';
        $trip->save();

        return back()->with('success', 'سفر با موفقیت لغو شد.');
    }

}
