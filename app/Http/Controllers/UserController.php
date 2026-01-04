<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Passenger;
use App\Models\Driver;
use App\Models\CarType;
use App\Models\Admin;
use App\Models\DriverReviewLog;
use App\Notifications\DriverSubmitted;
use App\Models\DriverNotification;
use App\Facades\SMS;
use App\Models\Trip;

class UserController extends Controller
{
    public function showLoginForm()
    {
        // If the user is already authenticated, redirect to profile
        if (Auth::check()) {
            return redirect()->route('user.profile');
        }

        return view('login');
    }

    public function home()
    {
        if (Auth::check() && optional(Auth::user())->type === 'driver') {
            return redirect()->route('user.profile');
        }
        if (Auth::check()) {
            $trips = Trip::with(['carType', 'passenger', 'driver'])
            ->where('passenger_id', Auth::user()->id)
            ->whereIn('status', ['pending', 'ongoing', 'paid'])
            ->get();

        }
        

        return view('welcome', [
            'carTypes' => CarType::all(),
            'zones' => \App\Models\Zone::all(),
            'trips' => $trips ?? [],
        ]);
    }

    public function profile(Request $request)
    {
        $user = Auth::user();
        
        // if no authenticated user, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

        // passenger -> user profile view
        if ($user->type === 'passenger') {
            // prepare birth date display in Jalali if possible
            $bd = optional($user->userable)->birth_date;
            $birth_display = $this->formatBirthForDisplay($bd);

            $trips = Trip::with(['carType', 'passenger', 'driver'])
                ->where('passenger_id', $user->id)
                ->orderBy('id', 'desc') 
                ->paginate(30);

            return view('user-profile', compact('user', 'birth_display', 'trips'));
        }


        // driver -> check driver record completeness
        if ($user->type === 'driver') {
            // try to get the driver record via relation or by id
            $driver = null;
            if (method_exists($user, 'userable') && $user->userable) {
                $driver = $user->userable;
            } elseif ($user->userable_id) {
                $driver = Driver::find($user->userable_id);
            }

            // if no driver record found, show unverified view
            if (!$driver) {
                return view('driver-unverfied', compact('user'));
            }

            // consider driver 'complete' if a set of important fields are present
            $required = [
                'license_number', 'license_front', 'license_back',
                'profile_photo', 'id_card_front', 'id_card_back',
                'car_plate', 'car_model'
            ];

            $complete = true;
            foreach ($required as $f) {
                if (empty($driver->$f)) { $complete = false; break; }
            }

            if ($complete) {
                $bd = optional($driver)->birth_date ?? optional($user->userable)->birth_date ?? null;
                $birth_display = $this->formatBirthForDisplay($bd);
                if( $user->status === 'awaiting') {
                    return view('driver-in-prossese', compact('user','driver','birth_display'));
                }
                if($user->status == 'rejected' && $request->query('slug') == 'retry') {
                    User::where('id', $user->id)->update(['status' => 'pending']);
                    return view('driver-unverfied', compact('user','driver','birth_display'));
                }
                if($user->status === 'rejected') {
                    $lastRejected = DriverReviewLog::where('driver_id', $user->id)
                        ->where('status', 'rejected')
                        ->latest() 
                        ->first();
                    
                    $message = $lastRejected ? $lastRejected->message : 'اطلاعات ارسالی شما مورد تایید قرار نگرفت. لطفا با پشتیبانی تماس بگیرید.';

                    return view('driver-notacceped', compact('user','driver','birth_display','message'));
                }
                if($user->status === 'inactive') {
                    $message = 'حساب کاربری شما غیرفعال شده است. لطفا با پشتیبانی تماس بگیرید.';
                    return view('driver-notacceped', compact('user','driver','birth_display','message'));
                }
                if($user->status === 'pending') {
                    
                    return view('driver-unverfied', compact('user'));
                }
                if($user->status === 'active') {
                    
                    return view('driver-proile', compact('user','driver','birth_display'));
                }
                

                
                
            }
            
            if ($user->status === 'awaiting') {
                return view('driver-in-prossese', compact('user','driver'));
            }
            if($user->status == 'rejected' && $request->query('slug') == 'retry') {
                User::where('id', $user->id)->update(['status' => 'pending']);
                return view('driver-unverfied', compact('user','driver'));
            }
            if($user->status === 'rejected') {

                $lastRejected = DriverReviewLog::where('driver_id', $user->id)
                    ->where('status', 'rejected')
                    ->latest() 
                    ->first();

                $message = $lastRejected ? $lastRejected->message : 'اطلاعات ارسالی شما مورد تایید قرار نگرفت. لطفا با پشتیبانی تماس بگیرید.';

                return view('driver-notacceped', compact('user','driver','message'));
            }
            $bd = optional($driver)->birth_date ?? optional($user->userable)->birth_date ?? null;
            $birth_display = $this->formatBirthForDisplay($bd);
            return view('driver-unverfied', compact('user','driver','birth_display'));
        }

        // fallback
        return view('user-profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'national_code' => 'nullable|string|max:20',
            // accept jalali like 1404/05/08 or gregorian 2025-08-30; we'll normalize below
            'birth_date' => ['nullable','string','regex:/^\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}$/'],
        ]);

