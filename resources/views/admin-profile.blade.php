<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>پنل ادمین</title>
  <link rel="shortcut icon" href="{{ asset('img/fav.png') }}" type="image/x-icon">
  <style>
    :root {

        --main-color: {{ setting('colers_primary') }};
        --second-color: {{ setting('colers_secondary') }};
        --Third-color: {{ setting('colers_tertiary') }};
      }
  </style>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  
  <style>
    body {
      margin: 0;
      display: flex;
      height: 100vh;
    }
    
    iframe {
      width: 100%;
      height: 100%;
      border: none;
    }

    .logout-link {
      cursor: pointer;
      color: #007bff;
      text-decoration: none;
    }
    .logout-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  
  <div class="admin-md-header">
  <button class="admin-hamburger" onclick="toggleMenu()"><img src="{{ asset('img/bars-staggered.svg') }}" alt="منو"></button>
    <section>
      <h2>{{ auth()->guard('admin')->user()->name }}</h2>
      
      <a class="logout-link" onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
        خروج
      </a>
      <form id="logout-form-header" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
          @csrf
      </form>

    </section>
  </div>

  <div class="admin-sidebar" id="adminSidebar">
  <div class="admin-side-title" style="background-image: url('{{ asset('img/admin-bg.png') }}');">
      <h2>{{ auth()->guard('admin')->user()->name }}</h2>
      
      <a class="logout-link" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
        خروج
      </a>
      <form id="logout-form-sidebar" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
          @csrf
      </form>

    </div>
    <ul id="menuList">
      <li onclick="loadPage('dashboard', this)" class="active">
        <img src="{{ asset('img/dashboard.svg') }}" alt="داشبورد"> داشبورد
      </li>
      <li onclick="loadPage('travels', this)">
        <img src="{{ asset('img/travels.svg') }}" alt="سفرها"> سفر ها
      </li>
      <li onclick="loadPage('pricing', this)">
        <img src="{{ asset('img/pricing.svg') }}" alt="تعرفه"> تعرفه بندی ها
      </li>
      <li onclick="loadPage('cars', this)">
        <img src="{{ asset('img/cars.svg') }}" alt="خودرو"> خودرو ها
      </li>
      <li onclick="loadPage('users', this)">
        <img src="{{ asset('img/users.svg') }}" alt="کاربران"> مدیریت کاربران
      </li>
      <li onclick="loadPage('drivers', this)">
        <img src="{{ asset('img/drivers.svg') }}" alt="رانندگان"> مدیریت رانندگان
      </li>
      <li onclick="loadPage('driver-docs', this)">
        <img src="{{ asset('img/driver-docs.svg') }}" alt="مدارک"> بررسی مدارک رانندگان
      </li>
      <li onclick="loadPage('driver-reports', this)">
        <img src="{{ asset('img/driver-reports.svg') }}" alt="گزارش"> گزارش رانندگان
      </li>
      <li onclick="loadPage('zones', this)">
        <img src="{{ asset('img/zones.svg') }}" alt="مناطق"> مناطق خاص
      </li>
      <li onclick="loadPage('admins', this)">
        <img src="{{ asset('img/admins.svg') }}" alt="ادمین"> ادمین ها
      </li>
      <li onclick="loadPage('notifications', this)">
        <img src="{{ asset('img/notifications.svg') }}" alt="اعلانات"> اعلانات
      </li>
      <li onclick="loadPage('setting', this)">
        <img src="{{ asset('img/setting.svg') }}" alt="تنظیمات"> تنظیمات
      </li>
    </ul>
  </div>

  <div class="admin-content">

    <div id="loadingOverlay">
      <div class="spinner"></div>
      <p>در حال بارگذاری...</p>
    </div>
    
    <iframe id="admin-contentFrame" src="{{ route('admin.page', ['slug' => 'dashboard']) }}"></iframe>
  </div>
  <script>
// Admin Profile - JavaScript Logic


const adminPageRoute = '{{ route('admin.page', ['slug' => '__SLUG__'], false) }}';
const defaultPageSlug = 'dashboard';

function loadPage(pageSlug, el) { 
  let iframe = document.getElementById('admin-contentFrame');
  let loader = document.getElementById('loadingOverlay');
  
  loader.style.display = "flex";

  iframe.src = adminPageRoute.replace('__SLUG__', pageSlug);

  let items = document.querySelectorAll('#menuList li');
  items.forEach(item => item.classList.remove('active'));

  el.classList.add('active');

  const newUrl = new URL(window.location);
  newUrl.searchParams.set('tab', pageSlug); 
  window.history.pushState({}, '', newUrl);

  if (window.innerWidth <= 1024) {
    toggleMenu();
  }
}

document.getElementById('admin-contentFrame').addEventListener('load', function () {
  let iframe = this;
  let loader = document.getElementById('loadingOverlay');

  loader.style.display = "none";

  try {
    let pageTitle = iframe.contentDocument.title; 
    if (pageTitle) {
      document.title = "پنل ادمین | " + pageTitle;
    } else {
      document.title = "پنل ادمین";
    }
  } catch (e) {
    console.warn("امکان خواندن تایتل صفحه iframe وجود ندارد (Cross-Origin).");
    document.title = "پنل ادمین";
  }
});

window.addEventListener('DOMContentLoaded', () => {
  let urlParams = new URLSearchParams(window.location.search);
  let tabFromUrl = urlParams.get('tab') || defaultPageSlug;
  
  let iframe = document.getElementById('admin-contentFrame');
  let loader = document.getElementById('loadingOverlay');

  loader.style.display = "flex";

  iframe.src = adminPageRoute.replace('__SLUG__', tabFromUrl);

  let items = document.querySelectorAll('#menuList li');
  items.forEach(item => {
    if (item.getAttribute('onclick').includes("'" + tabFromUrl + "'")) {
      item.classList.add('active');
    } else {
      item.classList.remove('active');
    }
  });
});

function toggleMenu() {
  document.getElementById('adminSidebar').classList.toggle('open');
}

window.addEventListener('popstate', () => {
  let urlParams = new URLSearchParams(window.location.search);
  let tabFromUrl = urlParams.get('tab') || defaultPageSlug;
  
  let iframe = document.getElementById('admin-contentFrame');
  let loader = document.getElementById('loadingOverlay');

  loader.style.display = "flex";
  iframe.src = adminPageRoute.replace('__SLUG__', tabFromUrl);

  let items = document.querySelectorAll('#menuList li');
  items.forEach(item => {
    if (item.getAttribute('onclick').includes("'" + tabFromUrl + "'")) {
      item.classList.add('active');
    } else {
      item.classList.remove('active');
    }
  });
});
  </script>
  
  </body>
</html>
