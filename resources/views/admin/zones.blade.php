<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مناطق خاص</title>
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

  <div class="admin-zones-container">
    <h1>مناطق خاص</h1>
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

    <form action="{{ route('admin.zones.save') }}" method="POST" class="zones-admin"> 
      @csrf
      <input type="hidden" name="zone_id"class="zones-admin">
      <input type="text" name="latitude" id="latitude" placeholder="عرض جغرافیایی">
      <input type="text" name="longitude" id="longitude" placeholder="طول جغرافیایی">
      <input type="text" name="radius_km" id="radius_km" placeholder="شعاع ( کیلومتر )">
      <button type="submit">افزودن و ذخیره اطلاعات</button>
    </form>

    <ol class="zones-list">
    @foreach ($zones as $zone)
        <li>
            عرض جغرافیایی: {{ $zone->latitude }}
            طول جغرافیایی: {{ $zone->longitude }}
            شعاع: {{ $zone->radius_km }} کیلومتر
            <form action="{{ route('admin.zones.delete') }}" method="POST" style="display: inline-block"> 
              @csrf
              <input type="hidden" name="zone_id" value="{{ $zone->id }}">
              <button type="submit" class="list-zone-delete">حذف</button>
            </form>
        </li>
    @endforeach
</ol>


  </div>
  
</body>
</html>
