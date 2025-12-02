<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="zones" content='@json($zones)'>

  <!-- Page Title -->
  <title>درخواست ماشین</title>
  <link rel="shortcut icon" href="{{ asset('img/fav.png') }}" type="image/x-icon">
  <!-- Page Style -->
  <style>
      :root {
          --main-color: {{ setting('colers_primary') ?? '#1E90FF' }};
          --second-color: {{ setting('colers_secondary') ?? '#FF4081' }};
          --Third-color: {{ setting('colers_tertiary') ?? '#E0E0E0' }};
      }
  </style>

  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/leaflet.css') }}">
  <link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/jalalidatepicker.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
  <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
  <script src="{{ asset('js/jalalidatepicker.min.js') }}"></script>
  <script src="{{ asset('js/leaflet.js') }}"></script>
  <script src="{{ asset('js/select2.min.js') }}"></script>
</head>
<body>
  <!-- Home page -->
    <!-- Home Header -->
    <header>
      <div class="home-header-left">
        <a href="{{ route('home') }}"><img src="{{ asset('img/home.svg') }}" alt="خانه"></a>
        <button id="tripFAQ"><img src="{{ asset('img/shild.svg') }}" alt="اطلاعات"></button>
        <button id="backBtn" style="display: none;"><img src="{{ asset('img/back.svg') }}" alt="بازگشت"></button>
      </div>
      <div class="home-header-center">
        <a href="https://viptaxiluxe.com/"><img src="{{ asset('img/logo.png') }}" alt="رزرو تاکسی"></a>
      </div>
      <div class="home-header-right">
        <a href="{{ route('user.profile') }}"><img src="{{ asset('img/profile.svg') }}" alt="پروفایل"></a>
      </div>
    </header>
    <!-- Home Header end -->
    <!-- Map -->
    <div id="map"></div>
    <!-- Map end -->
    <!-- User Have a Trip -->

    @if (auth()->check())
      @if(count($trips ?? []) > 0)
        <div class="has-trip-alert margin-bottom">
          <p>شما سفری در حال انجام دارید.</p>
          <button id="openUserCurrentTripPopup"><span>مشاهده جزئیات</span> <img src="{{ asset('img/left-arrow.svg') }}" alt="بیشتر"></button>
        </div>
        <div id="UserCurrentTripPopup">
       <button id="closeUserCurrentTripPopup">بستن</button>
       <div class="passenger-current-trip">
        <h2>سفر های در حال انجام:</h2>
        <ul>
          <!-- Current Trip item -->
          @foreach ($trips as $trip )
            <li>
              <div class="passenger-trip-item">
               
                @php
                    $tripDT = tripDate($trip->start_date);
                @endphp


                <div class="passenger-item-title"><div class="trip-id">کد سفر: {{ $trip->id }}</div> <div class="trip-date">تاریخ: {{ $tripDT['date'] }}</div><div class="trip-time">ساعت: {{ $tripDT['time'] }}</div></div>
                <img src="{{ asset('img/down.svg') }}" alt="فلش">
              </div>
              <div class="passenger-trip-content">
                <div class="trip-extra-info-md"><div class="trip-date">تاریخ: {{ $tripDT['date'] }}</div><div class="trip-time">ساعت: {{ $tripDT['time'] }}</div></div>
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

                  </div>
                @endif
                @php
                  $destinations = json_decode($trip->destinations, true);
                  $origins = json_decode($trip->origins, true);
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
                  <p>{{ $trip->caption }}</p>
                </div>
              </div>
            </li>
          @endforeach
          
        </ul>
      </div>
     </div>
      @endif
      
    @endif
     
     
    <!-- User Have a Trip end -->
    <!-- Buttons -->
    <div id="controls">
      <button id="addBtn">افزودن مبدا</button>
      <button id="confirmBtn" style="display: none;">تایید مبدا</button>
      <button id="calcBtn" style="display: none;">محاسبه</button>
    </div>
    <!-- Buttons end -->
    <!-- Reserve form popup -->
    <div id="popup">
      <form class="popup-content" action="{{ route('trips.store') }}" method="POST">
        @csrf
        <h2>اطلاعات سفر</h2>
        <!-- Chose car swiper -->
        <div class="swiper carChoseSwiper">
          <div class="swiper-wrapper radio-group">
            @foreach ($carTypes as $carType)
            <!-- Car Single item -->
            <div class="swiper-slide radio-option">
              <input class="radio-item" type="radio" name="car_type_id" id="car{{ $carType->id }}" value="{{ $carType->id }}" data-price="{{ $carType->price_per_km }}" @if($loop->first) checked @endif>
              <label class="radio-label" for="car{{ $carType->id }}">
                <strong>{{ $carType->title }}</strong>
                <img src="{{ asset( 'storage/' . $carType->header_image) }}" alt="{{ $carType->title }}">
                <span>{{ number_format($carType->price_per_km) }} تومان</span>
              </label>
            </div>
            <!-- Car Single item end -->
            @endforeach
          </div>
         
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
         
          <div class="swiper-pagination"></div>
        </div>
        <!-- Chose car swiper end -->
        <div id="formContainer">
          <!-- Data and Time picker -->
          <section class="form-data-time">
            <div class="form-data-picker">
              <input type="text" class="form-control" id="rideDate" name="start_date" data-jdp data-jdp-min-date="today" placeholder="تاریخ سوار شدن">
              <img src="{{ asset('img/data-picker.svg') }}" alt="تاریخ سوار شدن">
            </div>
           
            <div class="form-data-picker">
              <input type="text" id="rideTime" data-jdp placeholder="ساعت سوار شدن" data-jdp-only-time>
              <img src="{{ asset('img/time-picker.svg') }}" alt="ساعت سوار شدن">
            </div>
            <input type="hidden" name="start_date" id="startDateFinal">

          </section>
          <!-- Data and Time picker end -->
          <!-- List of Locations -->
          <section class="form-location-lists">
            <img src="{{ asset('img/roads.svg') }}" alt="سفر">
            <div class="locations-list" id="locationsList"></div>
            <input type="hidden" name="origins" id="originsInput" value="">
            <input type="hidden" name="destinations" id="destinationsInput" value="">
          </section>
          <!-- List of Locations end -->
          <!-- passengers & luggage -->
          <section class="form-info-extra">
            <div class="form-input-item">
              <input type="number" id="passengers" name="passenger_count" min="1" max="5" placeholder="تعداد مسافر">
            </div>
            <div class="form-input-item">
              <input type="number" id="luggage" name="luggage_count" min="0" max="4" placeholder="تعداد چمدان">
            </div>
          </section>
          <!-- passengers & luggage end -->
         
          <!-- Pet -->
          <div class="form-have-pet">
            <label><input type="checkbox" id="pet" name="has_pet"> حیوان خانگی</label>
          </div>
          <!-- Pet end -->
          <!-- TripType and Waiting hours -->
          <section class="form-info-extra">
            <div class="form-input-item">
              <select id="tripType" name="trip_type" style="width: 100%;">
                <option value="oneway">یکطرفه</option>
                <option value="round">رفت و برگشت</option>
              </select>
            </div>
            <div class="form-input-item">
              <input type="number" id="waitingHours" name="waiting_hours" min="0" data-price="{{ tariff('waiting_fee') }}" placeholder="ساعات انتظار">
              <input type="hidden" id="tripDuration" name="trip_time" value="">

            </div>
          </section>
          <!-- TripType and Waiting hours end -->
         
          <!-- User Desc -->
          <textarea class="form-input-user-desc" rows="3" name="caption" id=""  placeholder="توضیحات"></textarea>
          <!-- User Desc end -->
        </div>
        <!-- Time and KM result -->
        <div id="results"></div>
        <!-- Time and KM result end -->
        <div class="form-buttons-container">
          <div id="finalPrice"></div>
          <!-- مسافت عادی -->
        <input type="hidden" name="normal_distance" id="normalDistanceInput">

        <!-- مسافت ویژه -->
        <input type="hidden" name="special_distance" id="specialDistanceInput">

        <!-- مسافت کل -->
        <input type="hidden" name="trip_distance" id="totalDistanceInput">
        <input type="hidden" id="hiddenTotalPrice" name="cost" value="">

          <section>
            <button type="button" onclick="closePopup()">بستن</button>
            <button type="submit" class="button" id="">ثبت درخواست</button>
          </section>
        </div>
      </form>
    </div>
    <!-- Reserve form popup end -->
    <!-- FAQ -->
     <div id="faq">
      <div class="faq-content">
        <img src="{{ asset('img/faq.svg') }}" alt="سوالات">
        <ul>
          <li>تمامی رانندگان این سامانه دارای مدارک شناسایی، گواهی عدم سوءپیشینه، و تاییدیه از پلیس راهور می‌باشند.</li>
          <li>تیم پشتیبانی ما در تمام طول شبانه‌روز آماده پاسخگویی به سوالات و گزارش‌های شماست.</li>
          <li>در صورت هرگونه مشکل، بلافاصله رسیدگی خواهد شد.</li>
          <li>اطلاعات شخصی شما محفوظ است و راننده فقط به اطلاعات ضروری (مثل نام و موقعیت مبدا/مقصد) دسترسی دارد.</li>
        </ul>
        <button id="faqClose">بستن</button>
      </div>
     </div>
    <!-- FAQ end -->
    <script src="{{ asset('js/Polyline.encoded.js') }}"></script>
    <script src="{{ asset('js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('js/converter.js') }}"></script>
    <script type="module" src="{{ asset('js/app.js') }}"></script>
    <script>
      function updateStartDate() {
          let date = document.getElementById("rideDate").value;
          let time = document.getElementById("rideTime").value;

          if (!date || !time) {
              document.getElementById("startDateFinal").value = "";
              return;
          }
          let final = `${date} ${time}:00`;

          document.getElementById("startDateFinal").value = final;
      }

      document.getElementById("rideDate").addEventListener("change", updateStartDate);
      document.getElementById("rideTime").addEventListener("change", updateStartDate);

      document.getElementById("rideDate").addEventListener("input", updateStartDate);
      document.getElementById("rideTime").addEventListener("input", updateStartDate);
    </script>

</body>
</html>