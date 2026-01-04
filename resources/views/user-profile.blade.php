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


      .alarm {
          position: fixed;
          top: 0;
          left: 50%;
          transform: translate(-50%, -150%);
          width: 92%;
          max-width: 420px;
          background: #fff;
          border-radius: 0 0 14px 14px;
          box-shadow: 0 12px 30px rgba(0,0,0,.12);
          z-index: 9999;
          transition: transform .4s ease;
          font-family: sans-serif;
      }

      .alarm.show {
          transform: translate(-50%, 0);
      }

      .alarm-content {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 14px 16px;
      }

      .alarm-text {
          font-size: 14px;
          color: #222;
          line-height: 1.4;
      }

      .alarm-actions {
          display: flex;
          align-items: center;
          gap: 8px;
      }

      .alarm-btn {
          font-size: 12px;
          padding: 6px 10px;
          border-radius: 8px;
          border: none;
          cursor: pointer;
          background: #4f46e5;
          color: #fff;
      }

      .alarm-btn:hover {
          opacity: .9;
      }

      .alarm-close {
          background: none;
          border: none;
          font-size: 18px;
          cursor: pointer;
          opacity: .6;
      }

      .alarm-close:hover {
          opacity: 1;
      }

      .alarm-timeline {
          height: 3px;
          background: #eee;
          overflow: hidden;
      }

      .alarm-progress {
          height: 100%;
          width: 0%;
          background: #4f46e5;
      }

  </style>
  <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
