<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Page Title -->
  <title>در انتظار پرداخت</title>
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

      <img src="{{ asset('/img/state-accepted.svg') }}" alt="تایید سفر">
      <h1>سفر شما توسط راننده پذیرفته شد!</h1>

      <p>لطفا برای تکمیل نهایی پرداخت زیر را انجام دهید تا سفر برای شما ثبت شود.</p>

      <a href="#" class="user-state-pay-btn">پرداخت</a>

      <section class="state-buttons">
        <a href="#" class="user-state-white-cancel">لغو درخواست</a>
        <a href="tel:{{ setting('support_phone') }}" class="user-state-white-contact">تماس با پشتیبانی</a>
      </section>

    </div>

   </div>
  <!-- Login page end -->
  
</body>
</html>