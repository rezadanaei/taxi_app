<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Page Title -->
  <title>رد درخواست</title>
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

      <img src="{{ asset('/img/state-notaccepted.svg') }}" alt="رد درخواست">
      <h1>سفر شما تایید نشد!</h1>

      <p>متاسفانه سفر شما توسط هیچ راننده ای قبول نشده!</p>

      <section class="state-buttons">
        <a href="{{ route('home')}}" class="user-state-white-cancel">صفحه اصلی</a>
        <a href="tel:{{ setting('support_phone') }}" class="user-state-white-contact">تماس با پشتیبانی</a>
      </section>

    </div>

   </div>
  <!-- Login page end -->
  
</body>
</html>