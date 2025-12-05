// Admin Profile drivers

document.querySelectorAll('#adminEditdriver').forEach(button => {
  button.addEventListener('click', () => {
    const baseUrl = window.location.origin; 

    function validImage(path) {
      if (!path || path === `${baseUrl}/storage` || path.trim() === '') {
        return `${baseUrl}/img/no-photo.png`;
      }
      return path.startsWith('http') ? path : `${baseUrl}${path}`;
    }

    const popup = document.getElementById('adminAddEditdriverPopup');
    popup.style.display = 'block';
    popup.querySelector('input[name="id"]').value = button.dataset.id || '';
    popup.querySelector('input[name="first_name"]').value = button.dataset.firstname || '';
    popup.querySelector('input[name="last_name"]').value = button.dataset.lastname || '';
    popup.querySelector('input[name="father_name"]').value = button.dataset.fathername || '';
    popup.querySelector('input[name="birth_date"]').value = button.dataset.birthdate || '';
    popup.querySelector('input[name="national_code"]').value = button.dataset.nationalcode || '';
    popup.querySelector('input[name="phone"]').value = button.dataset.phone || '';
    popup.querySelector('input[name="address"]').value = button.dataset.address || '';

    popup.querySelector('input[name="car_type"]').value = button.dataset.cartype || '';
    popup.querySelector('input[name="car_plate"]').value = button.dataset.carplate || '';
    popup.querySelector('input[name="license_number"]').value = button.dataset.licensenumber || '';
    popup.querySelector('input[name="car_model"]').value = button.dataset.carmodel || '';

    // Existing images
    popup.querySelector('input[name="id_card_front"]').dataset.current  = validImage(button.dataset.idcardfront);
    popup.querySelector('input[name="id_card_back"]').dataset.current = validImage(button.dataset.idcardback);
    popup.querySelector('input[name="id_card_selfie"]').dataset.current = validImage(button.dataset.idselfi);
    popup.querySelector('input[name="profile_photo"]').dataset.current = validImage(button.dataset.profilephoto);
    popup.querySelector('input[name="license_front"]').dataset.current = validImage(button.dataset.licensefront);
    popup.querySelector('input[name="license_back"]').dataset.current = validImage(button.dataset.licenseback);
    popup.querySelector('input[name="car_card_front"]').dataset.current = validImage(button.dataset.carcardfront);
    popup.querySelector('input[name="car_card_back"]').dataset.current = validImage(button.dataset.carcardback);
    popup.querySelector('input[name="car_insurance"]').dataset.current = validImage(button.dataset.carinsure);

    // ------------------ NEW EXTRA CAR IMAGES ------------------
    popup.querySelector('input[name="car_front_image"]').dataset.current = validImage(button.dataset.carfrontimage);
    popup.querySelector('input[name="car_back_image"]').dataset.current = validImage(button.dataset.carbackimage);
    popup.querySelector('input[name="car_left_image"]').dataset.current = validImage(button.dataset.carleftimage);
    popup.querySelector('input[name="car_right_image"]').dataset.current = validImage(button.dataset.carrightimage);
    popup.querySelector('input[name="car_front_seats_image"]').dataset.current = validImage(button.dataset.carfrontseatsimage);
    popup.querySelector('input[name="car_back_seats_image"]').dataset.current = validImage(button.dataset.carbackseatsimage);

    const toggleinput = document.getElementById('driverid');
    toggleinput.value = button.dataset.id;
  });
});



// close popup 
const adminAep = document.getElementById('adminAddEditdriverPopup');

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

// upload handler
document.querySelectorAll(".u-driver-grid-4 .file-upload").forEach(uploadBox => {
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

// Admin Profile drivers end