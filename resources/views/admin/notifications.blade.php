<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>اعلانات</title>
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

  <div class="admin-notifications-container">
    <h1>اعلانات</h1>

    <button id="notificationsAllDelete">حذف همه</button>

    <ul class="notifications-list">
      <li><img src="{{ asset('img/notifications.svg') }}"> <p>اعلان ها در این قسمت نشان داده می شوند</p></li>
      <li><img src="{{ asset('img/notifications.svg') }}"> <p>سفر شماره 15384 یک ساعت دیگر زمان دارد و هیچ راننده ای قبول نکرده است!</p></li>

    </ul>

  </div>

</body>
</html>
