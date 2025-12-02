<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Page Title -->
  <title>حساب رانندگان</title>
  <link rel="shortcut icon" href="{{ asset('/img/fav.png') }}" type="image/x-icon">
  <!-- Page Style -->
  <style>
    :root {

        --main-color: {{ setting('colers_primary') }};
        --second-color: {{ setting('colers_secondary') }};
        --Third-color: {{ setting('colers_tertiary') }};
      }
  </style>
  <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(auth()->check())
      @php
          $token = auth()->user()->createToken('web')->plainTextToken;
      @endphp
          <meta name="api-token" content="{{ $token }}">
    @endif
  <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
</head>
<body>

  <!-- User Profile page -->
   <div class="user-profile-container max-width">

    <!-- User info -->
    <div class="u-profile-info">

  <img class="u-profile-info-img" src="{{ optional(auth()->user()->userable)->profile_photo
            ? asset('storage/' . auth()->user()->userable->profile_photo)
            : asset('img/no-photo.png') 
            }}" alt="تصویر کاربری">

      <section>
        <div class="u-profile-username">
          <h2>
              {{ optional(auth()->user()->userable)->first_name ?? '' }}
              {{ optional(auth()->user()->userable)->last_name ?? '' }}
          </h2>

          <form method="POST" action="{{ route('logout') }}" style="display:inline" class="logout-form" id="logoutForm">
                @csrf
                <button type="submit" id="logoutBtn">خروج</button>
          </form>
          <button id="editDriverInfo">ویرایش اطلاعات</button>
        </div>
        <div class="u-profile-type">راننده</div>
      </section>
    
    </div>

    <div id="driverProfilePopup">
      <div class="driver-profile-content">
        <h2>ویرایش اطلاعات</h2>
        <p>جهت ویرایش اطلاعات لطفا با ادمین های سایت تماس بگیرید</p>
        <section>
          <button id="driverProfilePopupClose">بستن</button>
          <a href="#">تماس با پشتیبانی</a>
        </section>
      </div>
    </div>
    <!-- User info end -->

    <div class="u-profile-content driver-profile">
      <!-- Tabs -->
      <div class="u-profile-tabs">
        <div class="active">کل سفر‌ها</div>
        <div>جاری</div>
        <div>تاریخچه</div>
      </div>
      <!-- Tabs end -->

      <!-- Tabs content -->
      <div class="u-profile-tab-content">
        <!-- Tab 1 -->
        <div class="u-profile-tab-item active ud-tab1">
          <div class="passenger-current-trip">
            <ul id="tripsList">
              <!-- Current Trip item -->
              <li>
                <div class="passenger-trip-item">
                  <div class="passenger-item-title"><div class="trip-id">کد سفر: 1245</div> <div class="trip-state">هزینه سفر: 350000 تومان</div> </div>

                  <section>
                    <button id="skipTrip">رد سفر</button>
                    <button id="acceptTrip">قبول سفر</button>
                    <img src="{{ asset('/img/down.svg') }}" alt="فلش">
                  </section>
                </div>
                <div class="passenger-trip-content">

                  <div class="trip-extra-info-md">
                    <div class="trip-date">تاریخ: 08 مرداد 1404</div><span>-</span>
                    <div class="trip-time">ساعت: 22:16</div><span>-</span>
                    <div class="trip-time">تعداد مسافر: 1</div><span>-</span>
                    <div class="trip-time">تعداد چمدان: 2</div><span>-</span>
                    <div class="trip-time">نوع سفر: یکطرفه</div><span>-</span>
                    <div class="trip-time">ساعات انتظار: 0</div><span>-</span>
                    <div class="trip-time">حیوان خانگی: ندارد</div>
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
              <!-- Current Trip item end -->

            </ul>
            <button id="loadMoreTrips">بارگذاری بیشتر</button>
          </div>
        </div>
        <!-- Tab 1 end -->

        <!-- Tab 2 -->
        <div class="u-profile-tab-item ud-tab2">
          <div class="passenger-current-trip">
            <ul>
              <!-- Current Trip item -->
              <li>
                <div class="passenger-trip-item">
                  <div class="passenger-item-title"><div class="trip-id">کد سفر: 1245</div> <div class="trip-state">در حال انجام</div> </div>

                  <section>
                    <button id="tripActionEnd">اتمام سفر</button>
                    <img src="{{ asset('/img/down.svg') }}" alt="فلش">
                  </section>
                </div>
                <div class="passenger-trip-content">

                  <div class="trip-extra-info-md">
                    <div class="trip-date">تاریخ: 08 مرداد 1404</div><span>-</span>
                    <div class="trip-time">ساعت: 22:16</div><span>-</span>
                    <div class="trip-time">تعداد مسافر: 1</div><span>-</span>
                    <div class="trip-time">تعداد چمدان: 2</div><span>-</span>
                    <div class="trip-time">نوع سفر: یکطرفه</div><span>-</span>
                    <div class="trip-time">ساعات انتظار: 0</div><span>-</span>
                    <div class="trip-time">حیوان خانگی: ندارد</div>
                  </div>
                  
                  <!-- Total price -->
                  <div class="trip-total-price">
                    <div class="total-price"><span>هزینه سفر:</span> 240.000 تومان</div>
                    <a href="#">لغو سفر</a>
                  </div>

                  <!-- Driver Info -->
                  <div class="trip-driver-info">
                    <div class="driver-info">
                      <p><span>مسافر: </span>اسم مسافر</p>
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
              <!-- Current Trip item end -->

              
              <!-- Current Trip item -->
              <li>
                <div class="passenger-trip-item">
                  <div class="passenger-item-title"><div class="trip-id">کد سفر: 1245</div> <div class="trip-state">در انتظار رزرو</div> </div>

                  <section>
                    <button id="tripActionWhite">در انتظار رزرو</button>
                    <img src="{{ asset('/img/down.svg') }}" alt="فلش">
                  </section>
                </div>
                <div class="passenger-trip-content">

                  <div class="trip-extra-info-md">
                    <div class="trip-date">تاریخ: 08 مرداد 1404</div><span>-</span>
                    <div class="trip-time">ساعت: 22:16</div><span>-</span>
                    <div class="trip-time">تعداد مسافر: 1</div><span>-</span>
                    <div class="trip-time">تعداد چمدان: 2</div><span>-</span>
                    <div class="trip-time">نوع سفر: یکطرفه</div><span>-</span>
                    <div class="trip-time">ساعات انتظار: 0</div><span>-</span>
                    <div class="trip-time">حیوان خانگی: ندارد</div>
                  </div>
                  
                  <!-- Total price -->
                  <div class="trip-total-price">
                    <div class="total-price"><span>هزینه سفر:</span> 240.000 تومان</div>
                    <a href="#">لغو سفر</a>
                  </div>

                  <!-- Driver Info -->
                  <div class="trip-driver-info no-info">
                    <p>لطفا تا زمانی که مسافر هزینه رزرو را پرداخت کند منتظر بمانید</p>
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
              <!-- Current Trip item end -->
            </ul>
          </div>
        </div>
        <!-- Tab 2 end -->

        <!-- Tab 3 -->
        <div class="u-profile-tab-item ud-tab3">
          <div class="passenger-current-trip">
            <ul>
              <!-- Current Trip item -->
              <li>
                <div class="passenger-trip-item">
                  <div class="passenger-item-title"><div class="trip-id">کد سفر: 1245</div> <div class="trip-state">لغو راننده</div> </div>
                  <img src="{{ asset('/img/down.svg') }}" alt="فلش">
                </div>
                <div class="passenger-trip-content">
                  
                  <div class="trip-extra-info-md">
                    <div class="trip-date">تاریخ: 08 مرداد 1404</div><span>-</span>
                    <div class="trip-time">ساعت: 22:16</div><span>-</span>
                    <div class="trip-time">تعداد مسافر: 1</div><span>-</span>
                    <div class="trip-time">تعداد چمدان: 2</div><span>-</span>
                    <div class="trip-time">نوع سفر: یکطرفه</div><span>-</span>
                    <div class="trip-time">ساعات انتظار: 0</div><span>-</span>
                    <div class="trip-time">حیوان خانگی: ندارد</div>
                  </div>
                  
                  <!-- Total price -->
                  <div class="trip-total-price">
                    <div class="total-price"><span>هزینه سفر:</span> 240.000 تومان</div>
                    <a href="#">گزارش مشکل</a>
                  </div>

                  <!-- Driver Info -->
                  <div class="trip-driver-info">
                    <div class="driver-info">
                      <p><span>مسافر: </span>اسم مسافر</p>
                    </div>
                    <a href="tel:09123456789" class="call-to-driver">0912****789</a>
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
              <!-- Current Trip item end -->

            </ul>
          </div>
        </div>
        <!-- Tab 3 end -->

      </div>
      <!-- Tabs content end -->
    </div>

   </div>
  <!-- User Profile page end -->
  <button id="request-notification-permission">دریافت اجازه نوتیفیکیشن</button>
  
  <script src="{{ asset('/js/profile.js') }}"></script>

  <script>
