<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ادمین ها</title>
  <style>
        :root {
            --main-color: {{ setting('colers_primary') ?? '#1E90FF' }};
            --second-color: {{ setting('colers_secondary') ?? '#FF4081' }};
            --Third-color: {{ setting('colers_tertiary') ?? '#E0E0E0' }};
        }
    </style>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
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
  </style>
</head>

<body>

  <div class="admin-adm-container">
    
    <h1>ادمین ها</h1>

    <div class="admin-adm-content">
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
      <div id="adminAddEditadmPopup">
        <div class="admin-add-edit-adm-popup-content">
          <h3>ویرایش / افزودن ادمین</h3>
          <form action="{{ route('admin.admins.save') }}" method="POST">
            @csrf
            <input type="hidden" name="admin_id" id="admin_id" value="">

            <input type="text" name="name" id="adminName" placeholder="نام" value="">
            <input type="text" name="username" id="adminUsername" placeholder="نام کاربری" value="">
            <input type="password" name="password" id="adminPassword" placeholder="رمز عبور">
            <input type="text" name="phone" id="adminPhone" placeholder="شماره تماس" value="">
           
            <select name="type" id="adminType" class="category-select">
              <option value="">نوع ادمین</option>
              <option value="owner">دسترسی کامل</option>
              <option value="admin">ادمین ساده</option>
            </select>

            <button type="submit" class="button">ذخیره اطلاعات</button>

          </form>
        </div>
      </div>

      <ol>
      
        @foreach ($admins as $admin)  
          <li class="admin-adm-item">
            <div class="admin-adm-item-title"><p>{{ $admin->name }}</p></div>
            <div class="admin-adm-item-btn">
              <button id="adminEditadm" data-name="{{ $admin->name }}" data-username="{{ $admin->username }}" data-password="{{ $admin->password }}" data-phone="{{ $admin->phone }}" data-type="{{ $admin->type }}" data-id="{{ $admin->id }}"> ویرایش</button>
              <form action="{{ route('admin.admins.delete') }}" method="POST" style="display: inline-block" >
                @csrf
                <input type="hidden" name="admin_id" value="{{ $admin->id }}"> 
                <button type="submit" id="adminDeleteadm">حذف</button>
              </form>
            </div>
          </li>
        @endforeach

      </ol>
      
      <button id="adminAddADM">افزودن</button>

    </div>

   </div>

  <script src="{{ asset('js/admin-profile-adms.js') }}"></script>
  
</body>
</html>
