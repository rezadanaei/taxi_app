<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Page Title -->
  <title>ورود | عضویت</title>
  <link rel="shortcut icon" href="{{ asset('/img/fav.png') }}" type="image/x-icon">
  <!-- Page Style -->
  <style>
    :root {

        --main-color: {{ setting('colers_primary') }};
        --second-color: {{ setting('colers_secondary') }};
        --Third-color: {{ setting('colers_tertiary') }};
      }
  </style>
  <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
</head>
<body>

  <!-- Login page -->
   <div class="login-container">
    
    <div class="login-signup-container max-width">

      <form action="{{ route('login.verify') }}" method="POST" class="login">
        @csrf

        <div class="role-tabs">
          <input type="radio" id="passenger" name="role" value="passenger" checked>
          <label for="passenger">مسافر</label>

          <input type="radio" id="driver" name="role" value="driver">
          <label for="driver">راننده</label>
        </div>

        <img src="{{ asset('/img/fav.png') }}" alt="لوگو">
        <h1>ورود | عضویت</h1>
        <p>برای استفاده از امکانات و خدمات سایت شماره خود را وارد کنید.</p>

        <div class="tel-input">
          <input type="tel" name="phone" id="phone" placeholder="شماره موبایل" required>
          <input class="button" type="submit" value="تایید شماره">
        </div>
      </form>

      <p class="agree-policy">با ورود یا ثبت نام در سایت شما <a href="#">قوانین و مقررات</a> سایت را قبول می کنید.</p>
    </div>

   </div>
  <!-- Login page end -->
  
</body>
</html>
