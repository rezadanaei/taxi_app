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
    
    <div class="state-page-content max-width driver-not-accepted">

  <img src="{{ asset('/img/state-notaccepted.svg') }}" alt="رد درخواست">
      @if ($user->status == 'rejected')
        <h1>درخواست شما رد شد!</h1>

        <p>راننده گرامی متاسفانه درخواست شما توسط تیم ما رد شده.</p>
        <p>علت رد شدن را در ادامه می توانید مشاهده کنید</p>
      @endif

      <div class="u-driver-not-accepted">
        <p>{{ $message }}</p>
      </div>

      <section class="state-buttons">
        <a href="{{ route('user.profile', ['slug' => 'retry']) }}" class="user-state-white-cancel">درخواست مجدد</a>
        <a href="#" class="user-state-white-contact">تماس با پشتیبانی</a>
      </section>

    </div>

   </div>
  <!-- Login page end -->
  
</body>
</html>