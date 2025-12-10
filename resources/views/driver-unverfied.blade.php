<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>حساب رانندگان | تایید نشده</title>
  <link rel="shortcut icon" href="{{ asset('/img/fav.png') }}" type="image/x-icon">

  <style>
    :root {
        --main-color: {{ setting('colers_primary') }};
        --second-color: {{ setting('colers_secondary') }};
        --Third-color: {{ setting('colers_tertiary') }};
    }
    .file-selected { background:#d4edda !important; border-color:#28a745 !important; color:#155724; }
  </style>

  <link rel="stylesheet" href="{{ asset('css/jalalidatepicker.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
  <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
  <script src="{{ asset('js/jalali-date.js') }}"></script>
  <script src="{{ asset('js/jalalidatepicker.min.js') }}"></script>
  <script src="{{ asset('js/select2.min.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
</head>
<body>

<div class="user-profile-container max-width">
  <div class="u-profile-info">
    <img class="u-profile-info-img" src="{{ asset('/img/no-photo.png') }}" alt="تصویر کاربری">
    <section>
      <div class="u-profile-username">
        <h2>نام کاربری</h2>
        <form method="POST" action="{{ route('logout') }}" style="display:inline" class="logout-form" id="logoutForm">
          @csrf
          <button type="submit" id="logoutBtn">خروج</button>
        </form>
      </div>
      <div class="u-profile-type">راننده</div>
    </section>
  </div>

  <div class="u-profile-content">
    <div class="u-driver-top">
      <h1>تکمیل اطلاعات</h1>
      <p>راننده گرامی جهت دسترسی به سفر ها باید ابتدا اطلاعات خود را ثبت کنید.</p>
    </div>
    @if ($errors->any())
        <div class="admin-errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    @if (session('success'))
        <div class="admin-success">
            {{ session('success') }}
        </div>
    @endif
    @php
        $user = auth()->user();
        $driver = $user->userable;
    @endphp

    <form class="u-driver-form" method="POST" action="{{ route('driver.save') }}" enctype="multipart/form-data">
      @csrf

      <section class="u-driver-personal-info">
        <h2>اطلاعات هویتی</h2>
        <div class="u-driver-grid-2">
          <input type="text" name="first_name" placeholder="نام" value="{{ old('first_name',$driver->first_name) ?? '' }}">
          <input type="text" name="last_name" placeholder="نام خانوادگی" value="{{ old('last_name',$driver->last_name) ?? '' }}">
          <input type="text" name="father_name" placeholder="نام پدر" value="{{ old('father_name',$driver->father_name) ?? '' }}">
          <input type="text" name="birth_date" data-jdp value="{{ old('birth_date', $driver->birth_date) ?? '' }}" placeholder="تاریخ تولد">
          <input type="text" name="national_code" placeholder="شماره ملی" value="{{old('national_code',$driver->national_code) ?? '' }}">
          <input type="tel" name="phone" placeholder="شماره موبایل" value="{{ old('phone',$user->phone) ?? '' }}">
          <input class="full-width" type="text" name="address" placeholder="آدرس محل سکونت" value="{{ old('address',$driver->address) ?? '' }}">
        </div>

        <div class="u-driver-grid-4">
          <!-- همه input file ها حذف شدن - فقط span هست -->
          <div class="file-upload">
            @if($driver->id_card_front)<img src="{{ asset('storage/'.$driver->id_card_front) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <span class="file-button camera-opener" data-field="id_card_front">عکس روی کارت ملی</span>
          </div>
          <div class="file-upload">
            @if($driver->id_card_back)<img src="{{ asset('storage/'.$driver->id_card_back) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <span class="file-button camera-opener" data-field="id_card_back">عکس پشت کارت ملی</span>
          </div>
          <div class="file-upload">
            @if($driver->id_card_selfie)<img src="{{ asset('storage/'.$driver->id_card_selfie) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <span class="file-button camera-opener" data-field="id_card_selfie">سلفی با کارت ملی</span>
          </div>
          <div class="file-upload">
            @if($driver->profile_photo)<img src="{{ asset('storage/'.$driver->profile_photo) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <span class="file-button camera-opener" data-field="profile_photo">تصویر پرسنلی</span>
          </div>
        </div>
      </section>

      <section class="u-driver-car-info">
        <h2>اطلاعات رانندگی</h2>
        <div class="u-driver-grid-2">
          <input type="text" name="car_type" placeholder="نوع ماشین" value="{{ old('car_type',$driver->car_type) ?? '' }}">
          {{-- <input type="text" name="car_plate" placeholder="پلاک ماشین" value="{{ $driver->car_plate ?? '' }}"> --}}
          
        <style>
          .plate-container {
            direction: rtl;
            display: flex;
            justify-content: center;
          }

          .plate-box {
            background: #fff;
            border: 3px solid var(--Third-color);
            border-radius: 8px;
            display: flex;
            align-items: center;
            width: 100%;
            justify-content: space-around;
            box-shadow: 0 0 8px #0002;
            position: relative;
          }
          .plate-input,
          .plate-select {
            width: 55px;
            height: 45px;
            font-size: 20px;
            text-align: center;
            border: 0px solid #333;
            border-radius: 6px;
            outline: none;
            font-family: 'Vazir-FD', sans-serif;
          }

          .plate-select {
            width: 70px;
            cursor: pointer;
          }
          .spliter {
            width: 2px;
            height: 100%;
            background: var(--Third-color);
          }
          .plate-container input {
            width: 40px !important;
            padding: 16px 10px !important;
            border-radius: 0px !important;
            text-align: center !important;
            background-color: transparent !important;
            font-size: 18px !important;
          }
        </style>

        <div class="plate-container">
            <div class="plate-box">
                <input type="tel" maxlength="2" class="plate-input" placeholder="20" id="part1">

                <div class="spliter"></div>
                
                <input type="tel" maxlength="3" class="plate-input" placeholder="345" id="part2">

                <select class="plate-select" id="letter">
                    <option value="">حرف</option>
                    <option>الف</option><option>ب</option><option>پ</option><option>ت</option>
                    <option>ث</option><option>ج</option><option>چ</option><option>ح</option>
                    <option>خ</option><option>د</option><option>ذ</option><option>ر</option>
                    <option>ز</option><option>س</option><option>ش</option><option>ص</option>
                    <option>ط</option><option>ق</option><option>ک</option><option>گ</option>
                    <option>ل</option><option>م</option><option>ن</option><option>و</option>
                    <option>ه</option><option>ی</option>
                </select>
                

                <input type="tel" maxlength="2" class="plate-input" placeholder="67" id="part3">
            </div>
        </div>

        <input type="hidden" name="car_plate" id="full_plate" value="{{ old('car_plate', $driver->car_plate) ?? '' }}">

          <script>
            function updateFullPlate() {
              let p1 = document.getElementById('part1').value.trim();
              let p2 = document.getElementById('part2').value.trim();
              let letter = document.getElementById('letter').value;
              let p3 = document.getElementById('part3').value.trim();

              const hiddenInput = document.getElementById('full_plate');

              // اگر هرکدام خالی بود، hidden باید مقدار قبلی خود را نگه دارد (اگر داشت) و فقط زمانی خالی شود که هیچ مقداری نداشته باشد
              if (p1 === '' || p2 === '' || letter === '' || p3 === '') {
                  if (!hiddenInput.value) {
                      hiddenInput.value = ''; // اگر hidden خالی است، خالی بماند
                  }
                  return;
              }

              // اگر همه پر بودند → حالا padStart کنیم
              p1 = p1.padStart(2, '0');
              p2 = p2;
              p3 = p3.padStart(2, '0');

              const full = `${p1} ${p2} ${letter} ${p3}`;
              hiddenInput.value = full;
          }

          document.querySelectorAll('.plate-input, .plate-select').forEach(element => {
              element.addEventListener('input', updateFullPlate);
              element.addEventListener('change', updateFullPlate);
          });

          // فقط وقتی hidden input مقدار ندارد، update کنیم
          if (!document.getElementById('full_plate').value) {
              updateFullPlate();
          }

        </script>
          <input type="text" name="license_number" placeholder="شماره گواهینامه" value="{{ old('license_number',$driver->license_number ) ?? '' }}">
          <input type="text" name="car_model" placeholder="مدل ماشین" value="{{ old('car_model',$driver->car_model) ?? '' }}">
        </div>

       
        <p>لطفاً توجه کنید: بیمه‌نامه خودرو باید دارای کاربری برون‌شهری باشد.</p>
        
        <div class="u-driver-grid-4">
          <div class="file-upload">@if($driver->license_front)<img src="{{ asset('storage/'.$driver->license_front) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif<span class="file-button camera-opener" data-field="license_front">عکس روی گواهینامه</span></div>
          <div class="file-upload">@if($driver->license_back)<img src="{{ asset('storage/'.$driver->license_back) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif<span class="file-button camera-opener" data-field="license_back">عکس پشت گواهینامه</span></div>
          <div class="file-upload">@if($driver->car_card_front)<img src="{{ asset('storage/'.$driver->car_card_front) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif<span class="file-button camera-opener" data-field="car_card_front">عکس روی کارت خودرو</span></div>
          <div class="file-upload">@if($driver->car_card_back)<img src="{{ asset('storage/'.$driver->car_card_back) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif<span class="file-button camera-opener" data-field="car_card_back">عکس پشت کارت خودرو</span></div>
          <div class="file-upload">@if($driver->car_front_image)<img src="{{ asset('storage/'.$driver->car_front_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif<span class="file-button camera-opener" data-field="car_front_image">عکس نمای جلوی خودرو</span></div>
          <div class="file-upload">@if($driver->car_insurance)<img src="{{ asset('storage/'.$driver->car_insurance) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif<span class="file-button camera-opener" data-field="car_insurance">تصویر بیمه ماشین</span></div>
          <div class="file-upload">@if($driver->car_back_image)<img src="{{ asset('storage/'.$driver->car_back_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif<span class="file-button camera-opener" data-field="car_back_image">عکس نمای عقب خودرو</span></div>
          <div class="file-upload">@if($driver->car_left_image)<img src="{{ asset('storage/'.$driver->car_left_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif<span class="file-button camera-opener" data-field="car_left_image">عکس نمای چپ خودرو</span></div>
          <div class="file-upload">@if($driver->car_right_image)<img src="{{ asset('storage/'.$driver->car_right_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif<span class="file-button camera-opener" data-field="car_right_image">عکس نمای راست خودرو</span></div>
          <div class="file-upload">@if($driver->car_front_seats_image)<img src="{{ asset('storage/'.$driver->car_front_seats_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif<span class="file-button camera-opener" data-field="car_front_seats_image">صندلی جلو و داشبورد</span></div>
          <div class="file-upload">@if($driver->car_back_seats_image)<img src="{{ asset('storage/'.$driver->car_back_seats_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif<span class="file-button camera-opener" data-field="car_back_seats_image">صندلی‌های عقب</span></div>
        </div>
      </section>

      <button class="u-driver-form-submit" type="submit">ثبت و تایید اطلاعات</button>
    </form>
  </div>
</div>

<script>jalaliDatepicker.startWatch();</script>
<script src="{{ asset('js/Polyline.encoded.js') }}"></script>
<script src="{{ asset('js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('js/converter.js') }}"></script>
<script type="module" src="{{ asset('/js/profile.js') }}"></script>


</body>
</html>