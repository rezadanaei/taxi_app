<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مدیریت کاربران</title>
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

  <div class="admin-user-container">
    
    <h1>مدیریت کاربران</h1>
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
    <div class="admin-user-content">

      <div id="adminAddEdituserPopup">
        <div class="admin-add-edit-user-popup-content">
          <h3>ویرایش کاربر</h3>
          <form action="{{ route('admin.users.save') }}" method="POST">
            @csrf
            <input type="hidden" name="id" id="id" >
            <input type="text" name="name" id="name" placeholder="نام کاربر">
            <input type="text" name="phone" id="phone" placeholder="شماره تماس">
            <input type="text" name="national_code" id="national_code" placeholder="شماره ملی">
            <input type="text" name="birth_date" id="birth_date" placeholder="تاریخ تولد">

            <button type="submit" class="button">ذخیره اطلاعات</button>

          </form>
        </div>
      </div>

      <ol>
        @foreach ($users as $user)
        <li class="admin-user-item">
          <div class="admin-user-item-title"><p>{{ $user->phone }}</p></div>
          <div class="admin-user-item-btn"> 
            <button 
              id="adminEdituser" 
              data-id="{{ $user->id }}" 
              data-name="{{ $user->userable->name ?? '' }}" 
              data-phone="{{ $user->phone }}" 
              data-national_code="{{ $user->userable->national_code ?? '' }}" 
              data-birth_date="{{ $user->userable->birth_date ?? '' }}"
            >
              ویرایش
            </button>
            <form action="{{ route('admin.users.delete') }}" method="POST" class="delete-user-form" style="display: inline-block">
              @csrf
              <input type="hidden" name="id" value="{{ $user->id }}">
              <button id="adminDeleteuser">حذف</button>
            </form>
              
          </div>
        </li>
        @endforeach
      </ol>
      {{ $users->links() }}

    </div>

   </div>

  <script src="{{ asset('js/admin-profile-users.js') }}"></script>
  
</body>
</html>
