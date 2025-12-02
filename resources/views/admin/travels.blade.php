<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>سفر ها</title>
    <style>
        :root {
            --main-color: {{ setting('colers_primary') ?? '#1E90FF' }};
            --second-color: {{ setting('colers_secondary') ?? '#FF4081' }};
            --Third-color: {{ setting('colers_tertiary') ?? '#E0E0E0' }};
        }


    </style>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

  <div class="admin-travels-container">
    
   <div id="AddDriverTripPopup">
      <div class="add-driver-trip-content">
        <h2>تخصیص راننده</h2>
        <p>یک راننده را برای این سفر انتخاب کنید</p>
        <input type="search" id="driverSearchInput" placeholder="جستجوی راننده...">
        <input type="hidden" id="selectedTripId" name="selectedTripId" value="">
        <ul id="driverSearchResults"></ul>
      </div> 
    </div>


    <h1>سفر ها</h1>

    <div class="status-filter-buttons">
      <button class="status-btn active" data-status="همه سفرها">همه سفرها</button>
      <button class="status-btn" data-status="در انتظار">در انتظار</button>
      <button class="status-btn" data-status="در حال انجام">در حال انجام</button>
      <button class="status-btn" data-status="تکمیل شده">تکمیل شده</button>
      <button class="status-btn" data-status="لغو شده">لغو شده</button>
    </div>

    <div class="sort-search-container">
      <select class="sort-dropdown">
        <option value="date-desc">مرتب‌سازی: جدیدترین</option>
        <option value="date-asc">مرتب‌سازی: قدیمی‌ترین</option>
        <option value="id-desc">مرتب‌سازی: کد سفر (نزولی)</option>
        <option value="id-asc">مرتب‌سازی: کد سفر (صعودی)</option>
        <option value="price-desc">مرتب‌سازی: قیمت (نزولی)</option>
        <option value="price-asc">مرتب‌سازی: قیمت (صعودی)</option>
      </select>
      <input type="text" class="search-box" placeholder="کد سفر">
    </div>

    <div class="admin-travels-content">
      <div class="passenger-current-trip">
        <ul>
          <li class="no-results" style="display: none;">موردی یافت نشد</li>
          @foreach ($trips as $trip)
            @php
                // تبدیل تاریخ به شمسی با تابع اختصاصی پروژه
                $tripDT = tripDate($trip->start_date);

                // دیکد کردن مبدا و مقصدها
                $origins = json_decode($trip->origins, true);
                $destinations = json_decode($trip->destinations, true);

                // تبدیل وضعیت
                $statusLabels = [
                    'pending' => 'در انتظار',
                    'pending-payment' => 'در انتظار',
                    'ongoing' => 'در حال انجام',
                    'paid' => 'درحال انجام',
                    'completed' => 'تکمیل شده',
                    'cancelled' => 'لغو شده',
                    'rejected' => 'لغو شده',
                    'no-show' => 'لغو شده',
                    'refunded' => 'لغو شده',
                ];
                $statusText = $statusLabels[$trip->status] ?? $trip->status;

                // آیا حیوان خانگی دارد؟
                $petStatus = $trip->has_pet ? 'دارد' : 'ندارد';
            @endphp

            <li>
                <div class="passenger-trip-item">
                    <div class="passenger-item-title">
                        <div class="trip-id">کد سفر: {{ $trip->id }}</div>
                        <div class="trip-state">{{ $statusText }}</div>
                    </div>

                    <section>
                        <img src="{{ asset('img/down.svg') }}" alt="فلش">
                    </section>
                </div>

                <div class="passenger-trip-content">

                    {{-- دکمه‌های مدیریت سفر --}}
                    <div class="admin-trip-actions-btn">
                        <form method="POST" action="{{ route('admin.trips.cancel') }}" style="display: inline-block">
                          @csrf
                          <input type="hidden" name="trip_id" value="{{ $trip->id }}">
                          <button class="ad-cancel-trip">لغو سفر</button>
                        </form>
                        @if (!$trip->driver)
                          <button class="ad-driver-trip" id="AddDriverTrip" data-tripId="{{ $trip->id }}">تخصیص راننده</button>
                        @endif
                    </div>

                    {{-- اطلاعات پایه سفر --}}
                    <div class="trip-extra-info-ad">
                        <div class="trip-date">تاریخ: {{ $tripDT['date'] }}</div><span>-</span>
                        <div class="trip-time">ساعت: {{ $tripDT['time'] }}</div><span>-</span>
                        <div class="trip-time">تعداد مسافر: {{ $trip->passenger_count }}</div><span>-</span>
                        <div class="trip-time">تعداد چمدان: {{ $trip->luggage_count }}</div><span>-</span>
                        <div class="trip-time">نوع سفر: {{ $trip->trip_type === 'oneway' ? 'یکطرفه' : 'رفت و برگشت' }}</div><span>-</span>
                        <div class="trip-time">ساعات انتظار: {{ $trip->waiting_hours }}</div><span>-</span>
                        <div class="trip-time">حیوان خانگی: {{ $petStatus }}</div>
                    </div>

                    {{-- قیمت و پرداخت --}}
                    <div class="trip-extra-info-ad2">
                        @if(isset($trip->payment_id))
                            <div class="trip-date">شناسه پرداخت: {{ $trip->payment_id }}</div><span>-</span>
                        @endif
                        <div class="trip-time">هزینه سفر: {{ number_format($trip->cost) }} تومان</div><span>-</span>
                        <div class="trip-time">وضعیت: {{ $statusText }}</div>
                    </div>

                    {{-- اطلاعات راننده --}}
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
                    </div>
                    @endif

                    {{-- اطلاعات مسافر --}}
                    @if ($trip->passenger)
                    <div class="trip-passenger-info">
                        <div class="driver-info">
                            <p>
                                <span>مسافر: </span>
                                @if (empty(optional($trip->passenger->userable)->first_name) && empty(optional($trip->passenger->userable)->last_name))
                                  نامشخص
                                @else
                                  {{ optional($trip->passenger->userable)->first_name ?? '' }}
                                  {{ optional($trip->passenger->userable)->last_name ?? '' }}
                                @endif

                            </p>
                        </div>
                        <a href="tel:{{ optional($trip->passenger)->phone ?? '' }}" class="call-to-driver">
                            {{ optional($trip->passenger)->phone ?? '' }}
                        </a>
                    </div>
                    @endif

                    {{-- نمایش JSON مبدا و مقصد --}}
                    <section class="trip-locations-info">
                        @foreach ($origins as $index => $origin)
                            <div class="trip-location-item">
                                <span>مبدا {{ $index + 1 }}: </span> {{ $origin['address'] ?? 'آدرس موجود نیست' }}
                            </div>
                        @endforeach

                        @foreach ($destinations as $index => $destination)
                            <div class="trip-location-item">
                                <span>مقصد {{ $index + 1 }}: </span> {{ $destination['address'] ?? 'آدرس موجود نیست' }}
                            </div>
                        @endforeach
                    </section>

                    {{-- توضیح کاربر --}}
                    <div class="user-form-desc">
                        <p>{{ $trip->caption ?? 'توضیحاتی ثبت نشده.' }}</p>
                    </div>

                </div>
            </li>

            @endforeach
            {{ $trips->links('pagination::bootstrap-5') }}


          <li>
            <div class="passenger-trip-item">
              <div class="passenger-item-title"><div class="trip-id">کد سفر: 1245</div> <div class="trip-state">در انتظار</div> </div>

              <section>
                <img src="{{ asset('img/down.svg') }}" alt="فلش">
              </section>
            </div>
            <div class="passenger-trip-content">

              <div class="admin-trip-actions-btn">
                <button class="ad-cancel-trip">لغو سفر</button>
                <button class="ad-driver-trip" id="AddDriverTrip">تخصیص راننده</button>
              </div>

              <div class="trip-extra-info-ad">
                <div class="trip-date">تاریخ: 08 مرداد 1401</div><span>-</span>
                <div class="trip-time">ساعت: 22:16</div><span>-</span>
                <div class="trip-time">تعداد مسافر: 1</div><span>-</span>
                <div class="trip-time">تعداد چمدان: 2</div><span>-</span>
                <div class="trip-time">نوع سفر: یکطرفه</div><span>-</span>
                <div class="trip-time">ساعات انتظار: 0</div><span>-</span>
                <div class="trip-time">حیوان خانگی: ندارد</div>
              </div>
              
              <div class="trip-extra-info-ad2">
                <div class="trip-date">شناسه پرداخت: 12354845</div><span>-</span>
                <div class="trip-time">هزینه سفر: 330000 تومان</div><span>-</span>
                <div class="trip-time">وضعیت: در انتظار تایید راننده</div>
              </div>

              <div class="trip-driver-info">
                <img src="{{ asset('img/no-photo.png') }}" alt="تصویر راننده">
                <div class="driver-info">
                  <p><span>راننده: </span>اسم راننده</p>
                  <p><span>ماشین: </span> مدل ماشین</p>
                  <p><span>پلاک: </span>21 ب 341 ایران 99</p>
                </div>
                <a href="tel:09123456789" class="call-to-driver">09123456789</a>
              </div>

              <div class="trip-passenger-info">
                <div class="driver-info">
                  <p><span>مسافر: </span>اسم مسافر</p>
                </div>
                <a href="tel:09123456789" class="call-to-driver">09123456789</a>
              </div>

              <section class="trip-locations-info">
                <div class="trip-location-item"><span>مبدا 1: </span>آدرس مبدا اول</div>
                <div class="trip-location-item"><span>مبدا 2: </span>آدرس مبدا دوم</div>
                <div class="trip-location-item"><span>مقصد 1: </span>آدرس مقصد اول</div>
              </section>

              <div class="user-form-desc">
                <p>توضیحاتی که کاربر در قسمت ثبت رزرو فرم انجام داده در این قسمت نمایش داده میشود.</p>
              </div>
            </div>
          </li>

        </ul>
      </div>
    </div>

   </div>

  <script src="{{ asset('js/admin-profile-travels.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function() {

    console.log("[INIT] اسکریپت لود شد");

    document.querySelectorAll('.ad-driver-trip').forEach(button => {
        button.addEventListener('click', function() {
            const tripId = this.dataset.tripid;
            console.log("[LOG] سفر انتخاب شد:", tripId);

            const selectedTripInput = document.getElementById('selectedTripId');
            if(!selectedTripInput) return console.error("[ERROR] selectedTripId پیدا نشد!");

            selectedTripInput.value = tripId;
            document.getElementById('AddDriverTripPopup').style.display = 'block';
        });
    });

    const searchInput = document.getElementById('driverSearchInput');
    const searchResults = document.getElementById('driverSearchResults');
    const selectedTripInput = document.getElementById('selectedTripId');

    // جستجو
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        console.log("[LOG] جستجوی راننده، query:", query);

        searchResults.innerHTML = '';
        if(query.length < 1) return;

        fetch("{{ route('admin.drivers.search') }}?q=" + encodeURIComponent(query))
            .then(res => {
                console.log("[LOG] پاسخ جستجو:", res.status);
                return res.json();
            })
            .then(drivers => {
                console.log("[LOG] لیست رانندگان:", drivers);

                drivers.forEach(driver => {
                    const li = document.createElement('li');
                    li.textContent = `${driver.first_name} ${driver.last_name}`;
                    li.dataset.driverId = driver.user_id;
                    searchResults.appendChild(li);
                });
            })
            .catch(err => console.error("[ERROR] جستجو:", err));
    });

    // تخصیص راننده
    searchResults.addEventListener('click', function(e) {
        const li = e.target.closest('li');
        if(!li) return;

        const driverId = li.dataset.driverId;
        const tripId = selectedTripInput.value;

        console.log("[LOG] انتخاب راننده:", driverId, "برای سفر:", tripId);

        if(!driverId || !tripId) {
            alert("اطلاعات معتبر نیست");
            return;
        }

        const url = `{{ url('/trips/assign-driver') }}?trip_id=${tripId}&driver_id=${driverId}`;
        console.log("[REQUEST] ارسال GET:", url);

        fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                },
                credentials: 'same-origin' // ← این مهمه: کوکی‌ها را همراه درخواست می‌فرسته
            })
            .then(res => {
                console.log("[LOG] پاسخ سرور برای تخصیص:", res.status);

                return res.text(); // چون ممکنه JSON یا HTML باشه
            })
            .then(text => {
                console.log("[RAW RESPONSE]", text);

                try {
                    const data = JSON.parse(text);

                    if(data.success) {
                        alert(data.message);
                        document.getElementById('AddDriverTripPopup').style.display = 'none';
                        searchInput.value = '';
                        searchResults.innerHTML = '';
                        location.reload();
                    } else {
                        alert("خطا: " + data.message);
                    }
                } catch (e) {
                    console.error("[ERROR] سرور JSON نداد، متن خام:", text);
                    alert("پاسخ غیرمنتظره از سرور دریافت شد.");
                }
            })
            .catch(err => {
                console.error("[ERROR] ارسال درخواست:", err);
                alert("خطا در ارسال درخواست");
            });

    });

});
</script>






</body>
</html>
