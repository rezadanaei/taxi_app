<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Page Title -->
  <title>حساب رانندگان | تایید نشده</title>
  <link rel="shortcut icon" href="{{ asset('/img/fav.png') }}" type="image/x-icon">
  <!-- Page Style -->
  <style>
    :root {

        --main-color: {{ setting('colers_primary') }};
        --second-color: {{ setting('colers_secondary') }};
        --Third-color: {{ setting('colers_tertiary') }};
      }
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
  
  <!-- User Profile page -->
   <div class="user-profile-container max-width">

    <!-- User info -->
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
    <!-- User info end -->

    <div class="u-profile-content">

      <!-- Unverfied driver form -->
       <div class="u-driver-top">
        <h1>تکمیل اطلاعت</h1>
        <p>راننده گرامی جهت دسترسی به سفر ها باید ابتدا اطلاعات خود را ثبت کنید.</p>
       </div>

        @php
            $user = auth()->user();
            $driver = $user->userable;
        @endphp


       <form class="u-driver-form" 
          method="POST" 
          action="{{ route('driver.save') }}" 
          enctype="multipart/form-data">

        @csrf

        <!-- Driver Personal info -->
        <section class="u-driver-personal-info">

            <h2>اطلاعات هویتی</h2>

            <div class="u-driver-grid-2">

                <input type="text" name="first_name" placeholder="نام"
                      value="{{ $driver->first_name ?? '' }}">

                <input type="text" name="last_name" placeholder="نام خانوادگی"
                      value="{{ $driver->last_name ?? '' }}">

                <input type="text" name="father_name" placeholder="نام پدر"
                      value="{{ $driver->father_name ?? '' }}">
                {{-- <input 
                    type="text" 
                    name="birth_date" 
                    id="rideDate" 
                    class="form-control" 
                    data-jdp 
                    data-jdp-min-date="today" 
                    placeholder="تاریخ تولد"
                    value="{{ $driver->birth_date ?? '' }}"> --}}
                
          
            <input type="text" name="birth_date"  data-jdp value="{{ $driver->birth_date ?? '' }}">
            
            <input type="hidden" name="start_date" id="startDateFinal">

         


                <input type="text" name="national_code" placeholder="شماره ملی"
                      value="{{ $driver->national_code ?? '' }}">

                <input type="tel" name="phone" placeholder="شماره موبایل"
                      value="{{ $user->phone ?? '' }}">

                <input class="full-width" type="text" name="address" placeholder="آدرس محل سکونت"
                      value="{{ $driver->address ?? '' }}">

            </div>

            <div class="u-driver-grid-4">

                <!-- Id card front -->
                <div class="file-upload">
                    @if($driver->id_card_front)
                        <img src="{{ asset('storage/'.$driver->id_card_front) }}" width="120" style="margin-bottom:10px;">
                    @endif
                    <input type="file" name="id_card_front" accept="image/*" capture="camera">
                    <button type="button" class="file-button">عکس روی کارت ملی</button>
                </div>

                <!-- Id card back -->
                <div class="file-upload">
                    @if($driver->id_card_back)
                        <img src="{{ asset('storage/'.$driver->id_card_back) }}" width="120" style="margin-bottom:10px;">
                    @endif
                    <input type="file" name="id_card_back" accept="image/*" capture="camera">
                    <button type="button" class="file-button">عکس پشت کارت ملی</button>
                </div>

                <!-- Id card selfie -->
                <div class="file-upload">
                    @if($driver->id_card_selfie)
                        <img src="{{ asset('storage/'.$driver->id_card_selfie) }}" width="120" style="margin-bottom:10px;">
                    @endif
                    <input type="file" name="id_card_selfie" accept="image/*" capture="camera">
                    <button type="button" class="file-button">سلفی با کارت ملی</button>
                </div>

                <!-- Profile photo -->
                <div class="file-upload">
                    @if($driver->profile_photo)
                        <img src="{{ asset('storage/'.$driver->profile_photo) }}" width="120" style="margin-bottom:10px;">
                    @endif
                    <input type="file" name="profile_photo" accept="image/*" capture="camera">
                    <button type="button" class="file-button">تصویر پرسنلی</button>
                </div>

            </div>

        </section>
        <!-- Driver Personal info end -->

        <!-- Driver car info -->
        <section class="u-driver-car-info">

            <h2>اطلاعات رانندگی</h2>

            <div class="u-driver-grid-2">

                <input type="text" name="car_type" placeholder="نوع ماشین"
                      value="{{ $driver->car_type ?? '' }}">

                <input type="text" name="car_plate" placeholder="پلاک ماشین"
                      value="{{ $driver->car_plate ?? '' }}">

                <input type="text" name="license_number" placeholder="شماره گواهینامه"
                      value="{{ $driver->license_number ?? '' }}">

                <input type="text" name="car_model" placeholder="مدل ماشین"
                      value="{{ $driver->car_model ?? '' }}">

            </div>

            <div class="u-driver-grid-4">

            <!-- Driving license front -->
            <div class="file-upload">
                @if($driver->license_front)
                    <img src="{{ asset('storage/'.$driver->license_front) }}" width="120" style="margin-bottom:10px;">
                @endif
                <input type="file" name="license_front" accept="image/*" capture="camera">
                <button type="button" class="file-button">عکس روی گواهینامه</button>
            </div>

            <!-- Driving license back -->
            <div class="file-upload">
                @if($driver->license_back)
                    <img src="{{ asset('storage/'.$driver->license_back) }}" width="120" style="margin-bottom:10px;">
                @endif
                <input type="file" name="license_back" accept="image/*" capture="camera">
                <button type="button" class="file-button">عکس پشت گواهینامه</button>
            </div>

            <!-- Car ID front -->
            <div class="file-upload">
                @if($driver->car_card_front)
                    <img src="{{ asset('storage/'.$driver->car_card_front) }}" width="120" style="margin-bottom:10px;">
                @endif
                <input type="file" name="car_card_front" accept="image/*" capture="camera">
                <button type="button" class="file-button">عکس روی کارت خودرو</button>
            </div>

            <!-- Car ID back -->
            <div class="file-upload">
                @if($driver->car_card_back)
                    <img src="{{ asset('storage/'.$driver->car_card_back) }}" width="120" style="margin-bottom:10px;">
                @endif
                <input type="file" name="car_card_back" accept="image/*" capture="camera">
                <button type="button" class="file-button">عکس پشت کارت خودرو</button>
            </div>

            <!-- Car insurance -->
            <div class="file-upload">
                @if($driver->car_insurance)
                    <img src="{{ asset('storage/'.$driver->car_insurance) }}" width="120" style="margin-bottom:10px;">
                @endif
                <input type="file" name="car_insurance" accept="image/*" capture="camera">
                <button type="button" class="file-button">تصویر بیمه ماشین</button>
            </div>


            <!-- --------------- NEW EXTRA CAR IMAGES ---------------- -->

            <!-- Front of car -->
            <div class="file-upload">
                @if($driver->car_front_image)
                    <img src="{{ asset('storage/'.$driver->car_front_image) }}" width="120" style="margin-bottom:10px;">
                @endif
                <input type="file" name="car_front_image" accept="image/*" capture="camera">
                <button type="button" class="file-button">عکس نمای جلوی خودرو</button>
            </div>

            <!-- Back of car -->
            <div class="file-upload">
                @if($driver->car_back_image)
                    <img src="{{ asset('storage/'.$driver->car_back_image) }}" width="120" style="margin-bottom:10px;">
                @endif
                <input type="file" name="car_back_image" accept="image/*" capture="camera">
                <button type="button" class="file-button">عکس نمای عقب خودرو</button>
            </div>

            <!-- Left side -->
            <div class="file-upload">
                @if($driver->car_left_image)
                    <img src="{{ asset('storage/'.$driver->car_left_image) }}" width="120" style="margin-bottom:10px;">
                @endif
                <input type="file" name="car_left_image" accept="image/*" capture="camera">
                <button type="button" class="file-button">عکس نمای چپ خودرو</button>
            </div>

            <!-- Right side -->
            <div class="file-upload">
                @if($driver->car_right_image)
                    <img src="{{ asset('storage/'.$driver->car_right_image) }}" width="120" style="margin-bottom:10px;">
                @endif
                <input type="file" name="car_right_image" accept="image/*" capture="camera">
                <button type="button" class="file-button">عکس نمای راست خودرو</button>
            </div>

            <!-- Front seats + dashboard -->
            <div class="file-upload">
                @if($driver->car_front_seats_image)
                    <img src="{{ asset('storage/'.$driver->car_front_seats_image) }}" width="120" style="margin-bottom:10px;">
                @endif
                <input type="file" name="car_front_seats_image" accept="image/*" capture="camera">
                <button type="button" class="file-button">صندلی جلو و داشبورد</button>
            </div>

            <!-- Back seats -->
            <div class="file-upload">
                @if($driver->car_back_seats_image)
                    <img src="{{ asset('storage/'.$driver->car_back_seats_image) }}" width="120" style="margin-bottom:10px;">
                @endif
                <input type="file" name="car_back_seats_image" accept="image/*" capture="camera">
                <button type="button" class="file-button">صندلی‌های عقب</button>
            </div>

        </div>


        </section>
        <!-- Driver car info end -->

        <button class="u-driver-form-submit" type="submit">
            ثبت و تایید اطلاعات
        </button>

    </form>


      <!-- Unverfied driver form end -->
      
    </div>

   </div>
  <!-- User Profile page end -->
  <script>jalaliDatepicker.startWatch();</script>
  <script src="{{ asset('js/Polyline.encoded.js') }}"></script>
  <script src="{{ asset('js/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('js/converter.js') }}"></script>
  <script type="module" src="{{ asset('/js/profile.js') }}"></script>

</body>
</html>