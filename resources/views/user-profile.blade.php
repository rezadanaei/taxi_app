<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Page Title -->
  <title>حساب کاربری</title>
  <link rel="shortcut icon" href="{{ asset('/img/fav.png') }}" type="image/x-icon">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


  @if(auth()->check())
      @php
          $token = auth()->user()->createToken('web')->plainTextToken;
      @endphp
      <meta name="api-token" content="{{ $token }}">
  @endif

  


  <!-- Page Style -->
  <style>
    :root {

        --main-color: {{ setting('colers_primary') }};
        --second-color: {{ setting('colers_secondary') }};
        --Third-color: {{ setting('colers_tertiary') }};
      }
  </style>
  <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
</head>
<body>

  <!-- User Profile page -->
   <div class="user-profile-container max-width">

    <!-- User info -->
    <div class="u-profile-info">

      <img class="u-profile-info-img" src="{{ asset('/img/no-photo.png') }}" alt="تصویر کاربری">

      <section>
        <div class="u-profile-username">
          @php
            $displayName = null;
            if (!empty($user->name)) {
                $displayName = $user->name;
            } elseif (!empty(optional($user->userable)->name)) {
                $displayName = optional($user->userable)->name;
            }
          @endphp
          <h2>{{ $displayName ? $displayName : 'نام کاربری' }}</h2>
          <form method="POST" action="{{ route('logout') }}" style="display:inline" class="logout-form" id="logoutForm">
            @csrf
            <button type="submit" id="logoutBtn">خروج</button>
          </form>
        </div>
        <div class="u-profile-type">مسافر</div>
      </section>
    
      @php
        // Compute previous URL — if it was a login-related path, go to home instead.
        $prev = url()->previous();
        $prevPath = parse_url($prev, PHP_URL_PATH) ?: '';
        $loginPaths = ['/login', '/login/verify-code'];
        $backUrl = in_array($prevPath, $loginPaths, true) ? route('home') : $prev;
      @endphp

      <a href="{{ $backUrl }}" class="u-profile-back-btn"><img class="u-profile-back-img" src="{{ asset('/img/back.svg') }}" alt="بازگشت"></a>

    </div>
    <!-- User info end -->

    <div class="u-profile-content">
      <!-- Tabs -->
      <div class="u-profile-tabs">
        <div class="active">تاریخچه سفر ها</div>
        <div>جزئیات حساب</div>
      </div>
      <!-- Tabs end -->

      <!-- Tabs content -->
      <div class="u-profile-tab-content">
        <!-- Tab 1 -->
        <div class="u-profile-tab-item active">
          <div class="passenger-current-trip">
            <ul>
              <!-- Current Trip item -->
              @if(count($trips ?? []) > 0)
                @foreach ($trips as $trip)
                  <li>
                      <div class="passenger-trip-item">
                          @php
                              $tripDT = tripDate($trip->start_date);
                          @endphp
                          <div class="passenger-item-title">
                              <div class="trip-id">کد سفر: {{ $trip->id }}</div>
                              <div class="trip-state">{{ ucfirst($trip->status) }}</div>
                          </div>
                          <img src="{{ asset('img/down.svg') }}" alt="فلش">
                      </div>

                      <div class="passenger-trip-content">
                          <div class="trip-extra-info-md">
                              <div class="trip-date">تاریخ: {{ $tripDT['date'] }}</div>
                              <span>-</span>
                              <div class="trip-time">ساعت: {{ $tripDT['time'] }}</div>
                              @if(isset($trip->payment_id))
                                  <span>-</span>
                                  <div class="trip-pay-id">شناسه پرداخت: {{ $trip->payment_id }}</div>
                              @endif
                          </div>

                          <!-- Total price -->
                          <div class="trip-total-price">
                              <div class="total-price"><span>هزینه سفر:</span> {{ number_format($trip->cost) }} تومان</div>
                              <a href="#">گزارش مشکل</a>
                          </div>

                          <!-- Driver Info -->
                          @if ($trip->driver)
                          <div class="trip-driver-info">
                              <img src="{{ optional($trip->driver->userable)->profile_photo ? asset('storage/' . $trip->driver->userable->profile_photo) : asset('img/no-photo.png') }}" alt="تصویر راننده">
                              <div class="driver-info">
                                  <p>
                                      <span>راننده: </span>
                                      {{ optional($trip->driver->userable)->first_name ?? '' }}
                                      {{ optional($trip->driver->userable)->last_name ?? '' }}
                                  </p>
                                  <p><span>ماشین: </span>{{ optional($trip->driver->userable->car)->name ?? 'نامشخص' }}</p>
                                  <p><span>پلاک: </span>{{ optional($trip->driver->userable)->car_plate ?? 'نامشخص' }}</p>
                              </div>
                              <a href="tel:{{ optional($trip->driver)->phone ?? '' }}" class="call-to-driver">
                                  {{ optional($trip->driver)->phone ?? 'نامشخص' }}
                              </a>
                              @php
                                  $hasPaid = \App\Models\Payment::where('payable_type', \App\Models\Trip::class)
                                              ->where('payable_id', $trip->id)
                                              ->where('status', 'success')
                                              ->exists();
                              @endphp

                              @if($trip->status == 'pending-payment' && !$hasPaid)
                                  <a href="{{ route('trip.payment', ['trip_id' => $trip->id]) }}" class="call-to-driver">
                                      پرداخت
                                  </a>
                              @endif
                          </div>
                          @endif

                          @php
                              $origins = json_decode($trip->origins, true);
                              $destinations = json_decode($trip->destinations, true);
                          @endphp

                          <ul class="trip-locations">
                              @foreach($origins as $index => $origin)
                                  <li><span>مبدا {{ $index + 1 }}: </span>{{ $origin['address'] ?? 'آدرس موجود نیست' }}</li>
                              @endforeach

                              @foreach($destinations as $index => $destination)
                                  <li><span>مقصد {{ $index + 1 }}: </span>{{ $destination['address'] ?? 'آدرس موجود نیست' }}</li>
                              @endforeach
                          </ul>

                          <div class="user-form-desc">
                              <p>{{ $trip->caption ?? 'توضیحاتی که کاربر در فرم ثبت کرده است.' }}</p>
                          </div>
                      </div>
                  </li>
                @endforeach
                {{ $trips->links('pagination::bootstrap-5') }}
              @endif
              <!-- Current Trip item end -->
              
              <li>
                <div class="passenger-trip-item">
                  <div class="passenger-item-title"><div class="trip-id">کد سفر: 1245</div> <div class="trip-state">تکمیل شده</div> </div>
                  <img src="{{ asset('/img/down.svg') }}" alt="فلش">
                </div>
                <div class="passenger-trip-content">

                  <div class="trip-extra-info-md"><div class="trip-date">تاریخ: 08 مرداد 1404</div><span>-</span><div class="trip-time">ساعت: 22:16</div><span>-</span><div class="trip-pay-id">شناسه پرداخت: 175322158</div></div>

                  <!-- Total price -->
                  <div class="trip-total-price">
                    <div class="total-price"><span>هزینه سفر:</span> 240.000 تومان</div>
                    <a href="#">گزارش مشکل</a>
                  </div>

                  <!-- Driver Info -->
                  <div class="trip-driver-info">
                    <img src="{{ asset('/img/no-photo.png') }}" alt="تصویر راننده">
                    <div class="driver-info">
                      <p><span>راننده: </span>اسم راننده</p>
                      <p><span>ماشین: </span> مدل ماشین</p>
                      <p><span>پلاک: </span>21 ب 341 ایران 99</p>
                    </div>
                    <a href="tel:09123456789" class="call-to-driver">09123456789</a>
                    

                  </div>

                  <ul class="trip-locations">
                    <li><span>مبدا 1: </span>آدرس مبدا اول</li>
                    <li><span>مبدا 2: </span>آدرس مبدا دوم</li>
                    <li><span>مقصد 1: </span>آدرس مقصد اول</li>
                  </ul>

                  <div class="user-form-desc">
                    <p>توضیحاتی که کاربر در قسمت ثبت رزرو فرم انجام داده در این قسمت نمایش داده میشود.</p>
                  </div>
                </div>
              </li>

            </ul>
          </div>
        </div>
        <!-- Tab 1 end -->

        <!-- Tab 2 -->
        <div class="u-profile-tab-item">

          <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            <section>
                <input type="text" name="name" id="name" placeholder="نام کاربر" value="{{ old('name', optional($user->userable)->name ?? ($user->name ?? '') ) }}">
              <input type="tel" name="phone" id="phone" placeholder="شماره موبایل" value="{{ old('phone', $user->phone ?? '') }}">
              <input type="text" name="national_code" id="national_code" placeholder="شماره ملی" value="{{ old('national_code', optional($user->userable)->national_code ?? '') }}">
                <input type="text" name="birth_date" id="birth_date" class="jalali-date" placeholder="تاریخ تولد (مثال: 1404/05/08)" value="{{ old('birth_date', $birth_display ?? (optional($user->userable)->birth_date ?? '') ) }}">
            </section>

            <button class="button" type="submit">ذخیره اطلاعات</button>
          </form>

        </div>
        <!-- Tab 2 end -->

      </div>
      <!-- Tabs content end -->
    </div>

   </div>
  <!-- User Profile page end -->
    <button id="request-notification-permission">دریافت اجازه نوتیفیکیشن</button>
  <script src="{{ asset('/js/profile.js') }}"></script>
  <script src="{{ asset('/js/jalalidatepicker.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", async () => {
    if (!("serviceWorker" in navigator) || !("PushManager" in window)) {
        console.log("Web Push در این مرورگر پشتیبانی نمی‌شود.");
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const tokenMeta = document.querySelector('meta[name="api-token"]');

    if (tokenMeta && !localStorage.getItem("auth_token")) {
        localStorage.setItem("auth_token", tokenMeta.getAttribute('content'));
    }

    let swRegistration;
    try {
        swRegistration = await navigator.serviceWorker.register("/sw.js", { scope: "/" });
        console.log("Service Worker با موفقیت ثبت شد");
    } catch (err) {
        console.error("خطا در ثبت Service Worker:", err);
        return;
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = "=".repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, "+").replace(/_/g, "/");
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    async function subscribeUser() {
        try {
            const permission = await Notification.requestPermission();
            if (permission !== "granted") {
                console.log("کاربر اجازه نوتیفیکیشن نداد");
                return false;
            }

            const vapidPublicKey = "{{ env('VAPID_PUBLIC_KEY') ?? 'BKVeFmlrdaKcwXVNSbLtUWqm3vUgFDr4DQVBj104D9MUkwA3itSrbjr7wV3ldP1cMhmCnx8TiOhXrMS3RO0cbZs' }}";
            const applicationServerKey = urlBase64ToUint8Array(vapidPublicKey.trim());

            const existingSub = await swRegistration.pushManager.getSubscription();
            if (existingSub) {
                console.log("اشتراک قدیمی پیدا شد → در حال حذف...");
                await existingSub.unsubscribe();
            }

            const subscription = await swRegistration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey
            });

            console.log("اشتراک جدید با موفقیت ایجاد شد");

            const response = await fetch("{{ route('api.user-push-token.store') ?? '/api/user-push-token' }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                    "X-CSRF-TOKEN": csrfToken || ""
                },
                body: JSON.stringify({
                    type: "web_push",
                    token: JSON.stringify(subscription) 
                })
            });

            if (response.ok) {
                const result = await response.json();
                console.log("توکن Push با موفقیت در سرور ذخیره شد", result);
                return true;
            } else {
                const error = await response.json();
                console.error("خطا در ذخیره توکن:", error);
                return false;
            }

        } catch (err) {
            console.error("خطا در فرآیند Push:", err);
            return false;
        }
    }

    if (Notification.permission === "default") {
        await subscribeUser();
    } else if (Notification.permission === "granted") {
        await subscribeUser();
    }

    const button = document.getElementById("request-notification-permission");
    if (button) {
        button.addEventListener("click", async () => {
            if (tokenMeta) {
                localStorage.setItem("auth_token", tokenMeta.getAttribute('content'));
            }

            const success = await subscribeUser();
            if (success) {
                alert("نوتیفیکیشن وب با موفقیت فعال شد");
            }
        });
    }
});
</script>
</script>

  

</body>
</html>