// Admin Profile users

document.querySelectorAll('#adminEdituser').forEach(button => {
  button.addEventListener('click', () => {
    const hiddenInput = document.getElementById('id');

    const userId = button.getAttribute('data-id') || "";
    const name = button.getAttribute('data-name') || "";
    const phone = button.getAttribute('data-phone') || "";
    const nationalCode = button.getAttribute('data-national_code') || "";
    const birthDate = button.getAttribute('data-birth_date') || "";

    hiddenInput.value = userId;
    document.getElementById('name').value = name;
    document.getElementById('phone').value = phone;
    document.getElementById('national_code').value = nationalCode;
    document.getElementById('birth_date').value = birthDate;

    document.getElementById('adminAddEdituserPopup').style.display = 'block';
  });
});

document.querySelectorAll('#adminAdduser').forEach(button => {
  button.addEventListener('click', () => {
    const popup = document.getElementById('adminAddEdituserPopup');
    const hiddenInput = document.getElementById('id');
    const nameInput = document.getElementById('name');
    const phoneInput = document.getElementById('phone');
    const nationalInput = document.getElementById('national_code');
    const birthInput = document.getElementById('birth_date');

    hiddenInput.value = "";
    nameInput.value = "";
    phoneInput.value = "";
    nationalInput.value = "";
    birthInput.value = "";

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