        $newPhone = isset($data['phone']) ? trim($data['phone']) : '';

        // If phone changed and not empty -> require verification
        if ($newPhone !== '' && $newPhone !== $user->phone) {
            if ($this->phoneIsTakenByOther($newPhone, $user->id)) {
                return back()->withErrors(['phone' => 'این شماره قبلاً در سیستم ثبت شده است']);
            }

            $code = $this->generateVerificationCode();
            $pendingBirth = $this->normalizeJalaliString($data['birth_date'] ?? null);

            $this->storeVerificationAndPending($newPhone, $code, $user->id, [
                'name' => $data['name'] ?? null,
                'national_code' => $data['national_code'] ?? null,
                'birth_date' => $pendingBirth,
            ]);

            // TODO: send SMS with the $code to $newPhone
            SMS::sendPattern($newPhone, [$code], 399329);

            return view('verification-code', ['phone' => $newPhone, 'role' => $user->type]);
        }

        // No phone change or phone empty — apply updates immediately
        // Update userable (passenger/driver) fields if present
        $profileData = [];
        if (!empty($data['name'])) $profileData['name'] = $data['name'];
        if (!empty($data['national_code'])) $profileData['national_code'] = $data['national_code'];

        // For passengers we store the birth date exactly as a normalized Jalali string YYYY/MM/DD
        if ($user->type === 'passenger') {
            if (!empty($data['birth_date'])) {
                $normalized = $this->normalizeJalaliString($data['birth_date']);
                if ($normalized) $profileData['birth_date'] = $normalized;
            }
        } else {
            if (!empty($data['birth_date'])) {
                $profileData['birth_date'] = $this->normalizeDriverBirthDate($data['birth_date']);
            }
        }

        if (!empty($profileData)) {
            if ($user->type === 'passenger' && $user->userable) {
                $user->userable->update($profileData);
            } elseif ($user->type === 'driver' && $user->userable) {
                $user->userable->update($profileData);
            }
        }

        // Update phone only if provided and same as existing (or empty -> leave)
        if ($newPhone !== '' && $newPhone === $user->phone) {
            // nothing to do
        }

