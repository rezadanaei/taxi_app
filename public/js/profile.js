// ==========================
// User Current Trip (Width Event Delegation)
// ==========================
document.addEventListener('click', function(e) {

    if (e.target.closest('.acceptTrip')) return;

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
// document.querySelectorAll(".u-driver-grid-4 .file-upload").forEach(uploadBox => {
//     const realFile = uploadBox.querySelector("input[type=file]");
//     const customBtn = uploadBox.querySelector(".file-button");

//     customBtn.addEventListener("click", () => {
//         realFile.click();
//     });

//     realFile.addEventListener("change", () => {
//         if (realFile.files.length > 0) {
//             customBtn.classList.add("file-selected");
//         } else {
//             customBtn.classList.remove("file-selected");
//         }
//     });
// });

function updatePreview(fieldName, file) {
    let box = document.querySelector(`.camera-opener[data-field="${fieldName}"]`).parentElement;

    let oldImg = box.querySelector("img");
    if (oldImg) oldImg.remove();

    let img = document.createElement("img");
    img.src = URL.createObjectURL(file);
    img.width = 120;
    img.style.marginBottom = "10px";
    img.style.borderRadius = "8px";
    box.prepend(img);
}


document.addEventListener("DOMContentLoaded", async () => {
    document.querySelectorAll(".u-driver-grid-4 .file-upload").forEach(async (box) => {

        let img = box.querySelector("img");
        let button = box.querySelector(".camera-opener");
        let field = button.dataset.field;

        if (img) {
            let input = document.querySelector(`input[name="${field}"]`);

            if (!input) {
                input = document.createElement("input");
                input.type = "file";
                input.name = field;
                input.style.display = "none";
                document.querySelector(".u-driver-form").appendChild(input);
            }

            try {
                const response = await fetch(img.src);
                const blob = await response.blob();
                const file = new File([blob], field + ".jpg", { type: blob.type });

                let dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;

                button.classList.add("file-selected");
            } catch (e) {
                console.log("خطای تبدیل عکس به فایل:", e);
            }
        }
    });
});



let stream = null;
let currentFacingMode = 'environment';

document.querySelectorAll('.camera-opener').forEach(opener => {

    const fieldName = opener.dataset.field;
    const originalText = opener.textContent.trim();

    opener.addEventListener('click', async e => {
        e.preventDefault();
        e.stopPropagation();

        
        if (fieldName === 'car_insurance') {

            if (opener.classList.contains('file-selected')) {
                if (!confirm('عکس قبلاً بارگذاری شده است. دوباره انتخاب شود؟')) return;

                opener.classList.remove('file-selected');
                const oldInput = document.querySelector(`input[name="${fieldName}"]`);
                if (oldInput) oldInput.remove();
            }

            let input = document.createElement('input');
            input.type = 'file';
            input.name = fieldName;
            input.accept = 'image/*';
            input.style.display = 'none';

            document.querySelector('.u-driver-form').appendChild(input);

            input.click();

            input.onchange = () => {
                if (!input.files.length) return;
                const file = input.files[0];

                updatePreview(fieldName, file);

                opener.textContent = 'فایل انتخاب شد';
                opener.classList.add('file-selected');
            };

            return;
        }

       
        if (opener.classList.contains('file-selected')) {
            if (!confirm('عکس قبلاً گرفته شده. دوباره بگیرید؟')) return;

            opener.classList.remove('file-selected');
            opener.textContent = originalText;

            const img = opener.parentElement.querySelector('img:not([src*="storage"])');
            if (img) img.remove();

            const oldInput = document.querySelector(`input[name="${fieldName}"]`);
            if (oldInput) oldInput.remove();
        }

        
        const modal = document.createElement('div');
        modal.style.cssText = `
            position:fixed;top:0;left:0;width:100%;height:100%;
            background:#000;z-index:9999;display:flex;
            flex-direction:column;align-items:center;justify-content:center;color:#fff;
        `;
        modal.innerHTML = `
            <video id="camVideo" autoplay playsinline style="width:90%;max-width:500px;border-radius:16px;"></video>
            <div style="margin:20px 0;display:flex;gap:20px;align-items:center;">
                <button id="takePhoto" style="padding:15px 35px;background:#28a745;color:#fff;border:none;border-radius:50px;font-size:18px;">عکس بگیر</button>
                <button id="switchCam" style="padding:12px 18px;background:#444;color:#fff;border:none;border-radius:50%;font-size:20px;">Switch</button>
                <button id="closeCam" style="padding:15px 35px;background:#dc3545;color:#fff;border:none;border-radius:50px;font-size:18px;">بستن</button>
            </div>
        `;
        document.body.appendChild(modal);

        const video = modal.querySelector('#camVideo');
        const switchBtn = modal.querySelector('#switchCam');

        const startCamera = async mode => {
            if (stream) stream.getTracks().forEach(t => t.stop());
            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: mode },
                    audio: false
                });
                video.srcObject = stream;
                currentFacingMode = mode;
                switchBtn.textContent = mode === 'environment' ? 'دوربین جلو' : 'دوربین عقب';
            } catch (err) {
                alert('دوربین در دسترس نیست');
                modal.remove();
            }
        };

        await startCamera('environment');

        switchBtn.onclick = () =>
            startCamera(currentFacingMode === 'environment' ? 'user' : 'environment');

        modal.querySelector('#closeCam').onclick = () => {
            if (stream) stream.getTracks().forEach(t => t.stop());
            modal.remove();
        };

    
        modal.querySelector('#takePhoto').onclick = () => {

            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            canvas.toBlob(blob => {

                let input = document.querySelector(`input[name="${fieldName}"]`);
                if (!input) {
                    input = document.createElement('input');
                    input.type = 'file';
                    input.name = fieldName;
                    input.style.display = 'none';
                    document.querySelector('.u-driver-form').appendChild(input);
                }

                const file = new File([blob], `${fieldName}.jpg`, { type: 'image/jpeg' });
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;

                updatePreview(fieldName, file);

                opener.textContent = 'عکس گرفته شد';
                opener.classList.add('file-selected');

                if (stream) stream.getTracks().forEach(t => t.stop());
                modal.remove();

            }, 'image/jpeg', 0.95);
        };
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


// Jalali Datapicker and set max days to 30
const today = new Date();
const maxDate = new Date(today);
maxDate.setDate(today.getDate() + 30);

const [jy, jm, jd] = gregorian_to_jalali(
    maxDate.getFullYear(),
    maxDate.getMonth() + 1,
    maxDate.getDate()
);

const maxDateString = `${jy}/${jm.toString().padStart(2, '0')}/${jd.toString().padStart(2, '0')}`;

// باید این مقدار درست باشد
document.getElementById("rideDate").setAttribute("data-jdp-maxDate", maxDateString);

// jalali datepicker
jalaliDatepicker.startWatch({
    separatorChars: {
        date: '/',
        time: ':',
        between: ' '
    },
    date: true,
    time: false,
    autoShow: true,
    autoHide: true,
    hasSecond: false,
    hideAfterChange: true,
    persianDigits: false,
    format: 'YYYY/MM/DD',
    zIndex: 1000,
    useDropDownYears: false,
    minDate: "today",
    maxDate: "attr"
});
