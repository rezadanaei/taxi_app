<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Page Title -->
  <title>در انتظار بررسی</title>
  <link rel="shortcut icon" href="{{ asset('/img/fav.png') }}" type="image/x-icon">
  <style>
    :root {

        --main-color: {{ setting('colers_primary') }};
        --second-color: {{ setting('colers_secondary') }};
        --Third-color: {{ setting('colers_tertiary') }};
      }
  </style>
  <!-- Page Style -->
  <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
</head>
<body>

  <!-- Login page -->
   <div class="state-container">
    
    <div class="state-page-content max-width driver-in-prossese">

  <img src="{{ asset('/img/driver-in-prossese.png') }}" alt="منتظر بمانید">
      <h1>لطفا منتظر بمانید...</h1>

      <p>درخواست شما توسط تیم ما در حال بررسی است.</p>
      <p>نتیجه از طریق پیامک اطلاع رسانی می گردد.</p>

    </div>

   </div>
  <!-- Login page end -->
  
</body>
</html>
