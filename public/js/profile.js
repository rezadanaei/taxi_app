// ==========================
// User Current Trip (با Event Delegation)
// ==========================
document.addEventListener('click', function(e) {
    const item = e.target.closest('.passenger-trip-item');
    if (!item) return;

    const content = item.nextElementSibling;

    document.querySelectorAll(".passenger-trip-content").forEach(c => {
        if (c !== content) c.classList.remove("active");
    });
    document.querySelectorAll(".passenger-trip-item").forEach(i => {
        if (i !== item) i.classList.remove("active");
    });

    content.classList.toggle("active");
    item.classList.toggle("active");
});

// ==========================
// Tabs
// ==========================
const tabs = document.querySelectorAll('.u-profile-tabs > div');
const contents = document.querySelectorAll('.u-profile-tab-content > div');

tabs.forEach((tab, index) => {
    tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        contents.forEach(c => c.classList.remove('active'));

        tab.classList.add('active');
        contents[index].classList.add('active');
    });
});

// ==========================
// Driver unverified - Upload Handler
// ==========================
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

// ==========================
// Driver Profile Popup
// ==========================
document.getElementById('editDriverInfo').addEventListener('click', async () => {
    document.getElementById('driverProfilePopup').style.display = 'block';
});

document.getElementById('driverProfilePopupClose').addEventListener('click', function() {
    document.getElementById('driverProfilePopup').style.display = 'none'; 
});
