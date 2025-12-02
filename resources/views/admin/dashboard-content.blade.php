<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>داشبورد</title>
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

  <div class="admin-dashboard-container">
    
    <h1>داشبورد</h1>

    <div class="admin-dashboard-content">

      <div class="dashboard-item-type1">
        <p>تعداد رانندگان</p>
        <h3>{{ $driversCount }}</h3>
      </div>
      <div class="dashboard-item-type1">
        <p>تعداد مسافران</p>
        <h3>{{ $passengersCount }}</h3>
      </div>
      
      <div class="dashboard-item-type2">
        <p>تعداد کل سفر ها</p>
        <h3>{{ $tripsCount }}</h3>
      </div>
      <div class="dashboard-item-type2">
        <p>تعداد سفر های جاری</p>
        <h3>{{ $tripsOngoingCount }}</h3>
      </div>

    </div>

   </div>
  
</body>
</html>