        return back();
    }
    public function resendVerificationCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $phone = $request->phone;

        $stored = null;
        $keys = ["verification_code:{$phone}", $phone];

        foreach ($keys as $k) {
            if (Cache::has($k)) {
                $stored = Cache::get($k);
                break;
            }
        }

        if (!$stored) {
            return response()->json([
                'message' => 'کدی برای این شماره وجود ندارد یا منقضی شده است.'
            ], 404);
        }

        SMS::sendPattern($phone, [$stored], 399329);

        return response()->json([
            'message' => 'کد دوباره ارسال شد.'
        ], 200);
    }




    public function loginVerification(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string',
            'role'  => 'required|in:passenger,driver',
        ]);

        $phone = $data['phone'];
        $role  = $data['role'];

        $user = User::withTrashed()->where('phone', $phone)->first();

        if ($user && ($user->trashed() || $user->status === 'deleted')) {
            return redirect()->back()->with([
                'error' => 'این شماره مسدود شده است و نمی‌توانید وارد شوید'
            ]);
        }


        try {
            $code = random_int(10000, 99999);
        } catch (\Exception $e) {
            $code = mt_rand(10000, 99999);
        }

        Cache::put($phone, $code, now()->addMinutes(10));

        SMS::sendPattern($phone, [$code], 399329);

        return view('verification-code', ['phone' => $phone, 'role' => $role]);
    }


    public function loginOrRegister(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string',
            'role'  => 'required|in:passenger,driver',
        ]);

        $phone = $data['phone'];
        $role  = $data['role'];

        $user = User::where('phone', $phone)->first();

        if ($user) {
            return response()->json([
                'status' => 'login',
                'message' => 'کاربر با موفقیت وارد شد',
                'user' => $user
            ]);
        }

        if ($role === 'passenger') {
            $userable = Passenger::create([]); 
        } else { // driver
            $userable = Driver::create([]); 
        }

        $user = User::create([
            'phone' => $phone,
            'type' => $role,
            'userable_id' => $userable->id,
            'userable_type' => get_class($userable),
            
            
        ]);
        
        if ($role === 'driver') {
            $user->status = 'panding';
            $user->save();
        } elseif ($role === 'passenger') {
            $user->status = 'active';
            $user->save();
        } 

        return response()->json([
            'status' => 'register',
            'message' => 'کاربر با موفقیت ثبت نام شد',
            'user' => $user
        ]);
    }

    public function checkVerification(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string',
            'code'  => 'required|string',
            'role'  => 'required|in:passenger,driver',
        ]);

        $phone = $data['phone'];
        $code  = $data['code'];
        $role  = $data['role'];

        // get the stored code from cache
        $stored = null;
        $foundKey = null;
        $keys = ["verification_code:{$phone}", $phone];
        foreach ($keys as $k) {
            if (Cache::has($k)) {
                $stored = Cache::get($k);
                $foundKey = $k;
                break;
            }
        }

        if (!$stored || (string)$stored !== (string)$code) {
            $bag = new MessageBag(['code' => 'کد وارد شده اشتباه است']);
            $errors = new ViewErrorBag();
            $errors->put('default', $bag);

            return view('verification-code', [
                'phone'  => $phone,
                'role'   => $role,
                'errors' => $errors,
            ]);
        }

        /**
         * =====================================
         * Pending profile change
         * =====================================
         */
        $pendingKey = "pending_profile:{$phone}";
        if (Cache::has($pendingKey)) {
            $pending = Cache::get($pendingKey);
            $targetUser = User::find($pending['user_id'] ?? null);

            if (!$targetUser) {
                $bag = new MessageBag(['code' => 'کاربر مربوطه پیدا نشد']);
                $errors = new ViewErrorBag();
                $errors->put('default', $bag);

                return view('verification-code', [
                    'phone'  => $phone,
                    'role'   => $role,
                    'errors' => $errors,
                ]);
            }

            $other = User::where('phone', $phone)
                ->where('id', '!=', $targetUser->id)
                ->first();

            if ($other) {
                $bag = new MessageBag(['code' => 'این شماره قبلاً به حساب دیگری اختصاص داده شده است']);
                $errors = new ViewErrorBag();
                $errors->put('default', $bag);

                return view('verification-code', [
                    'phone'  => $phone,
                    'role'   => $role,
                    'errors' => $errors,
                ]);
            }

            // apply pending changes
            $targetUser->phone = $phone;
            $targetUser->save();

            if (!empty($pending['data']) && $targetUser->userable) {
                $targetUser->userable->update($pending['data']);
            }

            if ($foundKey) Cache::forget($foundKey);
            Cache::forget($pendingKey);

            try {
                Auth::login($targetUser, true);
            } catch (\Throwable $e) {}

            if (session()->has('pending_trip')) {
                return redirect()->route('trip.store.after.login');
            }

            return redirect()->route('user.profile');
        }

        /**
         * =====================================
         * Normal login / register
         * =====================================
         */
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            if ($role === 'passenger') {
                $userable = Passenger::create([]);
                $status = 'active';
            } else {
                $userable = Driver::create([]);
                $status = 'panding';
            }

            $user = User::create([
                'phone'          => $phone,
                'type'           => $role,
                'status'         => $status,
                'userable_id'    => $userable->id,
                'userable_type'  => get_class($userable),
            ]);
        }

        try {
            Auth::login($user, true);
        } catch (\Throwable $e) {}

        if ($foundKey) {
            Cache::forget($foundKey);
        } else {
            Cache::forget($phone);
        }

        if (session()->has('pending_trip')) {
            return redirect()->route('trip.store.after.login');
        }

        return redirect()->route('user.profile');
    }


    public function debugCachedCode(Request $request)
    {
        // Prevent exposing codes in production — only allow in debug mode
        if (!config('app.debug')) {
            return response('Forbidden', 403);
        }

        $phone = $request->query('phone');
        if (empty($phone)) {
            return response()->json(['error' => 'phone parameter required'], 400);
        }

        // Support either a prefixed key (recommended) or the raw phone key
        $keys = ["verification_code:{$phone}", $phone];
        foreach ($keys as $k) {
            if (Cache::has($k)) {
                return response()->json([
                    'phone' => $phone,
                    'key' => $k,
                    'code' => Cache::get($k),
                ]);
            }
        }

        return response()->json(['phone' => $phone, 'found' => false], 404);
    }

    public function logout(Request $request)
    {
        // only for authenticated users
        try {
            Auth::logout();
        } catch (\Throwable $e) {
            // ignore
        }

        // Invalidate the session and regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function jalaliToGregorian(int $jy, int $jm, int $jd): array
    {
        $jy += 1595;
        $days = -355668 + (365 * $jy) + (int)(($jy / 33) * 8) + (int)((($jy % 33) + 3) / 4);
        for ($i = 0; $i < $jm - 1; $i++) {
            $days += ($i < 6) ? 31 : 30;
        }
        $days += $jd - 1;

        $g = $days + 79;
        $gy = 1600 + 400 * (int)($g / 146097);
        $g = $g % 146097;
        $leap = true;
        if ($g >= 36525) {
            $g--;
            $gy += 100 * (int)($g / 36524);
            $g = $g % 36524;
            if ($g >= 365) $g++;
            else $leap = false;
        }

        $gy += 4 * (int)($g / 1461);
        $g = $g % 1461;
        if ($g >= 366) {
            $leap = false;
            $gy += (int)(($g - 1) / 365);
            $g = ($g - 1) % 365;
        }

        $gd = $g + 1;
        $months = [0, 31, ($leap ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $gm = 0;
        for ($i = 1; $i <= 12; $i++) {
            if ($gd <= $months[$i]) { $gm = $i; break; }
            $gd -= $months[$i];
        }

        return [$gy, $gm, $gd];
    }

    protected function formatBirthForDisplay($date): ?string
    {
        if (empty($date)) return null;

        // accept both 'Y-m-d' and 'Y/m/d' and also full datetime
        if (is_string($date)) {
            $d = trim($date);
            // strip time if present
            if (strpos($d, ' ') !== false) {
                $d = explode(' ', $d)[0];
            }
            $sep = strpos($d, '-') !== false ? '-' : (strpos($d, '/') !== false ? '/' : null);
            if (!$sep) return $d;
            [$y,$m,$day] = explode($sep, $d) + [null,null,null];
            $y = intval($y); $m = intval($m); $day = intval($day);

            // if looks like already Jalali (reasonable range 1300-1500) just format as YYYY/MM/DD
            if ($y >= 1300 && $y <= 1500) {
                return sprintf('%04d/%02d/%02d', $y, $m, $day);
            }

            // convert to Jalali
            [$jy,$jm,$jd] = $this->gregorianToJalali($y,$m,$day);
            return sprintf('%04d/%02d/%02d', $jy, $jm, $jd);
        }

        return null;
    }

    protected function gregorianToJalali(int $g_y, int $g_m, int $g_d): array
    {
        $gy = $g_y-1600;
        $gm = $g_m-1;
        $gd = $g_d-1;

        $g_day_no = 365*$gy + (int)(($gy+3)/4) - (int)(($gy+99)/100) + (int)(($gy+399)/400);
        for ($i=0; $i < $gm; ++$i) {
            $g_day_no += [31,28,31,30,31,30,31,31,30,31,30,31][$i];
        }
        $g_day_no += $gd;

        $j_day_no = $g_day_no - 79;

        $j_np = (int)($j_day_no / 12053); // 12053 = 33 years
        $j_day_no = $j_day_no % 12053;

        $jy = 979 + 33*$j_np + 4*(int)($j_day_no/1461);
        $j_day_no %= 1461;

        if ($j_day_no >= 366) {
            $jy += (int)(($j_day_no-366)/365);
            $j_day_no = ($j_day_no-366)%365;
        }

        $jm = 0;
        for ($i = 0; $i < 11; $i++) {
            $v = ($i < 6) ? 31 : 30;
            if ($j_day_no < $v) { $jm = $i+1; break; }
            $j_day_no -= $v;
        }
        if ($jm == 0) $jm = 12;
        $jd = $j_day_no + 1;

        return [$jy, $jm, $jd];
    }

    protected function normalizeJalaliString(?string $input): ?string
    {
        if (empty($input)) return null;
        $s = trim($input);
        // allow both / and - separators
        if (preg_match('/^(\d{2,4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $s, $m)) {
            $y = intval($m[1]);
            $mo = intval($m[2]);
            $d = intval($m[3]);
            // pad month/day
            $mo = sprintf('%02d', $mo);
            $d = sprintf('%02d', $d);
            return sprintf('%04d/%s/%s', $y, $mo, $d);
        }
        return null;
    }

    private function phoneIsTakenByOther(string $phone, int $userId): bool
    {
        return User::where('phone', $phone)->where('id', '!=', $userId)->exists();
    }
    private function generateVerificationCode(): int
    {
        try {
            return random_int(10000, 99999);
        } catch (\Throwable $e) {
            return mt_rand(10000, 99999);
        }
    }

    private function storeVerificationAndPending(string $phone, int $code, int $userId, array $profileData): void
    {
        Cache::put("verification_code:{$phone}", $code, now()->addMinutes(10));
        Cache::put("pending_profile:{$phone}", [
            'user_id' => $userId,
            'data' => $profileData,
        ], now()->addMinutes(10));
    }

    private function normalizeDriverBirthDate(string $input): ?string
    {
        $bd = trim($input);
        $sep = strpos($bd, '/') !== false ? '/' : (strpos($bd, '-') !== false ? '-' : null);
        if (!$sep) return null;
        [$y,$m,$d] = explode($sep, $bd) + [null,null,null];
        $y = intval($y); $m = intval($m); $d = intval($d);
        if ($y >= 1300 && $y <= 1500) {
            [$gy,$gm,$gd] = $this->jalaliToGregorian($y,$m,$d);
            return sprintf('%04d-%02d-%02d', $gy,$gm,$gd);
        }
        return sprintf('%04d-%02d-%02d', $y,$m,$d);
    }
    // Deriver Controllers 
    public function driverSave(Request $request)
    {
        if (!Auth::guard('web')->check() || !$user = Auth::user()) {
            return redirect()->route('login');
        }

        if ($user->type !== 'driver') {
            return redirect()->route('user.profile');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'father_name' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'national_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',

            'id_card_front' => 'required|image|mimes:jpg,jpeg,png|max:3072',
            'id_card_back' => 'required|image|mimes:jpg,jpeg,png|max:3072',
            'id_card_selfie' => 'required|image|mimes:jpg,jpeg,png|max:3072',
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png|max:3072',

            'license_number' => 'required|string|max:50',
            'license_front' => 'required|image|mimes:jpg,jpeg,png|max:3072',
            'license_back' => 'required|image|mimes:jpg,jpeg,png|max:3072',

            'car_type' => 'required|string|max:100',
            'car_plate' => 'required|string|max:20',
            'car_model' => 'required|string|max:100',
            'car_card_front' => 'required|image|mimes:jpg,jpeg,png|max:3072',
            'car_card_back' => 'required|image|mimes:jpg,jpeg,png|max:3072',
            'car_insurance' => 'required|image|mimes:jpg,jpeg,png|max:3072',

            'car_front_image'        => 'required|image|mimes:jpg,jpeg,png|max:3072',
            'car_back_image'         => 'required|image|mimes:jpg,jpeg,png|max:3072',
            'car_left_image'         => 'required|image|mimes:jpg,jpeg,png|max:3072',
            'car_right_image'        => 'required|image|mimes:jpg,jpeg,png|max:3072',
            'car_front_seats_image'  => 'required|image|mimes:jpg,jpeg,png|max:3072',
            'car_back_seats_image'   => 'required|image|mimes:jpg,jpeg,png|max:3072',
        ],[
            'id.required' => 'شناسه کاربر الزامی است.',
            'id.exists'   => 'کاربری با این شناسه پیدا نشد.',

            '*.image' => 'فایل انتخاب شده باید تصویر باشد.',
            '*.mimes' => 'فرمت تصویر باید JPG یا PNG باشد.',

            'id_card_front.max' => 'حجم تصویر کارت ملی جلو نباید بیشتر از 3 مگابایت باشد.',
            'id_card_back.max' => 'حجم تصویر کارت ملی پشت نباید بیشتر از 3 مگابایت باشد.',
            'id_card_selfie.max' => 'حجم تصویر سلفی کارت ملی نباید بیشتر از 3 مگابایت باشد.',
            'profile_photo.max' => 'حجم عکس پروفایل نباید بیشتر از 3 مگابایت باشد.',
            'first_name.required' => 'وارد کردن نام الزامی است.',
            
            'last_name.required' => 'وارد کردن نام خانوادگی الزامی است.',
            'father_name.required' => 'وارد کردن نام پدر الزامی است.',
            'birth_date.required' => 'وارد کردن تاریخ تولد الزامی است.',
            'national_code.required' => 'وارد کردن کد ملی الزامی است.',
            'phone.required' => 'وارد کردن شماره تلفن الزامی است.',
            'address.required' => 'وارد کردن آدرس الزامی است.',

            'id_card_front.required' => 'بارگذاری کارت ملی جلو الزامی است.',
            'id_card_back.required' => 'بارگذاری کارت ملی پشت الزامی است.',
            'id_card_selfie.required' => 'بارگذاری سلفی کارت ملی الزامی است.',
            'profile_photo.required' => 'بارگذاری عکس پروفایل الزامی است.',

            'license_number.required' => 'وارد کردن شماره گواهینامه الزامی است.',
            'license_front.required' => 'بارگذاری گواهینامه جلو الزامی است.',
            'license_back.required' => 'بارگذاری گواهینامه پشت الزامی است.',

            'car_type.required' => 'وارد کردن نوع خودرو الزامی است.',
            'car_plate.required' => 'وارد کردن پلاک خودرو الزامی است.',
            'car_model.required' => 'وارد کردن مدل خودرو الزامی است.',
            'car_card_front.required' => 'بارگذاری کارت خودرو جلو الزامی است.',
            'car_card_back.required' => 'بارگذاری کارت خودرو پشت الزامی است.',
            'car_insurance.required' => 'بارگذاری بیمه خودرو الزامی است.',

            'car_front_image.required' => 'بارگذاری تصویر نمای جلوی خودرو الزامی است.',
            'car_back_image.required' => 'بارگذاری تصویر نمای عقب خودرو الزامی است.',
            'car_left_image.required' => 'بارگذاری تصویر نمای چپ خودرو الزامی است.',
            'car_right_image.required' => 'بارگذاری تصویر نمای راست خودرو الزامی است.',
            'car_front_seats_image.required' => 'بارگذاری تصویر صندلی‌های جلو الزامی است.',
            'car_back_seats_image.required' => 'بارگذاری تصویر صندلی‌های عقب الزامی است.',


            // ------------------ NEW ERRORS ------------------
            'car_front_image.max' => 'حجم تصویر نمای جلوی خودرو نباید بیشتر از 3 مگابایت باشد.',
            'car_back_image.max' => 'حجم تصویر نمای عقب خودرو نباید بیشتر از 3 مگابایت باشد.',
            'car_left_image.max' => 'حجم تصویر نمای چپ خودرو نباید بیشتر از 3 مگابایت باشد.',
            'car_right_image.max' => 'حجم تصویر نمای راست خودرو نباید بیشتر از 3 مگابایت باشد.',
            'car_front_seats_image.max' => 'حجم تصویر صندلی‌های جلو نباید بیشتر از 3 مگابایت باشد.',
            'car_back_seats_image.max' => 'حجم تصویر صندلی‌های عقب نباید بیشتر از 3 مگابایت باشد.',

            'first_name.max' => 'طول نام نباید بیشتر از 100 کاراکتر باشد.',
            'last_name.max' => 'طول نام خانوادگی نباید بیشتر از 100 کاراکتر باشد.',
            'father_name.max' => 'طول نام پدر نباید بیشتر از 100 کاراکتر باشد.',
            'address.max' => 'طول آدرس نبا
            ید بیشتر از 255 کاراکتر باشد.',

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
        $user_db = User::with('userable')->find($user->id);
        $user_db->update(['status' => 'awaiting']); 

        $driver = $user_db->userable;
        if (!$driver) {
            $driver = Driver::create($validated);
            $user_db->userable()->associate($driver);
            $user_db->save();
        } else {
            $driver->update($validated);
        }

        $admins = Admin::all();
        $existing = DriverNotification::where('driver_id', $user_db->id)->latest()->first();
        if ($user_db->status == 'awaiting') {

            if ($existing) {
                $existing->seen_by_admin_id = null;
                $existing->save();
                return redirect()->back()->with('success', 'اطلاعات با موفقیت ثبت شد.');
            }
        }
        
        $notification = new DriverSubmitted($user_db);
        foreach ($admins as $admin) {
            $admin->notify($notification);
        }

        return redirect()->back()->with('success', 'اطلاعات با موفقیت ثبت شد.');
    }



}