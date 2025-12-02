// Admin Profile adms
document.querySelectorAll('#adminEditadm').forEach(button => {
  button.addEventListener('click', () => {
    const popup = document.getElementById('adminAddEditadmPopup');
    const adminIdInput = document.getElementById('admin_id');
    const nameInput = document.getElementById('adminName');
    const usernameInput = document.getElementById('adminUsername');
    const phoneInput = document.getElementById('adminPhone');
    const passwordInput = document.getElementById('adminPassword');
    const typeSelect = document.getElementById('adminType');

    const adminId = button.getAttribute('data-id') || "";
    const name = button.getAttribute('data-name') || "";
    const username = button.getAttribute('data-username') || "";
    const phone = button.getAttribute('data-phone') || "";
    const type = button.getAttribute('data-type') || "";

    adminIdInput.value = adminId;
    nameInput.value = name;
    usernameInput.value = username;
    phoneInput.value = phone;
    passwordInput.value = ""; 
    typeSelect.value = type;

    popup.style.display = 'block';
  });
});

document.querySelectorAll('#adminAddADM').forEach(button => {
  button.addEventListener('click', () => {
    const popup = document.getElementById('adminAddEditadmPopup');
    const adminIdInput = document.getElementById('admin_id');
    const nameInput = document.getElementById('adminName');
    const usernameInput = document.getElementById('adminUsername');
    const phoneInput = document.getElementById('adminPhone');
    const passwordInput = document.getElementById('adminPassword');
    const typeSelect = document.getElementById('adminType');

    adminIdInput.value = "";
    nameInput.value = "";
    usernameInput.value = "";
    phoneInput.value = "";
    passwordInput.value = "";
    typeSelect.value = "";

    popup.style.display = 'block';
  });
})


// close popup 
const adminAep = document.getElementById('adminAddEditadmPopup');

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


// Admin Profile adms end