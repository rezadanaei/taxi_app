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
    .file-button {
      cursor: pointer;
      display: inline-block;
      padding: 12px 20px;
      background: #f0f0f0;
      border: 2px dashed #ccc;
      border-radius: 8px;
      text-align: center;
      transition: all 0.3s;
    }
    .file-button:hover { background: #e0e0e0; }
    .file-selected {
      background: #d4edda !important;
      border-color: #28a745 !important;
      color: #155724;
    }
  </style>

  <link rel="stylesheet" href="{{ asset('css/jalalidatepicker.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/style.css') }}">

  <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
  <script src="{{ asset('js/jalali-date.js') }}"></script>
  <script src="{{ asset('js/jalalidatepicker.min.js') }}"></script>
  <script src="{{ asset('js/select2.min.js') }}"></script>
</head>
<body>

<div class="user-profile-container max-width">

  <!-- User info -->
  <div class="u-profile-info">
    <img class="u-profile-info-img" src="{{ asset('/img/no-photo.png') }}" alt="تصویر کاربری">
    <section>
      <div class="u-profile-username">
        <h2>نام کاربری</h2>
        <form method="POST" action="{{ route('logout') }}" style="display:inline" id="logoutForm">
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
      <p>راننده گرامی جهت دسترسی به سفرها باید ابتدا اطلاعات خود را ثبت کنید.</p>
    </div>

    @php
        $user = auth()->user();
        $driver = $user->userable;
    @endphp

    <form class="u-driver-form" method="POST" action="{{ route('driver.save') }}" enctype="multipart/form-data">
      @csrf

      <section class="u-driver-personal-info">
        <h2>اطلاعات هویتی</h2>

        <div class="u-driver-grid-2">
          <input type="text" name="first_name" placeholder="نام" value="{{ $driver->first_name ?? '' }}">
          <input type="text" name="last_name" placeholder="نام خانوادگی" value="{{ $driver->last_name ?? '' }}">
          <input type="text" name="father_name" placeholder="نام پدر" value="{{ $driver->father_name ?? '' }}">
          <input type="text" name="birth_date" data-jdp placeholder="تاریخ تولد" value="{{ $driver->birth_date ?? '' }}">
          <input type="hidden" name="start_date" id="startDateFinal">
          <input type="text" name="national_code" placeholder="شماره ملی" value="{{ $driver->national_code ?? '' }}">
          <input type="tel" name="phone" placeholder="شماره موبایل" value="{{ $user->phone ?? '' }}">
          <input class="full-width" type="text" name="address" placeholder="آدرس محل سکونت" value="{{ $driver->address ?? '' }}">
        </div>

        <div class="u-driver-grid-4">

          <div class="file-upload">
            @if($driver->id_card_front)
              <img src="{{ asset('storage/'.$driver->id_card_front) }}" width="120" style="margin-bottom:10px; border-radius:8px;">
            @endif
            <input type="file" name="id_card_front" accept="image/*" capture="camera" id="file_id_card_front" style="display:none;">
            <label for="file_id_card_front" class="file-button">عکس روی کارت ملی</label>
          </div>

          <div class="file-upload">
            @if($driver->id_card_back)
              <img src="{{ asset('storage/'.$driver->id_card_back) }}" width="120" style="margin-bottom:10px; border-radius:8px;">
            @endif
            <input type="file" name="id_card_back" accept="image/*" capture="camera" id="file_id_card_back" style="display:none;">
            <label for="file_id_card_back" class="file-button">عکس پشت کارت ملی</label>
          </div>

          <div class="file-upload">
            @if($driver->id_card_selfie)
              <img src="{{ asset('storage/'.$driver->id_card_selfie) }}" width="120" style="margin-bottom:10px; border-radius:8px;">
            @endif
            <input type="file" name="id_card_selfie" accept="image/*" capture="camera" id="file_id_card_selfie" style="display:none;">
            <label for="file_id_card_selfie" class="file-button">سلفی با کارت ملی</label>
          </div>

          <div class="file-upload">
            @if($driver->profile_photo)
              <img src="{{ asset('storage/'.$driver->profile_photo) }}" width="120" style="margin-bottom:10px; border-radius:8px;">
            @endif
            <input type="file" name="profile_photo" accept="image/*" capture="camera" id="file_profile_photo" style="display:none;">
            <label for="file_profile_photo" class="file-button">تصویر پرسنلی</label>
          </div>

        </div>
      </section>

      <section class="u-driver-car-info">
        <h2>اطلاعات رانندگی</h2>

        <div class="u-driver-grid-2">
          <input type="text" name="car_type" placeholder="نوع ماشین" value="{{ $driver->car_type ?? '' }}">
          <input type="text" name="car_plate" placeholder="پلاک ماشین" value="{{ $driver->car_plate ?? '' }}">
          <input type="text" name="license_number" placeholder="شماره گواهینامه" value="{{ $driver->license_number ?? '' }}">
          <input type="text" name="car_model" placeholder="مدل ماشین" value="{{ $driver->car_model ?? '' }}">
        </div>

        <div class="u-driver-grid-4">

          <div class="file-upload">
            @if($driver->license_front)<img src="{{ asset('storage/'.$driver->license_front) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="license_front" accept="image/*" capture="camera" id="file_license_front" style="display:none;">
            <label for="file_license_front" class="file-button">عکس روی گواهینامه</label>
          </div>

          <div class="file-upload">
            @if($driver->license_back)<img src="{{ asset('storage/'.$driver->license_back) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="license_back" accept="image/*" capture="camera" id="file_license_back" style="display:none;">
            <label for="file_license_back" class="file-button">عکس پشت گواهینامه</label>
          </div>

          <div class="file-upload">
            @if($driver->car_card_front)<img src="{{ asset('storage/'.$driver->car_card_front) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_card_front" accept="image/*" capture="camera" id="file_car_card_front" style="display:none;">
            <label for="file_car_card_front" class="file-button">عکس روی کارت خودرو</label>
          </div>

          <div class="file-upload">
            @if($driver->car_card_back)<img src="{{ asset('storage/'.$driver->car_card_back) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_card_back" accept="image/*" capture="camera" id18="file_car_card_back" style="display:none;">
            <label for="file_car_card_back" class="file-button">عکس پشت کارت خودرو</label>
          </div>

          <div class="file-upload">
            @if($driver->car_insurance)<img src="{{ asset('storage/'.$driver->car_insurance) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_insurance" accept="image/*" capture="camera" id="file_car_insurance" style="display:none;">
            <label for="file_car_insurance" class="file-button">تصویر بیمه ماشین</label>
          </div>

          <div class="file-upload">
            @if($driver->car_front_image)<img src="{{ asset('storage/'.$driver->car_front_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_front_image" accept="image/*" capture="camera" id="file_car_front" style="display:none;">
            <label for="file_car_front" class="file-button">نمای جلوی خودرو</label>
          </div>

          <div class="file-upload">
            @if($driver->car_back_image)<img src="{{ asset('storage/'.$driver->car_back_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_back_image" accept="image/*" capture="camera" id="file_car_back" style="display:none;">
            <label for="file_car_back" class="file-button">نمای عقب خودرو</label>
          </div>

          <div class="file-upload">
            @if($driver->car_left_image)<img src="{{ asset('storage/'.$driver->car_left_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_left_image" accept="image/*" capture="camera" id="file_car_left" style="display:none;">
            <label for="file_car_left" class="file-button">نمای چپ خودرو</label>
          </div>

          <div class="file-upload">
            @if($driver->car_right_image)<img src="{{ asset('storage/'.$driver->car_right_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_right_image" accept="image/*" capture="camera" id="file_car_right" style="display:none;">
            <label for="file_car_right" class="file-button">نمای راست خودرو</label>
          </div>

          <div class="file-upload">
            @if($driver->car_front_seats_image)<img src="{{ asset('storage/'.$driver->car_front_seats_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_front_seats_image" accept="image/*" capture="camera" id="file_car_front_seats" style="display:none;">
            <label for="file_car_front_seats" class="file-button">صندلی جلو و داشبورد</label>
          </div>

          <div class="file-upload">
            @if($driver->car_back_seats_image)<img src="{{ asset('storage/'.$driver->car_back_seats_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_back_seats_image" accept="image/*" capture="camera" id="file_car_back_seats" style="display:none;">
            <label for="file_car_back_seats" class="file-button">صندلی‌های عقب</label>
          </div>

        </div>
      </section>

      <button class="u-driver-form-submit" type="submit">
        ثبت و تایید اطلاعات
      </button>
    </form>
  </div>
</div>

<script>jalaliDatepicker.startWatch();</script>

<!-- اسکریپت جدید آپلود فایل (کاملاً بدون ریلود) -->
<script>
document.querySelectorAll(".file-upload").forEach(box => {
  const input = box.querySelector("input[type=file]");
  const label = box.querySelector("label.file-button");

  // ذخیره متن اصلی دکمه
  if (!label.dataset.originalText) {
    label.dataset.originalText = label.textContent.trim();
  }

  input.addEventListener("change", () => {
    if (input.files && input.files.length > 0) {
      label.classList.add("file-selected");
      label.textContent = "عکس انتخاب شد ✓";
    } else {
      label.classList.remove("file-selected");
      label.textContent = label.dataset.originalText;
    }
  });
});
</script>

<script src="{{ asset('js/Polyline.encoded.js') }}"></script>
<script src="{{ asset('js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('js/converter.js') }}"></script>
<script type="module" src="{{ asset('/js/profile.js') }}"></script>

</body>
</html>