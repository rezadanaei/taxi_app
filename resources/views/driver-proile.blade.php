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

                    <form method="POST" action="{{ route('logout') }}" style="display:inline" class="logout-form"
                        id="logoutForm">
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
                    <a href="tel:{{ setting('support_phone') }}">تماس با پشتیبانی</a>
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
            @if (session('error'))
            <div class="admin-errors">
                {{ session('error') }}
            </div>
            @endif

            @if (session('success'))
            <div class="admin-success">
                {{ session('success') }}
            </div>
            @endif
            <!-- Tabs content -->
            <div class="u-profile-tab-content">
                <!-- Tab 1 -->
                <div class="u-profile-tab-item active ud-tab1">
                    <div id="notification-warning" style="display: none;">
                        <span>مرورگر شما اجازه ارسال نوتیفیکیشن نمی‌دهد.</span>
                        <span>
                            برای دریافت نوتیفیکیشن‌ها <button id="request-notification-permission">اینجا</button> کلیک
                            کنید.
                        </span>
                    </div>

                    <div class="passenger-current-trip">
                        <ul id="tripsList">
                            <!-- Current Trip item -->

                            <!-- Current Trip item end -->

                        </ul>
                        <div id="infiniteScrollTrigger"></div>

                    </div>
                </div>
                <!-- Tab 1 end -->

                <!-- Tab 2 -->
                <div class="u-profile-tab-item ud-tab2">
                    <div class="passenger-current-trip">
                        <ul id="driverTripsList">
                            <!-- Current Trip item -->
                            <li>
                                <div class="passenger-trip-item">
                                    <div class="passenger-item-title">
                                        <div class="trip-id">کد سفر: 1245</div>
                                        <div class="trip-state">در حال انجام</div>
                                    </div>

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
                                        <p>توضیحاتی که کاربر در قسمت ثبت رزرو فرم انجام داده در این قسمت نمایش داده
                                            میشود.</p>
                                    </div>
                                </div>
                            </li>
                            <!-- Current Trip item end -->


                            <!-- Current Trip item -->
                            <li>
                                <div class="passenger-trip-item">
                                    <div class="passenger-item-title">
                                        <div class="trip-id">کد سفر: 1245</div>
                                        <div class="trip-state">در انتظار رزرو</div>
                                    </div>

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
                                        <p>توضیحاتی که کاربر در قسمت ثبت رزرو فرم انجام داده در این قسمت نمایش داده
                                            میشود.</p>
                                    </div>
                                </div>
                            </li>
                            <!-- Current Trip item end -->
                        </ul>
                        <div id="driverInfiniteScrollTrigger"></div>
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
                                    <div class="passenger-item-title">
                                        <div class="trip-id">کد سفر: 1245</div>
                                        <div class="trip-state">لغو راننده</div>
                                    </div>
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
                                        <a href="tel:{{ setting('support_phone') }}">گزارش مشکل</a>
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
                                        <p>توضیحاتی که کاربر در قسمت ثبت رزرو فرم انجام داده در این قسمت نمایش داده
                                            میشود.</p>
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

    <script src="{{ asset('/js/profile.js') }}"></script>

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
            swRegistration = await navigator.serviceWorker.register("/sw.js", {
                scope: "/"
            });
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
                    console.log(
                        "User already has an active Push subscription — no need to create a new one."
                    );
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
    window.routes = {
        cancelTrip: "{{ route('trips.cancel') }}"
    };
    </script>



    <script>
    let currentPage = 1;
    let isLoading = false;
    let lastPage = false;

    const tripsList = document.getElementById("tripsList");
    const trigger = document.getElementById("infiniteScrollTrigger");

    /* ======= Helper Functions ======= */

    function translateTripType(type) {
        if (!type) return '';
        return type === 'oneway' ? 'یکطرفه' :
            type === 'round' ? 'رفت و برگشت' :
            type;
    }

    function safeJSON(val) {
        try {
            return JSON.parse(val);
        } catch {
            return [];
        }
    }

    function openMap(lat, lng) {
        const url = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
        if (/Android|iPhone|iPad/i.test(navigator.userAgent)) {
            window.location.href = `geo:${lat},${lng}?q=${lat},${lng}`;
        } else {
            window.open(url, "_blank");
        }
    }

    /* ======= Render Trip HTML ======= */
    function renderTrip(trip) {
        if (!trip || !trip.id) return '';

        if (tripIds.has(trip.id)) return '';
        tripIds.add(trip.id);
        const origins = trip.origins ? safeJSON(trip.origins) : [];
        const destinations = trip.destinations ? safeJSON(trip.destinations) : [];

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
                    <button class="acceptTrip" id="acceptTrip" data-trip-id="${trip.id}">قبول سفر</button>
                    <img src="/img/down.svg" class="toggle-trip" alt="فلش">
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

    /* ======= Load Trips from API ======= */
    function loadTrips(page = 1) {
        if (isLoading || lastPage) return;
        isLoading = true;

        fetch(`/driver/trips?page=${page}`)
            .then(res => res.json())
            .then(data => {
                isLoading = false;

                if (!data.status) return;

                const trips = data.tripsWithoutDriver.data;

                if (trips.length === 0) {
                    lastPage = true;
                    return;
                }

                trips.forEach(trip => {
                    const html = renderTrip(trip);
                    if (html) {
                        tripsList.insertAdjacentHTML("beforeend", html);
                    }
                });


                if (page >= data.tripsWithoutDriver.last_page) {
                    lastPage = true;
                }
            });
    }

    /* ======= Event Delegation for Toggle ======= */
    document.addEventListener("click", function(e) {
        if (e.target.matches(".toggle-trip")) {
            const li = e.target.closest("li");
            li.classList.toggle("open");
        }
    });

    /* ======= Intersection Observer for Infinite Scroll ======= */
    const observer = new IntersectionObserver((entries) => {
        const entry = entries[0];

        if (entry.isIntersecting && !isLoading && !lastPage) {
            currentPage++;
            loadTrips(currentPage);
        }
    }, {
        root: null,
        rootMargin: "200px",
        threshold: 0
    });

    observer.observe(trigger);

    /* ======= Initial Load ======= */
    loadTrips(currentPage);

    /* ======= Optional: FCM & Service Worker ======= */
    if (typeof messaging !== "undefined") {
        messaging.onMessage(payload => {
            let data = payload.data;

            if (typeof data === "string") {
                try { data = JSON.parse(data); } catch { return; }
            }

            if (!data || data.type !== "trip") return;

            let trip = data.trip;
            if (typeof trip === "string") {
                try { trip = JSON.parse(trip); } catch { return; }
            }

            if (!trip || tripIds.has(trip.id)) return;

            const html = renderTrip(trip);
            if (html) {
                tripsList.insertAdjacentHTML("afterbegin", html);
            }
        });
    }

    if ("serviceWorker" in navigator) {

        navigator.serviceWorker.addEventListener("message", function(event) {

            const msg = event.data;

            if (!msg) {
                console.log("⚠️ Message is empty");
                return;
            }
            if (msg.type === "peyment_trip") {
                console.log("🔄 Payment trip received, reloading page...");
                window.location.reload();
                return;
            }
            if (msg.type === "trip") {

                let trip = msg.payload;
                if (typeof trip === "string") {
                    try {
                        trip = JSON.parse(trip);
                    } catch (e) {
                        return;
                    }
                }

                if (!trip) {
                    return;
                }

                if (tripIds.has(trip.id)) {
                    return;
                }

                const html = renderTrip(trip);

                if (html) {
                    tripsList.insertAdjacentHTML("afterbegin", html);
                } else {
                    console.warn("⚠️ renderTrip returned empty HTML");
                }

            } else {
                console.log("⚠️ Message type is not 'trip'", msg.type);
            }
        });
    }

   




    </script>
    <script>
    /* =======================
    Variables
    ======================= */
    let currentDriverPage = 1;
    let isDriverLoading = false;
    let driverLastPage = false;
    const MAX_DISTANCE = 15000;

    /* =======================
    Trip Deduplication Cache
    ======================= */
    const tripIds = new Set();


    const driverTripsList = document.getElementById("driverTripsList");
    const driverTrigger = document.getElementById("driverInfiniteScrollTrigger");

    /* =======================
    Helpers
    ======================= */
    function translateTripType(type) {
        if (!type) return '';
        return type === 'oneway' ? 'یکطرفه' :
            type === 'round' ? 'رفت و برگشت' : type;
    }

    function translateTripStatus(status) {
        switch (status) {
            case 'pending': return 'در انتظار';
            case 'ongoing': return 'در حال انجام';
            case 'completed': return 'تکمیل شده';
            case 'cancelled': return 'لغو شده';
            case 'rejected': return 'رد شده';
            case 'no-show': return 'عدم حضور مسافر';
            case 'paid': return 'پرداخت شده';
            case 'pending-payment': return 'در انتظار پرداخت';
            default: return status;
        }
    }

    function safeJSON(val) {
        try { return JSON.parse(val); } catch { return []; }
    }

    function openMap(lat, lng) {
        const url = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
        if (/Android|iPhone|iPad/i.test(navigator.userAgent)) {
            window.location.href = `geo:${lat},${lng}?q=${lat},${lng}`;
        } else {
            window.open(url, "_blank");
        }
    }

    /* =======================
    GPS & Distance
    ======================= */
    function getCurrentLocation() {
        // return new Promise((resolve, reject) => {
        //     if (!navigator.geolocation) return reject("GPS پشتیبانی نمی‌شود");

        //     navigator.geolocation.getCurrentPosition(
        //         pos => {
        //             if (pos.coords.accuracy > 50) {
        //                 return reject("دقت جی‌پی‌اس پایین است، لطفا در محیط باز تلاش کنید");
        //             }
        //             resolve({
        //                 lat: pos.coords.latitude,
        //                 lng: pos.coords.longitude
        //             });
        //         },
        //         err => reject("دسترسی به موقعیت مکانی رد شد"),
        //         { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        //     );
        // });
          return Promise.resolve({
            lat:  34.07573193084771, // جایگزین با latitude مبدا
            lng: 47.97646668900475 // جایگزین با longitude مبدا
         
        });
    }

    function getDistanceMeters(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;

        const a = Math.sin(dLat / 2) ** 2 +
                Math.cos(lat1 * Math.PI / 180) *
                Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) ** 2;
        return 2 * R * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    /* =======================
    Render Trip
    ======================= */
    function renderDriverTrip(trip) {
        const origins = trip.origins ? safeJSON(trip.origins) : [];
        const destinations = trip.destinations ? safeJSON(trip.destinations) : [];
        const date = trip.formatted_date ?? trip.start_date;
        const time = trip.formatted_time ?? trip.trip_time;

        const originsHtml = origins.map((o, i) => `
            <li>
                <span>مبدا ${i+1}: </span>${o.address ?? ''}
                <button onclick="openMap(${o.lat}, ${o.lng})">مسیر یاب</button>
            </li>
        `).join('');

        const destinationsHtml = destinations.map((d, i) => `
            <li>
                <span>مقصد ${i+1}: </span>${d.address ?? ''}
                <button onclick="openMap(${d.lat}, ${d.lng})">مسیر یاب</button>
            </li>
        `).join('');

        let buttonHtml = '';

        if (trip.status === 'paid' && origins.length) {
            buttonHtml = `
                <button class="trip-arrived"
                    data-trip-id="${trip.id}"
                    data-lat="${origins[0].lat}"
                    data-lng="${origins[0].lng}">
                    رسیدم
                </button>
                <button class="trip-start"
                    data-trip-id="${trip.id}"
                    data-lat="${origins[0].lat}"
                    data-lng="${origins[0].lng}">
                    شروع سفر
                </button>
            `;
        }

        if (trip.status === 'ongoing' && destinations.length) {
            const last = destinations[destinations.length - 1];
            buttonHtml = `
                <button class="trip-end"
                    data-trip-id="${trip.id}"
                    data-lat="${last.lat}"
                    data-lng="${last.lng}">
                    اتمام سفر
                </button>
            `;
        }

        let passengerHtml = '';
        if (trip.status === 'pending-payment' || !trip.passenger) {
            passengerHtml = `
                <div class="trip-driver-info no-info">
                    <p>لطفا تا زمانی که مسافر هزینه رزرو را پرداخت کند منتظر بمانید</p>
                </div>
            `;
        } else {
            const phone = trip.passenger?.phone ?? '';
            passengerHtml = `
                <div class="trip-driver-info">
                    <div class="driver-info">
                        <p><span>مسافر: </span>${trip.passenger.userable?.name ?? '—'}</p>
                    </div>
                    <a href="tel:${phone}" class="call-to-driver">${phone}</a>
                </div>
            `;
        }

        return `
            <li>
                <div class="passenger-trip-item">
                    <div class="passenger-item-title">
                        <div class="trip-id">کد سفر: ${trip.id}</div>
                        <div class="trip-state">${translateTripStatus(trip.status)}</div>
                    </div>
                    <section>
                        ${buttonHtml}
                        <img src="/img/down.svg" class="toggle-trip">
                    </section>
                </div>
                <div class="passenger-trip-content">
                    <div class="trip-extra-info-md">
                        <div>تاریخ: ${date}</div><span>-</span>
                        <div>ساعت: ${time}</div><span>-</span>
                        <div>مسافر: ${trip.passenger_count}</div><span>-</span>
                        <div>چمدان: ${trip.luggage_count}</div><span>-</span>
                        <div>نوع سفر: ${translateTripType(trip.trip_type)}</div>
                    </div>
                    ${passengerHtml}
                    <ul class="trip-locations">
                        ${originsHtml + destinationsHtml}
                    </ul>
                </div>
            </li>
        `;
    }

    /* =======================
    Load Trips
    ======================= */
    function loadDriverTrips(page = 1) {
        if (isDriverLoading || driverLastPage) return;
        isDriverLoading = true;

        fetch(`/driver/trips?page=${page}&type=driverTrips`)
            .then(res => res.json())
            .then(data => {
                isDriverLoading = false;
                if (!data.status) return;

                const trips = data.driverTrips.data;
                if (!trips.length) {
                    driverLastPage = true;
                    return;
                }

                trips.forEach(trip => {
                    driverTripsList.insertAdjacentHTML("beforeend", renderDriverTrip(trip));
                });

                if (page >= data.driverTrips.last_page) driverLastPage = true;
            });
    }

    /* =======================
    Toggle Details
    ======================= */
    document.addEventListener("click", e => {
        if (e.target.matches(".toggle-trip")) {
            e.target.closest("li").classList.toggle("open");
        }
    });

    /* =======================
    Trip Actions
    ======================= */
    document.addEventListener("click", async e => {
        if (!e.target.matches(".trip-arrived, .trip-start, .trip-end")) return;

        const btn = e.target;
        const tripId = btn.dataset.tripId;
        const targetLat = +btn.dataset.lat;
        const targetLng = +btn.dataset.lng;

        try {
            btn.disabled = true;
            btn.textContent = "در حال دریافت موقعیت مکانی...";

            const driver = await getCurrentLocation();
            const distance = getDistanceMeters(driver.lat, driver.lng, targetLat, targetLng);

            if (distance > MAX_DISTANCE) {
                const msg = btn.classList.contains('trip-end') ?
                    `باید حداکثر ${MAX_DISTANCE} متر با مقصد آخر فاصله داشته باشید` :
                    `باید حداکثر ${MAX_DISTANCE} متر با مبدا اول فاصله داشته باشید`;
                alert(msg);
                btn.disabled = false;
                btn.textContent = btn.classList.contains("trip-arrived") ? "رسیدم" :
                                    btn.classList.contains("trip-start") ? "شروع سفر" : "اتمام سفر";
                return;
            }

            const action = btn.classList.contains("trip-arrived") ? 'arrived' :
                        btn.classList.contains("trip-start") ? 'start' : 'end';

            const response = await fetch(`/driver/trips/${tripId}/${action}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    lat: driver.lat,
                    lng: driver.lng,
                    distance: Math.round(distance)
                })
            });

            const res = await response.json();

            if (!res.status) {
                alert(res.message || "خطا در انجام عملیات");
                btn.disabled = false;
                btn.textContent = btn.classList.contains("trip-arrived") ? "رسیدم" :
                                    btn.classList.contains("trip-start") ? "شروع سفر" : "اتمام سفر";
                return;
            }

            location.reload();

        } catch (err) {
            alert(err);
            btn.disabled = false;
            btn.textContent = btn.classList.contains("trip-arrived") ? "رسیدم" :
                                btn.classList.contains("trip-start") ? "شروع سفر" : "اتمام سفر";
        }
    });

    /* =======================
    Infinite Scroll
    ======================= */
    const driverObserver = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting && !isDriverLoading && !driverLastPage) {
            currentDriverPage++;
            loadDriverTrips(currentDriverPage);
        }
    }, { rootMargin: "200px" });

    driverObserver.observe(driverTrigger);

    /* =======================
    Init
    ======================= */
    loadDriverTrips(currentDriverPage);
    </script>




    
    <script>
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.acceptTrip');
        if (!btn) return; 

        e.stopPropagation(); 

        const tripId = btn.dataset.tripId;

        fetch('/driver/trips/accept?trip_id=' + tripId, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },

            })
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        alert(data.message);
                        location.reload();

                    } else {
                        alert('خطا: ' + data.message);
                    }
                } catch (err) {
                    console.error('Invalid JSON:', text);
                    alert('پاسخ سرور معتبر نیست: ' + text);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('یک خطای ناخواسته رخ داده است.');
            });

    });
    </script>

</body>

</html>