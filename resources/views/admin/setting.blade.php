<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تنظیمات</title>
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

  <div class="admin-setting-container">
    <h1>تنظیمات</h1>
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
    <form action="{{ route('admin.settings.save') }}" method="POST" enctype="multipart/form-data"  class="admin-settings" >
      @csrf
      <div class="admin-settings-base">
        <input type="text" name="site_name" id="site_name" placeholder="اسم سایت" value="{{ setting('site_name') }}">
        <input type="text" name="support_phone" id="support_phone" placeholder="شماره تماس پشتیبانی سایت" value="{{ setting('support_phone') }}">
        <input type="text" name="merchant_id" id="merchant_id"  placeholder="مرچنت کد درگاه پرداخت" value="{{ setting('merchant_id') }}">
        <input type="text" name="sms_panel_number" id="sms_panel_number" placeholder="شماره پنل پیامکی" value="{{ setting('sms_panel_number') }}">
        <input type="text" name="sms_panel_username" id="sms_panel_username" placeholder="نام کاربری پنل پیامکی" value="{{ setting('sms_panel_username') }}">
        <input type="text" name="sms_panel_password" id="sms_panel_password" placeholder="رمز عبور سامانه پیامکی" value="{{ setting('sms_panel_password') }}">
        <input type="text" name="nashan_web_key" id="nashan_web_key" placeholder="کلید وب نشان" value="{{ setting('nashan_web_key') }}">
        <input type="text" name="nashan_service_key" id="nashan_service_key" placeholder="کلید سرویس نشان" value="{{ setting('nashan_service_key') }}">
      </div>

      <div class="admin-settings-colors">
        <div class="color-pick">
          <input type="color" name="colers_primary" id="colers_primary" value="{{ setting('colers_primary') }}">
          <p>رنگ اول</p>
        </div>

        <div class="color-pick">
          <input type="color" name="colers_secondary" id="colers_secondary" value="{{ setting('colers_secondary') }}">
          <p>رنگ دوم</p>
        </div>

        <div class="color-pick">
          <input type="color" name="colers_tertiary" id="colers_tertiary" value="{{ setting('colers_tertiary') }}" >
          <p>رنگ سوم</p>
        </div>
      </div>

      <button type="submit" class="button">ثبت و ذخیره</button>
    </form>

  </div>

</body>
</html>
