<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>گزارش رانندگان</title>
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
  <div class="admin-driverdock-container">
    <h1>گزارش رانندگان</h1>
    
    <div class="filter-container">
      <select id="sortFilter">
        <option value="totalTripsDesc">مرتب‌سازی: بیشترین کل سفرها</option>
        <option value="totalTripsAsc">مرتب‌سازی: کمترین کل سفرها</option>
        <option value="acceptedTripsDesc">مرتب‌سازی: بیشترین سفرهای تأیید شده</option>
        <option value="acceptedTripsAsc">مرتب‌سازی: کمترین سفرهای تأیید شده</option>
        <option value="rejectedTripsDesc">مرتب‌سازی: بیشترین سفرهای رد شده</option>
        <option value="rejectedTripsAsc">مرتب‌سازی: کمترین سفرهای رد شده</option>
      </select>
    </div>

    <table class="driver-reports-table">
      <thead>
        <tr>
          <th>ردیف</th>
          <th>اسم راننده</th>
          <th>کل سفرها</th>
          <th>سفرهای تأیید شده</th>
          <th>سفرهای رد شده</th>
        </tr>
      </thead>
      <tbody id="driversTableBody">
        <tr>
          <td>1</td>
          <td>علی محمدی</td>
          <td class="trip-detail" data-driver="علی محمدی">10</td>
          <td class="trip-detail" data-driver="علی محمدی">8</td>
          <td class="trip-detail" data-driver="علی محمدی">2</td>
        </tr>
      </tbody>
    </table>

    <div id="tripModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h2>اسم راننده</h2>
        
        <div class="admin-travels-content">
          <p class="atc-all-trip">همه سفر ها</p>
          <div class="passenger-current-trip">
            <ul>
              <li>
                <div class="passenger-trip-item">
                  <div class="passenger-item-title"><div class="trip-id">کد سفر: 1285</div> <div class="trip-state">تکمیل شده</div> </div>
                  <section>
                    <img src="{{ asset('img/down.svg') }}" alt="فلش">
                  </section>
                </div>
                <div class="passenger-trip-content">
                  <div class="trip-extra-info-ad">
                    <div class="trip-date">تاریخ: 12 مرداد 1404</div><span>-</span>
                    <div class="trip-time">ساعت: 22:16</div><span>-</span>
                    <div class="trip-time">تعداد مسافر: 1</div><span>-</span>
                    <div class="trip-time">تعداد چمدان: 2</div><span>-</span>
                    <div class="trip-time">نوع سفر: یکطرفه</div><span>-</span>
                    <div class="trip-time">ساعات انتظار: 0</div><span>-</span>
                    <div class="trip-time">حیوان خانگی: ندارد</div>
                  </div>
                  <div class="trip-extra-info-ad2">
                    <div class="trip-date">شناسه پرداخت: 12354845</div><span>-</span>
                    <div class="trip-time">هزینه سفر: 20000 تومان</div><span>-</span>
                    <div class="trip-time">وضعیت: تکمیل شده</div>
                  </div>

                  <div class="trip-driver-info">
                    <img src="{{ asset('img/no-photo.png') }}" alt="تصویر راننده">
                    <div class="driver-info">
                      <p><span>راننده: </span>اسم راننده</p>
                      <p><span>ماشین: </span> مدل ماشین</p>
                      <p><span>پلاک: </span>21 ب 341 ایران 99</p>
                    </div>
                    <a href="tel:09123456789" class="call-to-driver">09123456789</a>
                  </div>
                  
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

  </div>
  
  <script src="{{ asset('js/admin-profile-driver-reports.js') }}"></script>

</body>
</html>
