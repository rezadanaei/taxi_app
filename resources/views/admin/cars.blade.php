<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Page Title -->
  <title>خودرو ها</title>
  <!-- Page Style -->
  <style>
        :root {
            --main-color: {{ setting('colers_primary') ?? '#1E90FF' }};
            --second-color: {{ setting('colers_secondary') ?? '#FF4081' }};
            --Third-color: {{ setting('colers_tertiary') ?? '#E0E0E0' }};
        }
    </style>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <!-- Favicon -->
  <style>
    /* استایل تب‌ها با همان حس و حال قبلی */
    .admin-car-tabs {
      display: flex;
      border-bottom: 1px solid #ddd;
      margin-bottom: 20px;
    }
    .admin-car-tab {
      padding: 12px 24px;
      cursor: pointer;
      background: #f9f9f9;
      border: 1px solid #ddd;
      border-bottom: none;
      border-radius: 8px 8px 0 0;
      margin-left: 5px;
      font-weight: bold;
      transition: all 0.3s ease;
    }
    .admin-car-tab.active {
      background: #fff;
      border-bottom: 1px solid #fff;
      position: relative;
      top: 1px;
    }
    .admin-car-tab-content {
      display: none;
    }
    .admin-car-tab-content.active {
      display: block;
    }
    #adminAddEditCar2Popup {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgb(255 255 255 / 14%);
      backdrop-filter: blur(4px);
      z-index: 20;
      display: none;
    }
    #adminAddCar2 {
      all: unset;
      color: var(--main-color);
      padding: 12px 36px;
      border-radius: 16px;
      border: 1px solid var(--main-color);
      cursor: pointer;
    }
    #adminEditCar2 {
      color: var(--Third-color);
    }
    #adminDeleteCar2 {
      color: var(--second-color);
    }
    .u-driver-grid {
      display: grid;
      gap: 20px;
    }
    .u-driver-grid select {
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 15px;
      background: white;
      color: #333;
      font-family: 'Vazir-FD', sans-serif;
    }
    .u-driver-grid select:focus {
      outline: none;
      border-color: #007bff;
      box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
    }
    .admin-car-item-title {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      flex-wrap: wrap;
    }
    .admin-car-item-title span {
      opacity: 0.7;
      color: var(--second-color);
      padding-inline: 4px;
    }
  </style>
