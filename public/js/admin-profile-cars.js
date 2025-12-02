// Admin Profile Cars
document.querySelectorAll('#adminEditCar').forEach(button => {
  button.addEventListener('click', () => {

    const hiddenInput = document.getElementById('carType_id');

    const carId = button.getAttribute('data-id');
    hiddenInput.value = carId || "";

    const title = button.getAttribute('data-title') || "";
    const description = button.getAttribute('data-desc') || "";
    const pricePerKm = button.getAttribute('data-price_per_km') || "";
    const headerImage = button.getAttribute('data-header_image') || "";

    document.getElementById('carTypeTitle').value = title;
    document.getElementById('carTypeDesc').value = description;
    document.getElementById('carTypePrice').value = pricePerKm;

    // document.getElementById('carTypePreview').src = headerImage;

    document.getElementById('adminAddEditCarPopup').style.display = 'block';
  });
});


document.querySelectorAll('#adminAddCar').forEach(button => {
  button.addEventListener('click', () => {
    document.getElementById('adminAddEditCarPopup').style.display = 'block';

    document.getElementById('carType_id').value = "";
    document.getElementById('carTypeTitle').value = "";
    document.getElementById('carTypeDesc').value = "";
    document.getElementById('carTypePrice').value = "";
    document.getElementById('carTypeImage').value = ""; 

    // document.getElementById('carTypePreview').src = "";
  });
});



// Delete car button event
document.querySelectorAll('#adminDeleteCar').forEach(button => {
  button.addEventListener('click', () => {
    const carItem = button.closest('.admin-car-item');
    if (carItem) {
      carItem.remove();
    }
  });
});

// close popup 
const adminAep = document.getElementById('adminAddEditCarPopup');

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


// Admin Profile Cars end