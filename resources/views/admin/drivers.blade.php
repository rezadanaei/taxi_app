<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مدیریت رانندگان</title>
  <style>
        :root {
            --main-color: {{ setting('colers_primary') ?? '#1E90FF' }};
            --second-color: {{ setting('colers_secondary') ?? '#FF4081' }};
            --Third-color: {{ setting('colers_tertiary') ?? '#E0E0E0' }};
        }
    </style>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

  <div class="admin-driver-container">
    
    <h1>مدیریت رانندگان</h1>

    <div class="admin-driver-content">

      <div id="adminAddEditdriverPopup">
        <div class="admin-add-edit-driver-popup-content">

          <div class="admin-aed-popup-top">
            <h3>ویرایش راننده</h3>
            <form action="{{ route('admin.drivers.toggle.status') }}" method="POST">
              @csrf
              <input type="hidden" name="driver_id" id="driverid" value="">
              <button>غیرفعال کردن</button>
            </form>
            
          </div>
          
          <div class="u-profile-content">

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
            <form class="u-driver-form" enctype="multipart/form-data" method="POST" action="{{ route('admin.drivers.save', $driver->id ?? '') }}">
              @csrf
              <section class="u-driver-personal-info">
                <h2>اطلاعات هویتی</h2>
                <div class="u-driver-grid-2">
                  <input type="hidden" name="id" value="{{ $driver->id ?? '' }}">
                  <input type="text" name="first_name" placeholder="نام" value="{{ $driver->userable->first_name ?? '' }}">
                  <input type="text" name="last_name" placeholder="نام خانوادگی" value="{{ $driver->userable->last_name ?? '' }}">
                  <input type="text" name="father_name" placeholder="نام پدر" value="{{ $driver->userable->father_name ?? '' }}">
                  <input type="text" name="birth_date" placeholder="تاریخ تولد" value="{{ $driver->userable->birth_date ?? '' }}">
                  <input type="text" name="national_code" placeholder="شماره ملی" value="{{ $driver->userable->national_code ?? '' }}">
                  <input type="tel" name="phone" placeholder="شماره موبایل" value="{{ $driver->phone ?? '' }}">
                  <input class="full-width" type="text" name="address" placeholder="آدرس محل سکونت" value="{{ $driver->userable->address ?? '' }}">
                </div>

                <div class="u-driver-grid-4">
                  <div class="file-upload">
                    <input type="file" name="id_card_front">
                    <button type="button" class="file-button">عکس روی کارت ملی</button>
                  </div>
                  <div class="file-upload">
                    <input type="file" name="id_card_back">
                    <button type="button" class="file-button">عکس پشت کارت ملی</button>
                  </div>
                  <div class="file-upload">
                    <input type="file" name="id_card_selfie">
                    <button type="button" class="file-button">سلفی با کارت ملی</button>
                  </div>
                  <div class="file-upload">
                    <input type="file" name="profile_photo">
                    <button type="button" class="file-button">تصویر پرسنلی</button>
                  </div>
                </div>
              </section>

              <section class="u-driver-car-info">
                <h2>اطلاعات رانندگی</h2>
                <div class="u-driver-grid-2">
                  <input type="text" name="car_type" placeholder="نوع ماشین" value="{{ $driver->userable->car_type ?? '' }}">
                  <input type="text" name="car_plate" placeholder="پلاک ماشین" value="{{ $driver->userable->car_plate ?? '' }}">
                  <input type="text" name="license_number" placeholder="شماره گواهی نامه" value="{{ $driver->userable->license_number ?? '' }}">
                  <input type="text" name="car_model" placeholder="مدل ماشین" value="{{ $driver->userable->car_model ?? '' }}">
                </div>

                <div class="u-driver-grid-4">
                  <div class="file-upload">
                    <input type="file" name="license_front">
                    <button type="button" class="file-button">عکس روی گواهینامه</button>
                  </div>
                  <div class="file-upload">
                    <input type="file" name="license_back">
                    <button type="button" class="file-button">عکس پشت گواهینامه</button>
                  </div>
                  <div class="file-upload">
                    <input type="file" name="car_card_front">
                    <button type="button" class="file-button">عکس روی کارت خودرو</button>
                  </div>
                  <div class="file-upload">
                    <input type="file" name="car_card_back">
                    <button type="button" class="file-button">عکس پشت کارت خودرو</button>
                  </div>
                  <div class="file-upload">
                    <input type="file" name="car_insurance">
                    <button type="button" class="file-button">تصویر بیمه ماشین</button>
                  </div>
                

                  <!-- --------------- NEW EXTRA CAR IMAGES ---------------- -->
              
                  <div class="file-upload">
                    <input type="file" name="car_front_image">
                    <button type="button" class="file-button">نمای جلو خودرو</button>
                  </div>
                  <div class="file-upload">
                    <input type="file" name="car_back_image">
                    <button type="button" class="file-button">نمای عقب خودرو</button>
                  </div>
                  <div class="file-upload">
                    <input type="file" name="car_left_image">
                    <button type="button" class="file-button">نمای چپ خودرو</button>
                  </div>
                  <div class="file-upload">
                    <input type="file" name="car_right_image">
                    <button type="button" class="file-button">نمای راست خودرو</button>
                  </div>
                  <div class="file-upload">
                    <input type="file" name="car_front_seats_image">
                    <button type="button" class="file-button">صندلی جلو و داشبورد</button>
                  </div>
                  <div class="file-upload">
                    <input type="file" name="car_back_seats_image">
                    <button type="button" class="file-button">صندلی عقب</button>
                  </div>
                </div>
              </section>

              <button class="u-driver-form-submit" type="submit">ثبت و تایید اطلاعات</button>
            </form>

          
          </div>

        </div>
      </div>

      <ol>
        @foreach ($drivers as $driver)
          <li class="admin-driver-item">
          <div class="admin-driver-item-title"><p>{{ $driver->userable->first_name }} {{ $driver->userable->last_name }}</p></div>
          <div class="admin-driver-item-btn">
            <button id="adminEditdriver"
              data-id="{{ $driver->id }}" 
              data-phone="{{ $driver->phone }}"
              data-firstname="{{ $driver->userable->first_name }}"
              data-lastname="{{ $driver->userable->last_name }}"
              data-fathername="{{ $driver->userable->father_name }}"
              data-birthdate="{{ $driver->userable->birth_date }}"
              data-nationalcode="{{ $driver->userable->national_code }}"
              data-address="{{ $driver->userable->address }}"
              data-idcardfront="{{ asset('storage/'.$driver->userable->id_card_front) }}"
              data-idcardback="{{ asset('storage/'.$driver->userable->id_card_back) }}"
              data-idselfi="{{ asset('storage/'.$driver->userable->id_card_selfie) }}"
              data-profilephoto="{{ asset('storage/'.$driver->userable->profile_photo) }}"
              data-carplate="{{ $driver->userable->car_plate }}"
              data-licensenumber="{{ $driver->userable->license_number }}"
              data-carmodel="{{ $driver->userable->car_model }}"
              data-cartype="{{ $driver->userable->car_type }}"
              data-carcardfront="{{ asset('storage/'.$driver->userable->car_card_front) }}"
              data-carcardback="{{ asset('storage/'.$driver->userable->car_card_back) }}"
              data-carinsure="{{ asset('storage/'.$driver->userable->car_insurance) }}"
              data-licensefront="{{ asset('storage/'.$driver->userable->license_front) }}"
              data-licenseback="{{ asset('storage/'.$driver->userable->license_back) }}"
            >ویرایش</button>
            <form action="{{ route('admin.drivers.delete') }}" method="POST" style="display: inline-block">
              @csrf
              <input type="hidden" name="driver_id" value="{{ $driver->id }}">
              <button id="adminDeletedriver">حذف</button>
            </form>
          </div>
        </li>
        @endforeach
      </ol>

    </div>

   </div>

  <script src="{{ asset('js/admin-profile-drivers.js') }}"></script>
  
</body>
</html>
