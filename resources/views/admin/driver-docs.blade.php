<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>بررسی مدارک رانندگان</title>
  <style>
        :root {
            --main-color: {{ setting('colers_primary') ?? '#1E90FF' }};
            --second-color: {{ setting('colers_secondary') ?? '#FF4081' }};
            --Third-color: {{ setting('colers_tertiary') ?? '#E0E0E0' }};
        }

        select {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            background: white;
            color: #333;
            font-family: 'Vazir-FD', sans-serif;
        }
        select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }

        .images { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 15px; }
        .images div { text-align: center; }
        .images img { width: 120px; border: 1px solid #ddd; border-radius: 8px; }
  </style>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

  <div class="admin-driverdock-container">
    
    <h1>بررسی مدارک رانندگان</h1>

    <div class="admin-driverdock-content">
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

      <!-- پاپ‌آپ مشاهده مدارک -->
      <div id="adminAddSeedriverdockPopup" style="display:none;">
        <form class="admin-see-driverdock-popup-content" action="{{ route('admin.drivers.documents.approve') }}" method="POST">
          @csrf
          <h3>بررسی مدارک راننده</h3>

          <section>
            <p>نام: <span id="popupFirstName"></span></p>
            <p>نام خانوادگی: <span id="popupLastName"></span></p>
            <p>نام پدر: <span id="popupFatherName"></span></p>
            <p>تاریخ تولد: <span id="popupBirthDate"></span></p>
            <p>شماره ملی: <span id="popupNationalCode"></span></p>
            <p>شماره تلفن: <span id="popupPhone"></span></p>
            <p>آدرس محل سکونت: <span id="popupAddress"></span></p>

            <!-- مدارک شخصی -->
            <div class="images">
              <div class="id-card-front">
                <p>عکس روی کارت ملی</p>
                <img id="popupIdCardFront" src="{{ asset('img/no-photo.png') }}">
              </div>

              <div class="id-card-back">
                <p>عکس پشت کارت ملی</p>
                <img id="popupIdCardBack" src="{{ asset('img/no-photo.png') }}">
              </div>

              <div class="id-card-selfi">
                <p>سلفی با کارت ملی</p>
                <img id="popupIdSelfi" src="{{ asset('img/no-photo.png') }}">
              </div>

              <div class="id-selfi">
                <p>تصویر پرسنلی</p>
                <img id="popupProfilePhoto" src="{{ asset('img/no-photo.png') }}">
              </div>
            </div>

            <p>نوع ماشین: <span id="popupCarType"></span> 
                <select name="car_id" id="carSelect" class="category-select">
                    <option value="">انتخاب نوع خودرو</option>
                    @foreach ($cars as $car)
                        <option value="{{ $car->id }}">{{ $car->name }}</option>
                    @endforeach
                </select>
            </p> 
            <p>پلاک ماشین: <span id="popupCarPlate"></span></p>
            <p>شماره گواهینامه: <span id="popupLicenseNumber"></span></p>
            <p>مدل ماشین: <span id="popupCarModel"></span></p>

            <!-- مدارک خودرو -->
            <div class="images">
              <div class="car-id-card-front">
                <p>عکس روی گواهینامه</p>
                <img id="popupLicenseFront" src="{{ asset('img/no-photo.png') }}">
              </div>

              <div class="car-id-card-back">
                <p>عکس پشت گواهینامه</p>
                <img id="popupLicenseBack" src="{{ asset('img/no-photo.png') }}">
              </div>

              <div class="id-car-card-front">
                <p>عکس روی کارت خودرو</p>
                <img id="popupCarCardFront" src="{{ asset('img/no-photo.png') }}">
              </div>

              <div class="id-car-card-back">
                <p>عکس پشت کارت خودرو</p>
                <img id="popupCarCardBack" src="{{ asset('img/no-photo.png') }}">
              </div>

              <div class="car-insure-card">
                <p>تصویر بیمه ماشین</p>
                <img id="popupCarInsure" src="{{ asset('img/no-photo.png') }}">
              </div>

              <!-- 6 تصویر جدید خودرو -->
              <div class="car-front-image">
                <p>نمای جلوی خودرو</p>
                <img id="popupCarFrontImage" src="{{ asset('img/no-photo.png') }}">
              </div>

              <div class="car-back-image">
                <p>نمای عقب خودرو</p>
                <img id="popupCarBackImage" src="{{ asset('img/no-photo.png') }}">
              </div>

              <div class="car-left-image">
                <p>نمای چپ خودرو</p>
                <img id="popupCarLeftImage" src="{{ asset('img/no-photo.png') }}">
              </div>

              <div class="car-right-image">
                <p>نمای راست خودرو</p>
                <img id="popupCarRightImage" src="{{ asset('img/no-photo.png') }}">
              </div>

              <div class="car-front-seats-image">
                <p>صندلی جلو و داشبورد</p>
                <img id="popupCarFrontSeatsImage" src="{{ asset('img/no-photo.png') }}">
              </div>

              <div class="car-back-seats-image">
                <p>صندلی‌های عقب</p>
                <img id="popupCarBackSeatsImage" src="{{ asset('img/no-photo.png') }}">
              </div>

            </div>
          </section>

          <input type="hidden" name="note_id" id="note_id">
          <div class="admin-driver-doc-actions">
            <button type="submit">تایید اطلاعات</button>
            <button type="button" id="adminAddSeedriverdockPopupClose">بستن</button>
          </div>
        </form>
      </div>

      <!-- پاپ‌آپ رد درخواست -->
      <div id="adminAddDeletedriverdockPopup">
        <div class="admin-delete-driverdock-popup-content">
          <h3>رد درخواست</h3>
          <form action="{{ route('admin.drivers.documents.reject') }}" method="POST">
            @csrf
            <input type="hidden" name="note_id" value="">
            <textarea name="message" id="message" placeholder="دلیل رد درخواست" rows="4"></textarea>
            <section>
              <button type="submit">رد درخواست</button>
              <button type="button" id="adminAddDeletedriverdockPopupClose">بستن</button>
            </section>
          </form>
        </div>
      </div>

      <!-- لیست نوتیفیکیشن‌ها -->
      <ol>
        @foreach($notifications as $note)
            <li>
              <div class="admin-driverdock-item-title"><p>{{ $note->message }}</p></div>
              <div class="admin-driverdock-item-btn">
                <button id="adminSeeDriverDoc"
                  data-noteid="{{ $note->id }}"
                  data-id="{{ $note->driver->id }}"
                  data-phone="{{ $note->driver->phone }}"
                  data-firstname="{{ $note->driver->userable->first_name }}"
                  data-lastname="{{ $note->driver->userable->last_name }}"
                  data-fathername="{{ $note->driver->userable->father_name }}"
                  data-birthdate="{{ $note->driver->userable->birth_date }}"
                  data-nationalcode="{{ $note->driver->userable->national_code }}"
                  data-address="{{ $note->driver->userable->address }}"
                  data-idcardfront="{{ asset('storage/'.$note->driver->userable->id_card_front) }}"
                  data-idcardback="{{ asset('storage/'.$note->driver->userable->id_card_back) }}"
                  data-idselfi="{{ asset('storage/'.$note->driver->userable->id_card_selfie) }}"
                  data-profilephoto="{{ asset('storage/'.$note->driver->userable->profile_photo) }}"
                  data-carplate="{{ $note->driver->userable->car_plate }}"
                  data-licensenumber="{{ $note->driver->userable->license_number }}"
                  data-carmodel="{{ $note->driver->userable->car_model }}"
                  data-cartype="{{ $note->driver->userable->car_type }}"
                  data-carcardfront="{{ asset('storage/'.$note->driver->userable->car_card_front) }}"
                  data-carcardback="{{ asset('storage/'.$note->driver->userable->car_card_back) }}"
                  data-carinsure="{{ asset('storage/'.$note->driver->userable->car_insurance) }}"
                  data-licensefront="{{ asset('storage/'.$note->driver->userable->license_front) }}"
                  data-licenseback="{{ asset('storage/'.$note->driver->userable->license_back) }}"

                  data-carfrontimage="{{ asset('storage/'.$note->driver->userable->car_front_image) }}"
                  data-carbackimage="{{ asset('storage/'.$note->driver->userable->car_back_image) }}"
                  data-carleftimage="{{ asset('storage/'.$note->driver->userable->car_left_image) }}"
                  data-carrightimage="{{ asset('storage/'.$note->driver->userable->car_right_image) }}"
                  data-carfrontseatsimage="{{ asset('storage/'.$note->driver->userable->car_front_seats_image) }}"
                  data-carbackseatsimage="{{ asset('storage/'.$note->driver->userable->car_back_seats_image) }}"
                >
                  مشاهده
                </button>
                <button id="adminDeletedriverdockDoc" data-noteId="{{ $note->id }}">رد درخواست</button>
              </div>
            </li>
        @endforeach
      </ol>

    </div>
  </div>

  <script src="{{ asset('js/admin-profile-driverdocks.js') }}"></script>
  
 
</body>
</html>
