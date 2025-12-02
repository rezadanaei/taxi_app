<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تعرفه بندی ها</title>
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

  <div class="admin-pricing-container">
    
    <h1>تعرفه بندی ها</h1>

    <div class="u-profile-tab-item admin-pricing-content">
      @if (session('error'))
          <div class="admin-errors">
              {{ session('error') }}
          </div>
      @endif

      @if (session('success'))
          <div class="admin-success">
              {{ session('success') }}
          </div>
      @endif
      <form action="{{ route('admin.pricing.update') }}" method="POST">
        @csrf
        <section>
          <input type="number" name="commission" id="commission" value="{{tariff('commission')}}" placeholder="درصد کمیسیون سایت">
          <input type="number" name="area_coef" id="area_coef" value="{{ tariff('area_coef') }}" placeholder="ضریب مناطق خاص">
          <input type="number" name="waiting_fee" id="waiting_fee" value="{{ tariff('waiting_fee') }}"placeholder="هزینه هر ساعت انتظار">
        </section>

        <button class="button" type="submit">ذخیره اطلاعات</button>
      </form>

    </div>

   </div>
  
</body>
</html>