</head>
<body>
  <div id="trip-alarm" class="alarm">
    <div class="alarm-content">
        <div class="alarm-text"></div>

        <div class="alarm-actions">
            <button class="alarm-btn view">رد کردن</button>
            <button class="alarm-close" aria-label="بستن">✕</button>
        </div>
    </div>

    <div class="alarm-timeline">
        <div class="alarm-progress"></div>
    </div>
  </div>

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
          <div id="notification-warning" style="display: none;">
              <span>مرورگر شما اجازه ارسال نوتیفیکیشن نمی‌دهد.</span>
              <span>
                  برای دریافت نوتیفیکیشن‌ها <button id="request-notification-permission">اینجا</button> کلیک کنید.
              </span>
          </div>

          
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
                                @php
                                    $statuses = [
                                        'pending' => 'در انتظار قبول راننده',
                                        'ongoing' => 'در حال انجام',
                                        'completed' => 'تکمیل شده',
                                        'cancelled' => 'لغو شده',
                                        'rejected' => 'رد شده',
                                        'no-show' => 'عدم حضور',
                                        'paid' => 'پرداخت شده',
                                        'refunded' => 'بازپرداخت شده',
                                        'pending-payment' => 'در انتظار پرداخت',
                                    ];
                                @endphp

                                <div class="trip-state">
                                    {{ $statuses[$trip->status] ?? $trip->status }}
                                </div>

                              
                          </div>
                          <img src="{{ asset('img/down.svg') }}" alt="فلش">
                      </div>

                      <div class="passenger-trip-content">
                          <div class="trip-extra-info-md">
                              <div class="trip-date">تاریخ: {{ $tripDT['date'] }}</div>
                              <span>-</span>
                              <div class="trip-time">ساعت: {{ $tripDT['time'] }}</div>
                              @php
                                  $payment = \App\Models\Payment::where('payable_type', \App\Models\Trip::class)
                                      ->where('payable_id', $trip->id)
                                      ->where('status', 'success')
                                      ->first();
                              @endphp

                              @if($payment)
                                  <span>-</span>
                                  <div class="trip-pay-id">شناسه پرداخت: {{ $payment->ref_id }}</div>
                              @endif
                          </div>

                          <!-- Total price -->
                          <div class="trip-total-price">
                              <div class="total-price"><span>هزینه سفر:</span> {{ number_format($trip->cost) }} تومان</div>
                              <a href="#">گزارش مشکل</a>
                          </div>

                          <!-- Driver Info -->
                               @php
                                  $hasPaid = \App\Models\Payment::where('payable_type', \App\Models\Trip::class)
                                              ->where('payable_id', $trip->id)
                                              ->where('status', 'success')
                                              ->exists();
                               @endphp
                          @if ($trip->driver && $hasPaid)
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
                             
                          </div>
                          @elseif ($trip->driver && !$hasPaid)
                           
                              <div class="trip-driver-info no-info">
                                  <p style="margin:0">برای مشاهده اطلاعات راننده لطفا ابتدا هزینه رزرو سفر را پرداخت کنید.</p>
                                  <a href="{{ route('trip.payment', ['trip_id' => $trip->id]) }}" class="call-to-driver">
                                      پرداخت
                                  </a>
                              
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
                    <a href="tel:{{ setting('support_phone') }}">گزارش مشکل</a>
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
  <script src="{{ asset('/js/profile.js') }}"></script>
  <script src="{{ asset('/js/jalalidatepicker.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", async () => {

        /* ──────────────────────────────────────────────
        * 1) Check browser support
        * ────────────────────────────────────────────── */
        if (!("serviceWorker" in navigator) || !("PushManager" in window)) {
            console.log("Web Push is not supported in this browser.");
            return;
        }

        /* ──────────────────────────────────────────────
        * 2) Read CSRF and API Token
        * ────────────────────────────────────────────── */
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
        const tokenMeta = document.querySelector('meta[name="api-token"]');

        if (tokenMeta && !localStorage.getItem("auth_token")) {
            localStorage.setItem("auth_token", tokenMeta.getAttribute("content"));
        }

        const authToken = localStorage.getItem("auth_token");

        /* ──────────────────────────────────────────────
        * 3) Register Service Worker
        * ────────────────────────────────────────────── */
        let swRegistration;

        try {
            swRegistration = await navigator.serviceWorker.register("/sw.js", { scope: "/" });
            console.log("Service Worker registered successfully");
        } catch (e) {
            console.error("Service Worker registration failed:", e);
            return;
        }

        /* ──────────────────────────────────────────────
        * 4) Convert VAPID Key
        * ────────────────────────────────────────────── */
        function urlBase64ToUint8Array(base64) {
            const padding = "=".repeat((4 - (base64.length % 4)) % 4);
            const base64String = (base64 + padding).replace(/-/g, "+").replace(/_/g, "/");
            const rawData = atob(base64String);
            return Uint8Array.from([...rawData].map(c => c.charCodeAt(0)));
        }

        const vapidPublicKey = "{{ env('VAPID_PUBLIC_KEY') }}";
        const applicationServerKey = urlBase64ToUint8Array(vapidPublicKey);

        /* ──────────────────────────────────────────────
        * 5) Subscribe user only when needed
        * ────────────────────────────────────────────── */
        async function subscribeUserIfNeeded() {
            try {
                // Ask for notification permission only if not yet granted
                if (Notification.permission === "default") {
                    const permission = await Notification.requestPermission();
                    if (permission !== "granted") {
                        console.warn("User denied notification permission.");
                        return false;
                    }
                }

                // Check for existing subscription
                const existingSubscription = await swRegistration.pushManager.getSubscription();

                if (existingSubscription) {
                    console.log("User already has an active Push subscription — no need to create a new one.");
                    return existingSubscription;
                }

                // Create new subscription
                const newSubscription = await swRegistration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey
                });

                console.log("New Push subscription created");

                // Send subscription to backend only if API token exists
                if (!authToken) {
                    console.warn("API token not found. Cannot send subscription to server.");
                    return newSubscription;
                }

                const response = await fetch("{{ route('api.user-push-token.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "Authorization": `Bearer ${authToken}`,
                        "X-CSRF-TOKEN": csrfToken ?? ""
                    },
                    body: JSON.stringify({
                        type: "web_push",
                        token: JSON.stringify(newSubscription)
                    })
                });

                if (response.ok) {
                    console.log("Push subscription stored successfully on server");
                } else {
                    console.error("Failed to store Push subscription:", await response.json());
                }

                return newSubscription;

            } catch (e) {
                console.error("Push subscription error:", e);
                return false;
            }
        }

        /* ──────────────────────────────────────────────
        * 6) Auto-run subscription if permission already granted
        * ────────────────────────────────────────────── */
        if (Notification.permission === "granted") {
            await subscribeUserIfNeeded();
        }

        /* ──────────────────────────────────────────────
        * 7) Manual activation button + warning box
        * ────────────────────────────────────────────── */

        const warnBox = document.getElementById("notification-warning");
        const btn = document.getElementById("request-notification-permission");

        /**
         * Update UI (warning box + button)
         * granted → hide warning + hide button
         * default/denied → show warning + show button
         */
        function updateNotificationUI() {
            if (Notification.permission === "granted") {
                warnBox.style.display = "none";
                btn.style.display = "none";
            } else {
                warnBox.style.display = "block";
                btn.style.display = "inline-block";
            }
        }

        // Initial UI check
        updateNotificationUI();

        if (btn) {
            btn.addEventListener("click", async () => {

                if (tokenMeta) {
                    localStorage.setItem("auth_token", tokenMeta.getAttribute("content"));
                }

                const subscribed = await subscribeUserIfNeeded();

                if (subscribed) {
                    alert("Web Push Notifications successfully enabled ✓");
                }

                // Update UI again after click
                updateNotificationUI();
            });
        }



    });
  </script>
  <script>
    let alarmTimeout = null;
    let currentRoute = null;

    function showTripAlarm(data) {
        const alarm = document.getElementById('trip-alarm');
        const text  = alarm.querySelector('.alarm-text');
        const bar   = alarm.querySelector('.alarm-progress');

        currentRoute = data.route || null;

        text.innerText =
            data.type === 'trip_start_requested'
                ? 'راننده درخواست شروع سفر را ارسال کرده'
                : 'راننده درخواست پایان سفر را ارسال کرده';

        if (navigator.vibrate) {
            navigator.vibrate(60);
        }

        alarm.classList.add('show');

        bar.style.transition = 'none';
        bar.style.width = '0%';

        requestAnimationFrame(() => {
            bar.style.transition = 'width 60s linear';
            bar.style.width = '100%';
        });

        alarmTimeout = setTimeout(hideTripAlarm, 60000);
    }

    function hideTripAlarm() {
        const alarm = document.getElementById('trip-alarm');
        alarm.classList.remove('show');
        clearTimeout(alarmTimeout);
    }

    document.querySelector('.alarm-close')
        .addEventListener('click', hideTripAlarm);

    document.querySelector('.alarm-btn.view')
        .addEventListener('click', () => {
            if (currentRoute) {
                window.location.href = currentRoute;
            }
        });

    function onNotificationReceived(data) {
        if (
            data.type === 'trip_start_requested' ||
            data.type === 'trip_end_requested'
        ) {
            showTripAlarm(data);
        }
    }

    navigator.serviceWorker.addEventListener('message', event => {
        if (event.data && event.data.payload) {
            onNotificationReceived(event.data.payload);
        }
    });

    window.testTripAlarm = function () {
        onNotificationReceived({
            type: 'trip_start_requested',
            route: '/trip/12/start/response?rid=test'
        });
    };

   

    
    </script>

    <script>
        
          
        navigator.serviceWorker.addEventListener("message", function(event) {
            const msg = event.data;
            if (msg.type === "trip_accepted") {
                console.log("🚕 Trip accepted by driver, updating page...");
                window.location.reload();
                return;
            }
        });

    </script>


  

</body>
</html>