</head>
<body>
  <!-- Admin Car page -->
  <div class="admin-car-container">
    <h1>خودرو ها</h1>

    <!-- تب‌ها -->
    <div class="admin-car-tabs">
      <div class="admin-car-tab active" data-tab="categories">دسته ماشین</div>
      <div class="admin-car-tab" data-tab="cars">ماشین‌ها</div>
    </div>

    <!-- محتوای تب ماشین‌ها -->
    <div id="categories" class="admin-car-tab-content active">
      <div class="admin-car-content">
        @if (session('error'))
            <div class="admin-errors">
                {{ session('error') }}
            </div>
        @endif
  
        {{-- نمایش موفقیت --}}
        @if (session('success'))
            <div class="admin-success">
                {{ session('success') }}
            </div>
        @endif
        <!-- Add/Edit Admin car -->
        <div id="adminAddEditCarPopup">
          <div class="admin-add-edit-car-popup-content">
            <h3>ثبت / ویرایش دسته خودرو</h3>
           <form action="{{ route('admin.car-types.save') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <input type="hidden" name="carType_id" id="carType_id" value="">

              <div class="u-driver-grid-2">
                  <input type="text" name="title" id="carTypeTitle" placeholder="نام دسته خودرو">
                  <input type="text" name="description" id="carTypeDesc" placeholder="توضیحات...">
                  <input type="number" name="price_per_km" id="carTypePrice" placeholder="قیمت هر کیلومتر">

                  <div class="file-upload">
                      <input type="file" name="header_image" id="carTypeImage">
                      <button type="button" class="file-button">انتخاب تصویر خودرو</button>
                  </div>
              </div>

              <button type="submit">ذخیره و ثبت</button>
          </form>

          </div>
        </div>
        <!-- Add/Edit Admin car end -->
        <ul>
          
          @foreach ($carTypes as $carType)
            <li class="admin-car-item">
              <div class="admin-car-item-title"><p>{{ $carType->title }}</p></div>
              <div class="admin-car-item-btn">
              <button id="adminEditCar" data-id="{{ $carType->id }}" data-title="{{ $carType->title }}" data-desc="{{ $carType->description }}" data-price_per_km="{{ $carType->price_per_km }}" data-header_image="{{asset('storage/' . $carType->header_image) }}">ویرایش</button>
              <form action="{{ route('admin.car-types.delete') }}" method="POST" class="delete-car-form">
                  @csrf
                  <input type="hidden" name="carType_id" value="{{ $carType->id }}"> <!-- مقدار id خودرو را قرار بده -->
                  <button type="submit" id="adminDeleteCar2">حذف</button>
              </form>

            </div>
            </li>
          @endforeach
          
          <!-- Car item end -->
        </ul>
        <button id="adminAddCar">افزودن دسته</button>
      </div>
    </div>

    <!-- محتوای تب دسته ماشین -->
    <div id="cars" class="admin-car-tab-content">
      <div class="admin-car-content">
        @if (session('error'))
          <div class="admin-errors">
              {{ session('error') }}
          </div>
      @endif

      {{-- نمایش موفقیت --}}
      @if (session('success'))
          <div class="admin-success">
              {{ session('success') }}
          </div>
      @endif
        <!-- Add/Edit Car2 Popup -->
       <div id="adminAddEditCar2Popup" class="admin-popup">
          <div class="admin-add-edit-car-popup-content">
            <h3>ثبت / ویرایش ماشین</h3>
            <form action="{{ route('admin.cars.save') }}" method="POST">
                @csrf
                <!-- hidden input برای شناسایی ویرایش -->
                <input type="hidden" name="car_id" id="car_id" value="">

                <div class="u-driver-grid">
                    <!-- نام ماشین -->
                    <input name="name" id="carName" type="text" placeholder="نام ماشین (مثل پراید)" value="">

                    <!-- انتخاب دسته ماشین -->
                    <select name="car_type_id" id="carTypeSelect" class="category-select">
                        <option value="">انتخاب دسته خودرو</option>
                        @foreach ($carTypes as $carType)
                            <option value="{{ $carType->id }}">{{ $carType->title }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit">ذخیره و ثبت</button>
            </form>

          </div>
        </div>
        <!-- Add/Edit Car2 end -->
        <ul>
          <!-- Car item start -->
          @foreach ($cars as $car)
            <li class="admin-car-item">
              <div class="admin-car-item-title"><p>{{ $car->name }}</p>(<span>{{ $car->carType->title }}</span>)</div>
              <div class="admin-car-item-btn">
                <button id="adminEditCar2" data-id="{{ $car->id }}" data-title="{{ $car->name }}" data-car_type_id="{{ $car->carType->id }}">ویرایش</button>
                <form action="{{ route('admin.cars.delete') }}" method="POST" class="delete-car-form">
                    @csrf
                    <input type="hidden" name="car_id" value="{{ $car->id }}"> 
                    <button type="submit" id="adminDeleteCar2">حذف</button>
                </form>
              </div>
            </li>
          @endforeach
          <!-- Car item end -->
        </ul>
        <button id="adminAddCar2">افزودن ماشین</button>
      </div>
    </div>
  </div>
  <!-- Admin Car page end -->

  <script src="{{ asset('js/admin-profile-cars.js') }}"></script>
  <script>
    // اسکریپت ساده برای سوئیچ تب‌ها
    document.querySelectorAll('.admin-car-tab').forEach(tab => {
      tab.addEventListener('click', () => {
        // حذف active از همه
        document.querySelectorAll('.admin-car-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.admin-car-tab-content').forEach(c => c.classList.remove('active'));

        // فعال کردن تب کلیک شده
        tab.classList.add('active');
        const target = tab.getAttribute('data-tab');
        document.getElementById(target).classList.add('active');
      });
    });
        
    // Admin Profile Cars2
    document.querySelectorAll('#adminEditCar2').forEach(button => {
      button.addEventListener('click', () => {

        // گرفتن hidden input
        const hiddenInput = document.getElementById('car_id');

        // گرفتن data-id دکمه و وارد کردن در hidden input
        const carId = button.getAttribute('data-id') || "";
        hiddenInput.value = carId;

        // گرفتن سایر فیلدها از data attributes
        const name = button.getAttribute('data-title') || "";
        const carTypeId = button.getAttribute('data-car_type_id') || "";

        // قرار دادن مقادیر در input ها
        document.getElementById('carName').value = name;
        document.getElementById('carTypeSelect').value = carTypeId;

        // باز کردن پاپ‌آپ
        document.getElementById('adminAddEditCar2Popup').style.display = 'block';
      });
    });
    // open popup 2

    document.querySelectorAll('#adminAddCar2').forEach(button => {
      button.addEventListener('click', () => {
        // گرفتن المان های فرم
        const popup = document.getElementById('adminAddEditCar2Popup');
        const hiddenInput = document.getElementById('car_id');
        const carNameInput = document.getElementById('carName');
        const carTypeSelect = document.getElementById('carTypeSelect');

        // خالی کردن مقادیر فرم
        hiddenInput.value = "";
        carNameInput.value = "";
        carTypeSelect.value = ""; // انتخاب پیش‌فرض

        // باز کردن پاپ‌آپ
        popup.style.display = 'block';
      });
    });


    
    
    // close popup 2
    const adminAep2 = document.getElementById('adminAddEditCar2Popup');

    window.addEventListener('click', (e) => {
      if (e.target === adminAep2) {
        adminAep2.style.display = 'none';
      }
    });
  </script>
</body>
</html>