document.addEventListener("DOMContentLoaded", async () => {
    // چک کردن پشتیبانی مرورگر
    if (!("serviceWorker" in navigator) || !("PushManager" in window)) {
        console.log("Web Push در این مرورگر پشتیبانی نمی‌شود.");
        return;
    }

    // گرفتن توکن CSRF و API Token از متا تگ‌ها
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const tokenMeta = document.querySelector('meta[name="api-token"]');

    // ذخیره API Token یک‌بار برای همیشه
    if (tokenMeta && !localStorage.getItem("auth_token")) {
        localStorage.setItem("auth_token", tokenMeta.getAttribute('content'));
    }

    // ثبت Service Worker
    let swRegistration;
    try {
        swRegistration = await navigator.serviceWorker.register("/sw.js", { scope: "/" });
        console.log("Service Worker با موفقیت ثبت شد");
    } catch (err) {
        console.error("خطا در ثبت Service Worker:", err);
        return;
    }

    // تبدیل base64url به Uint8Array
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

    // تابع اصلی ثبت Push Subscription
    async function subscribeUser() {
        try {
            // درخواست اجازه نوتیفیکیشن
            const permission = await Notification.requestPermission();
            if (permission !== "granted") {
                console.log("کاربر اجازه نوتیفیکیشن نداد");
                return false;
            }

            // VAPID Public Key — از .env یا متا تگ بگیر (بهتر از ثابت نوشتن)
            const vapidPublicKey = "{{ env('VAPID_PUBLIC_KEY') ?? 'BKVeFmlrdaKcwXVNSbLtUWqm3vUgFDr4DQVBj104D9MUkwA3itSrbjr7wV3ldP1cMhmCnx8TiOhXrMS3RO0cbZs' }}";
            const applicationServerKey = urlBase64ToUint8Array(vapidPublicKey.trim());

            // حذف اشتراک قدیمی (اگر وجود داشت)
            const existingSub = await swRegistration.pushManager.getSubscription();
            if (existingSub) {
                console.log("اشتراک قدیمی پیدا شد → در حال حذف...");
                await existingSub.unsubscribe();
            }

            // ایجاد اشتراک جدید
            const subscription = await swRegistration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey
            });

            console.log("اشتراک جدید با موفقیت ایجاد شد");

            // ارسال به سرور — دقیقاً همون ساختاری که کنترلرت انتظار داره
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
                    token: JSON.stringify(subscription) // فقط این مهمه!
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

    // اجرای خودکار در لود صفحه (فقط اگر اجازه نداده باشه)
    if (Notification.permission === "default") {
        await subscribeUser();
    } else if (Notification.permission === "granted") {
        // حتی اگر قبلاً اجازه داده باشه، یه بار دیگه چک کنیم (برای مواقعی که کلید عوض شده)
        await subscribeUser();
    }

    // دکمه دستی برای فعال‌سازی دوباره (مثلاً بعد از لاگین دوباره)
    const button = document.getElementById("request-notification-permission");
    if (button) {
        button.addEventListener("click", async () => {
            // به‌روزرسانی توکن لاگین
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

  <script>

let currentPage = 1;
const tripsList = document.getElementById('tripsList');
const loadMoreBtn = document.getElementById('loadMoreTrips');

/* -------------------------------------
   تبدیل نوع سفر به فارسی
-------------------------------------- */
function translateTripType(type) {
    if (!type) return '';
    return type === 'oneway' ? 'یکطرفه' :
           type === 'round'  ? 'رفت و برگشت' :
           type;
}

/* -------------------------------------
   مسیر یابی (موبایل → اپ‌ها / دسکتاپ → گوگل)
-------------------------------------- */
function openMap(lat, lng) {
    const url = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;

    if (/Android|iPhone|iPad/i.test(navigator.userAgent)) {
        // موبایل → نمایش برنامه‌های مسیر‌یاب
        window.location.href = `geo:${lat},${lng}?q=${lat},${lng}`;
    } else {
        // دسکتاپ → گوگل‌مپ
        window.open(url, "_blank");
    }
}

/* -------------------------------------
   فعال‌سازی toggle باز و بسته شدن هر سفر
-------------------------------------- */
function attachTripClickEvents() {
    document.querySelectorAll('.passenger-trip-item section img').forEach(btn => {
        btn.onclick = function () {
            const parent = this.closest("li");
            parent.classList.toggle("open");
        };
    });
}

/* -------------------------------------
   رندر یک سفر
-------------------------------------- */
function renderTrip(trip) {
    const origins = trip.origins ? JSON.parse(trip.origins) : [];
    const destinations = trip.destinations ? JSON.parse(trip.destinations) : [];

    const originsHtml = origins.map((o, i) => `
        <li>
            <span>مبدا ${i+1}: </span>${o.address || 'آدرس موجود نیست'}
            <button onclick="openMap(${o.lat}, ${o.lng})">مسیر یاب</button>
        </li>
    `).join('');

    const destinationsHtml = destinations.map((d, i) => `
        <li>
            <span>مقصد ${i+1}: </span>${d.address || 'آدرس موجود نیست'}
            <button onclick="openMap(${d.lat}, ${d.lng})">مسیر یاب</button>
        </li>
    `).join('');

    const date = trip.formatted_date ?? trip.start_date;
    const time = trip.formatted_time ?? trip.trip_time;

    return `
    <li>
        <div class="passenger-trip-item">
            <div class="passenger-item-title">
                <div class="trip-id">کد سفر: ${trip.id}</div>
                <div class="trip-state">هزینه سفر: ${Number(trip.cost).toLocaleString()} تومان</div>
            </div>
            <section>
                <button id="skipTrip">رد سفر</button>
                <button id="acceptTrip">قبول سفر</button>
                <img src="/img/down.svg" alt="فلش">
            </section>
        </div>

        <div class="passenger-trip-content">

            <div class="trip-extra-info-md">
                <div>تاریخ: ${date}</div>
                <span>-</span>
                <div>ساعت: ${time}</div>
                <span>-</span>
                <div>تعداد مسافر: ${trip.passenger_count}</div>
                <span>-</span>
                <div>تعداد چمدان: ${trip.luggage_count}</div>
                <span>-</span>
                <div>نوع سفر: ${translateTripType(trip.trip_type)}</div>
                <span>-</span>
                <div>ساعات انتظار: ${trip.waiting_hours}</div>
                <span>-</span>
                <div>حیوان خانگی: ${trip.has_pet ? 'دارد' : 'ندارد'}</div>
            </div>

            <ul class="trip-locations">
                ${originsHtml + destinationsHtml}
            </ul>

            ${trip.driver ? `
            <div class="trip-driver-info">
                <img src="${trip.driver.userable?.profile_photo ? '/storage/' + trip.driver.userable.profile_photo : '/img/no-photo.png'}">
                <div class="driver-info">
                    <p><span>راننده: </span>${trip.driver.userable?.first_name ?? ''} ${trip.driver.userable?.last_name ?? ''}</p>
                    <p><span>ماشین: </span>${trip.driver.userable?.car?.name ?? 'نامشخص'}</p>
                    <p><span>پلاک: </span>${trip.driver.userable?.car_plate ?? 'نامشخص'}</p>
                </div>
                <a href="tel:${trip.driver?.phone ?? ''}" class="call-to-driver">${trip.driver?.phone ?? ''}</a>
            </div>` : ''}

            <div class="user-form-desc">
                <p>${trip.caption ?? ''}</p>
            </div>
        </div>
    </li>`;
}

/* -------------------------------------
   بارگذاری لیست سفرها
-------------------------------------- */
function loadTrips(page = 1) {
    fetch(`/driver/trips?page=${page}`)
        .then(res => res.json())
        .then(data => {
            if (!data.status) return;

            const trips = [
                ...data.tripsWithoutDriver.data
            ];

            trips.forEach(trip => {
                tripsList.insertAdjacentHTML("beforeend", renderTrip(trip));
            });

            if (page >= data.tripsWithoutDriver.last_page) {
                loadMoreBtn.style.display = "none";
            }

            attachTripClickEvents();
        });
}

/* -------------------------------------
   بار اول
-------------------------------------- */
loadTrips(currentPage);

loadMoreBtn.addEventListener("click", () => {
    currentPage++;
    loadTrips(currentPage);
});

/* -------------------------------------
   دریافت پیام FCM (همیشه فعال)
-------------------------------------- */
if (typeof messaging !== "undefined") {
    messaging.onMessage(payload => {

        console.log("📥 پیام جدید FCM:", payload);

        let data = payload.data;

        // اگر JSON رشته‌ای بود → تبدیل کن
        if (typeof data === "string") {
            try { data = JSON.parse(data); } catch { return; }
        }

        // فیلتر کردن ONLY notifications with type="trip"
        if (!data || data.type !== "trip") {
            console.warn("⛔ پیام تایپ trip نبود، نادیده گرفته شد.");
            return;
        }

        let trip = data.trip;

        // اگر JSON رشته‌ای بود → تبدیل کن
        if (typeof trip === "string") {
            try { trip = JSON.parse(trip); } catch { return; }
        }

        if (!trip) return;

        // ⬅ درج سفر جدید در بالاترین لایه لیست
        tripsList.insertAdjacentHTML("afterbegin", renderTrip(trip));

        // فعال‌سازی آیکن باز/بسته شدن سفر
        attachTripClickEvents();
    });
}
if (navigator.serviceWorker) {
    navigator.serviceWorker.addEventListener("message", function(event) {
        const data = event.data;

        if (!data || data.type !== "trip") return;

        let trip = data.trip;

        // در صورتی که به صورت رشته ارسال شده باشد
        if (typeof trip === "string") {
            try { trip = JSON.parse(trip); } catch {}
        }

        // درج سفر جدید در بالاترین قسمت
        tripsList.insertAdjacentHTML("afterbegin", renderTrip(trip));
        attachTripClickEvents();
    });
}

</script>


</body>
</html>