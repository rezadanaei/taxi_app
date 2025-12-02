// Admin Profile

// لود صفحه
function loadPage(page, el) {
  let iframe = document.getElementById('admin-contentFrame');
  let loader = document.getElementById('loadingOverlay');
  
  // نمایش لودینگ
  loader.style.display = "flex";

  iframe.src = '/admin/' + page;

  // حذف active از همه
  let items = document.querySelectorAll('#menuList li');
  items.forEach(item => item.classList.remove('active'));

  // فعال کردن آیتم کلیک شده
  el.classList.add('active');

  // به‌روزرسانی URL با پارامتر تب
  const newUrl = new URL(window.location);
  newUrl.searchParams.set('tab', page);
  window.history.pushState({}, '', newUrl);

  if (window.innerWidth <= 1024) {
    toggleMenu();
  }
}

// تغییر عنوان براساس iframe
document.getElementById('admin-contentFrame').addEventListener('load', function () {
  let iframe = this;
  let loader = document.getElementById('loadingOverlay');

  // مخفی کردن لودینگ
  loader.style.display = "none";

  try {
    let pageTitle = iframe.contentDocument.title; // گرفتن تایتل صفحه داخل iframe
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

// وقتی صفحه لود شد، تب را از URL بگیر
window.addEventListener('DOMContentLoaded', () => {
  let urlParams = new URLSearchParams(window.location.search);
  let tabFromUrl = urlParams.get('tab') || 'dashboard.html';
  
  let iframe = document.getElementById('admin-contentFrame');
  let loader = document.getElementById('loadingOverlay');

  // نمایش لودینگ هنگام شروع
  loader.style.display = "flex";

  iframe.src = '/admin/' + tabFromUrl;

  // پیدا کردن آیتم منو و فعال کردنش
  let items = document.querySelectorAll('#menuList li');
  items.forEach(item => {
    if (item.getAttribute('onclick').includes(tabFromUrl)) {
      item.classList.add('active');
    } else {
      item.classList.remove('active');
    }
  });
});

function toggleMenu() {
  document.getElementById('adminSidebar').classList.toggle('open');
}

// مدیریت تغییر در تاریخچه مرورگر (برای دکمه‌های back/forward)
window.addEventListener('popstate', () => {
  let urlParams = new URLSearchParams(window.location.search);
  let tabFromUrl = urlParams.get('tab') || 'dashboard.html';
  
  let iframe = document.getElementById('admin-contentFrame');
  let loader = document.getElementById('loadingOverlay');

  loader.style.display = "flex";
  iframe.src = '/admin/' + tabFromUrl;

  let items = document.querySelectorAll('#menuList li');
  items.forEach(item => {
    if (item.getAttribute('onclick').includes(tabFromUrl)) {
      item.classList.add('active');
    } else {
      item.classList.remove('active');
    }
  });
});

// Admin Profile end