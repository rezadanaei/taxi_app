// Admin Profile users

// دکمه‌های ویرایش کاربر
document.querySelectorAll('#adminEdituser').forEach(button => {
  button.addEventListener('click', () => {
    // گرفتن hidden input
    const hiddenInput = document.getElementById('id');

    // گرفتن مقادیر از data-attributes
    const userId = button.getAttribute('data-id') || "";
    const name = button.getAttribute('data-name') || "";
    const phone = button.getAttribute('data-phone') || "";
    const nationalCode = button.getAttribute('data-national_code') || "";
    const birthDate = button.getAttribute('data-birth_date') || "";

    // قرار دادن مقادیر در input ها
    hiddenInput.value = userId;
    document.getElementById('name').value = name;
    document.getElementById('phone').value = phone;
    document.getElementById('national_code').value = nationalCode;
    document.getElementById('birth_date').value = birthDate;

    // باز کردن پاپ‌آپ
    document.getElementById('adminAddEdituserPopup').style.display = 'block';
  });
});

// دکمه‌های افزودن کاربر
document.querySelectorAll('#adminAdduser').forEach(button => {
  button.addEventListener('click', () => {
    // گرفتن المان‌های فرم
    const popup = document.getElementById('adminAddEdituserPopup');
    const hiddenInput = document.getElementById('id');
    const nameInput = document.getElementById('name');
    const phoneInput = document.getElementById('phone');
    const nationalInput = document.getElementById('national_code');
    const birthInput = document.getElementById('birth_date');

    // خالی کردن مقادیر فرم برای افزودن کاربر جدید
    hiddenInput.value = "";
    nameInput.value = "";
    phoneInput.value = "";
    nationalInput.value = "";
    birthInput.value = "";

    // باز کردن پاپ‌آپ
    popup.style.display = 'block';
  });
});




// close popup 
const adminAep = document.getElementById('adminAddEdituserPopup');

window.addEventListener('click', (e) => {
  if (e.target === adminAep) {
    adminAep.style.display = 'none';
  }
});


// upload handler
document.querySelectorAll(".u-driver-grid-2 .file-upload").forEach(uploadBox => {
  const realFile = uploadBox.querySelector("input[type=file]");
  const customBtn = uploadBox.querySelector(".file-button");

  customBtn.addEventListener("click", () => {
    realFile.click();
  });

  realFile.addEventListener("change", () => {
    if (realFile.files.length > 0) {
      customBtn.classList.add("file-selected");
    } else {
      customBtn.classList.remove("file-selected");
    }
  });
});


// Admin Profile users end