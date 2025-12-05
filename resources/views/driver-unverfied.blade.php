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
          <input type="text" name="birth_date" data-jdp value="{{ $driver->birth_date ?? '' }}">
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
            <input type="file" name="id_card_front" accept="image/*" id="cam_id_card_front" style="display:none;">
            <span class="file-button camera-opener" data-input="cam_id_card_front">عکس روی کارت ملی</span>
          </div>

          <div class="file-upload">
            @if($driver->id_card_back)
              <img src="{{ asset('storage/'.$driver->id_card_back) }}" width="120" style="margin-bottom:10px; border-radius:8px;">
            @endif
            <input type="file" name="id_card_back" accept="image/*" id="cam_id_card_back" style="display:none;">
            <span class="file-button camera-opener" data-input="cam_id_card_back">عکس پشت کارت ملی</span>
          </div>

          <div class="file-upload">
            @if($driver->id_card_selfie)
              <img src="{{ asset('storage/'.$driver->id_card_selfie) }}" width="120" style="margin-bottom:10px; border-radius:8px;">
            @endif
            <input type="file" name="id_card_selfie" accept="image/*" id="cam_id_card_selfie" style="display:none;">
            <span class="file-button camera-opener" data-input="cam_id_card_selfie">سلفی با کارت ملی</span>
          </div>

          <div class="file-upload">
            @if($driver->profile_photo)
              <img src="{{ asset('storage/'.$driver->profile_photo) }}" width="120" style="margin-bottom:10px; border-radius:8px;">
            @endif
            <input type="file" name="profile_photo" accept="image/*" id="cam_profile_photo" style="display:none;">
            <span class="file-button camera-opener" data-input="cam_profile_photo">تصویر پرسنلی</span>
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
            <input type="file" name="license_front" accept="image/*" id="cam_license_front" style="display:none;">
            <span class="file-button camera-opener" data-input="cam_license_front">عکس روی گواهینامه</span>
          </div>

          <div class="file-upload">
            @if($driver->license_back)<img src="{{ asset('storage/'.$driver->license_back) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="license_back" accept="image/*" id="cam_license_back" style="display:none;">
            <span class="file-button camera-opener" data-input="cam_license_back">عکس پشت گواهینامه</span>
          </div>

          <div class="file-upload">
            @if($driver->car_card_front)<img src="{{ asset('storage/'.$driver->car_card_front) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_card_front" accept="image/*" id="cam_car_card_front" style="display:none;">
            <span class="file-button camera-opener" data-input="cam_car_card_front">عکس روی کارت خودرو</span>
          </div>

          <div class="file-upload">
            @if($driver->car_card_back)<img src="{{ asset('storage/'.$driver->car_card_back) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_card_back" accept="image/*" id="cam_car_card_back" style="display:none;">
            <span class="file-button camera-opener" data-input="cam_car_card_back">عکس پشت کارت خودرو</span>
          </div>

          <div class="file-upload">
            @if($driver->car_insurance)<img src="{{ asset('storage/'.$driver->car_insurance) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_insurance" accept="image/*" id="cam_car_insurance" style="display:none;">
            <span class="file-button camera-opener" data-input="cam_car_insurance">تصویر بیمه ماشین</span>
          </div>

          <div class="file-upload">
            @if($driver->car_front_image)<img src="{{ asset('storage/'.$driver->car_front_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_front_image" accept="image/*" id="cam_car_front" style="display:none;">
            <span class="file-button camera-opener" data-input="cam_car_front">عکس نمای جلوی خودرو</span>
          </div>

          <div class="file-upload">
            @if($driver->car_back_image)<img src="{{ asset('storage/'.$driver->car_back_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_back_image" accept="image/*" id="cam_car_back" style="display:none;">
            <span class="file-button camera-opener" data-input="cam_car_back">عکس نمای عقب خودرو</span>
          </div>

          <div class="file-upload">
            @if($driver->car_left_image)<img src="{{ asset('storage/'.$driver->car_left_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_left_image" accept="image/*" id="cam_car_left" style="display:none;">
            <span class="file-button camera-opener" data-input="cam_car_left">عکس نمای چپ خودرو</span>
          </div>

          <div class="file-upload">
            @if($driver->car_right_image)<img src="{{ asset('storage/'.$driver->car_right_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_right_image" accept="image/*" id="cam_car_right" style="display:none;">
            <span class="file-button camera-opener" data-input="cam_car_right">عکس نمای راست خودرو</span>
          </div>

          <div class="file-upload">
            @if($driver->car_front_seats_image)<img src="{{ asset('storage/'.$driver->car_front_seats_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_front_seats_image" accept="image/*" id="cam_car_front_seats" style="display:none;">
            <span class="file-button camera-opener" data-input="cam_car_front_seats">صندلی جلو و داشبورد</span>
          </div>

          <div class="file-upload">
            @if($driver->car_back_seats_image)<img src="{{ asset('storage/'.$driver->car_back_seats_image) }}" width="120" style="margin-bottom:10px; border-radius:8px;">@endif
            <input type="file" name="car_back_seats_image" accept="image/*" id="cam_car_back_seats" style="display:none;">
            <span class="file-button camera-opener" data-input="cam_car_back_seats">صندلی‌های عقب</span>
          </div>

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

<!-- In-browser camera only - NO gallery at all, front/back switch, default back camera -->
<script>
let stream = null;
let currentFacingMode = 'environment';

document.querySelectorAll('.camera-opener').forEach(opener => {
  const inputId = opener.dataset.input;
  const input = document.getElementById(inputId);
  const originalText = opener.textContent.trim();

  opener.addEventListener('click', async e => {
    e.preventDefault();
    e.stopPropagation();

    if (input.files.length > 0) {
      if (!confirm('عکس قبلاً گرفته شده. دوباره بگیرید؟')) return;
      input.value = '';
      opener.textContent = originalText;
      opener.classList.remove('file-selected');
      const prevImg = opener.parentElement.querySelector('img');
      if (prevImg && !prevImg.src.includes('storage')) prevImg.remove();
    }

    const modal = document.createElement('div');
    modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:#000;z-index:9999;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#fff;';
    modal.innerHTML = `
      <video id="camVideo" autoplay playsinline style="width:90%;max-width:500px;border-radius:16px;"></video>
      <div style="margin:20px 0;display:flex;gap:20px;align-items:center;">
        <button id="takePhoto" style="padding:15px 35px;background:#28a745;color:#fff;border:none;border-radius:50px;font-size:18px;">عکس بگیر</button>
        <button id="switchCam" style="padding:12px 18px;background:#444;color:#fff;border:none;border-radius:50%;font-size:20px;">Switch</button>
        <button id="closeCam" style="padding:15px 35px;background:#dc3545;color:#fff;border:none;border-radius:50px;font-size:18px;">بستن</button>
      </div>
    `;
    document.body.appendChild(modal);

    const video = modal.querySelector('#camVideo');
    const switchBtn = modal.querySelector('#switchCam');

    const startCamera = async (mode) => {
      if (stream) stream.getTracks().forEach(t => t.stop());
      try {
        stream = await navigator.mediaDevices.getUserMedia({
          video: { facingMode: mode },
          audio: false
        });
        video.srcObject = stream;
        currentFacingMode = mode;
        switchBtn.textContent = mode === 'environment' ? 'Selfie' : 'Back';
      } catch (err) {
        alert('دوربین در دسترس نیست');
        modal.remove();
      }
    };

    await startCamera('environment');

    switchBtn.onclick = () => {
      startCamera(currentFacingMode === 'environment' ? 'user' : 'environment');
    };

    modal.querySelector('#closeCam').onclick = () => {
      if (stream) stream.getTracks().forEach(t => t.stop());
      modal.remove();
    };

    modal.querySelector('#takePhoto').onclick = () => {
      const canvas = document.createElement('canvas');
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      canvas.getContext('2d').drawImage(video, 0, 0);

      canvas.toBlob(blob => {
        const file = new File([blob], `${input.name}.jpg`, { type: 'image/jpeg' });
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;

        let img = opener.parentElement.querySelector('img');
        if (!img) {
          img = document.createElement('img');
          img.width = 120;
          img.style.marginBottom = '10px';
          img.style.borderRadius = '8px';
          opener.parentElement.insertBefore(img, opener);
        }
        img.src = URL.createObjectURL(blob);

        opener.textContent = 'عکس گرفته شد';
        opener.classList.add('file-selected');

        if (stream) stream.getTracks().forEach(t => t.stop());
        modal.remove();
      }, 'image/jpeg', 0.95);
    };
  });
});
</script>

</body>
</html>