// Admin Profile

function loadPage(page, el) {
  let iframe = document.getElementById('admin-contentFrame');
  let loader = document.getElementById('loadingOverlay');
  
  loader.style.display = "flex";

  iframe.src = '/admin/' + page;

  let items = document.querySelectorAll('#menuList li');
  items.forEach(item => item.classList.remove('active'));

  el.classList.add('active');

  const newUrl = new URL(window.location);
  newUrl.searchParams.set('tab', page);
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

function toggleMenu() {
  document.getElementById('adminSidebar').classList.toggle('open');
}

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