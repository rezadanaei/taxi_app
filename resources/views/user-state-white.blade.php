<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Page Title -->
  <title>در انتظار بررسی</title>
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
   <div class="state-container">
    
    <div class="state-page-content max-width">

      <img src="{{ asset('/img/state-white.svg') }}" alt="منتظر بمانید">
      <h1>لطفا منتظر بمانید</h1>

      <p>درخواست شما در انتظار تایید راننده است، لطفا تا زمان تایید منتظر بمانید. در صورت تایید سفر پیامی برای شما ارسال خواهد شد.</p>

      <section class="state-buttons">
        <a href="#" class="user-state-white-cancel">لغو درخواست</a>
        <a href="tel:{{ setting('support_phone') }}" class="user-state-white-contact">تماس با پشتیبانی</a>
      </section>

    </div>

   </div>
  <!-- Login page end -->
  
</body>
</html>