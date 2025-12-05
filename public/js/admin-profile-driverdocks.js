// Admin Profile driverdocks

// document.querySelectorAll('#adminSeeDriverDoc').forEach(button => {
//   button.addEventListener('click', async () => {
//     document.getElementById('adminAddSeedriverdockPopup').style.display = 'block';
//   });
// });

document.addEventListener('DOMContentLoaded', () => {
  const baseUrl = window.location.origin; 
  function validImage(path) {
    if (!path || path === `${baseUrl}/storage` || path.trim() === '') {
      return `${baseUrl}/img/no-photo.png`;
    }
    return path.startsWith('http') ? path : `${baseUrl}${path}`;
  }

  document.querySelectorAll('#adminSeeDriverDoc').forEach(button => {
    button.addEventListener('click', () => {

      const popup = document.getElementById('adminAddSeedriverdockPopup');
      popup.style.display = 'block';

      document.getElementById('popupFirstName').textContent = button.dataset.firstname || '-';
      document.getElementById('popupLastName').textContent = button.dataset.lastname || '-';
      document.getElementById('popupFatherName').textContent = button.dataset.fathername || '-';
      document.getElementById('popupBirthDate').textContent = button.dataset.birthdate || '-';
      document.getElementById('popupNationalCode').textContent = button.dataset.nationalcode || '-';
      document.getElementById('popupPhone').textContent = button.dataset.phone || '-';
      document.getElementById('popupAddress').textContent = button.dataset.address || '-';
      document.getElementById('popupCarType').textContent = button.dataset.cartype || '-';
      document.getElementById('popupCarPlate').textContent = button.dataset.carplate || '-';
      document.getElementById('popupLicenseNumber').textContent = button.dataset.licensenumber || '-';
      document.getElementById('popupCarModel').textContent = button.dataset.carmodel || '-';
      document.getElementById('note_id').value = button.dataset.noteid || '-';

      document.getElementById('popupIdCardFront').src = validImage(button.dataset.idcardfront);
      document.getElementById('popupIdCardBack').src = validImage(button.dataset.idcardback);
      document.getElementById('popupIdSelfi').src = validImage(button.dataset.idselfi);
      document.getElementById('popupProfilePhoto').src = validImage(button.dataset.profilephoto);
      document.getElementById('popupLicenseFront').src = validImage(button.dataset.licensefront);
      document.getElementById('popupLicenseBack').src = validImage(button.dataset.licenseback);
      document.getElementById('popupCarCardFront').src = validImage(button.dataset.carcardfront);
      document.getElementById('popupCarCardBack').src = validImage(button.dataset.carcardback);
      document.getElementById('popupCarInsure').src = validImage(button.dataset.carinsure);

      // تصاویر جدید خودرو
      document.getElementById('popupCarFrontImage').src = validImage(button.dataset.carfrontimage);
      document.getElementById('popupCarBackImage').src = validImage(button.dataset.carbackimage);
      document.getElementById('popupCarLeftImage').src = validImage(button.dataset.carleftimage);
      document.getElementById('popupCarRightImage').src = validImage(button.dataset.carrightimage);
      document.getElementById('popupCarFrontSeatsImage').src = validImage(button.dataset.carfrontseatsimage);
      document.getElementById('popupCarBackSeatsImage').src = validImage(button.dataset.carbackseatsimage);
    });
  });

  document.getElementById('adminAddSeedriverdockPopupClose').addEventListener('click', () => {
    document.getElementById('adminAddSeedriverdockPopup').style.display = 'none';
  });
});






// close popup 
const adminAep = document.getElementById('adminAddSeedriverdockPopup');

window.addEventListener('click', (e) => {
  if (e.target === adminAep) {
    adminAep.style.display = 'none';
  }
});

document.querySelectorAll('#adminAddSeedriverdockPopupClose').forEach(button => {
  button.addEventListener('click', async () => {
    document.getElementById('adminAddSeedriverdockPopup').style.display = 'none';
  });
});


document.querySelectorAll('#adminDeletedriverdockDoc').forEach(button => {
  button.addEventListener('click', () => {
    const popup = document.getElementById('adminAddDeletedriverdockPopup');
    popup.style.display = 'block';

    const hiddenInput = popup.querySelector('input[name="note_id"]');
    hiddenInput.value = button.dataset.noteid || '';
  });
});


// close popup 
const adminAepDelete = document.getElementById('adminAddDeletedriverdockPopup');

window.addEventListener('click', (e) => {
  if (e.target === adminAepDelete) {
    adminAepDelete.style.display = 'none';
  }
});

document.querySelectorAll('#adminAddDeletedriverdockPopupClose').forEach(button => {
  button.addEventListener('click', async () => {
    document.getElementById('adminAddDeletedriverdockPopup').style.display = 'none';
  });
});

document.addEventListener("DOMContentLoaded", function () {
    const images = document.querySelectorAll(".images img");

    images.forEach(img => {
      img.addEventListener("click", function () {
        window.open(this.src, "_blank");
      });
    });
  });

// Admin Profile driverdocks end