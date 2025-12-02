<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Page Title -->
  <title>ورود ادمین</title>
  <link rel="shortcut icon" href="{{ asset('img/fav.png') }}" type="image/x-icon">
  <!-- Page Style -->
  <style>
    :root {

        --main-color: {{ setting('colers_primary') }};
        --second-color: {{ setting('colers_secondary') }};
        --Third-color: {{ setting('colers_tertiary') }};
      }
  </style>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

  <!-- Admin Login page -->
   <div class="admin-login-container">
    
    <div class="admin-login max-width">
      
  <img src="{{ asset('img/logo.png') }}" alt="لوگو">
      <h1>ورود ادمین</h1>
      <p>برای ورود نام کاربری و رمز عبور خود را وارد کنید</p>
      
      <form method="POST" action="{{ route('admin.login.verify') }}" class="admin-login-form">
            @csrf
            <input type="text" name="username" placeholder="نام کاربری" required>
            <input type="password" name="password" placeholder="رمز عبور" required>
            <input class="button" type="submit" value="ورود">
        </form>

        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        
    </div>
      
  <img class="admin-img-login" src="{{ asset('img/admin-login.png') }}" alt="ورود ادمین">

   </div>
  <!-- Admin Login page end -->
  
</body>
</html>