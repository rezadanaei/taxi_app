<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تکمیل اطلاعات راننده</title>
    <link rel="shortcut icon" href="{{ asset('/img/fav.png') }}" type="image/x-icon">

    <style>
        :root {
            --main-color: {{ setting('colers_primary') }};
            --second-color: {{ setting('colers_secondary') }};
            --third-color: {{ setting('colers_tertiary') }};
        }
        .file-button { cursor: pointer; padding: 14px; background: #f8f9fa; border: 2px dashed #ccc; border-radius: 12px; text-align: center; transition: 0.3s; font-size: 14px; }
        .file-button:hover { background: #e9ecef; }
        .file-selected { background: #d4edda !important; border-color: #28a745 !important; color: #155724; }
        .camera-preview { position: relative; width: 100%; max-width: 340px; margin: 15px auto; border-radius: 16px; overflow: hidden; background: #000; box-shadow: 0 8px 20px rgba(0,0,0,0.3); }
        .camera-preview video { width: 100%; display: block; }
        .camera-controls { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 15px; }
        .camera-controls button { background: white; border: none; width: 64px; height: 64px; border-radius: 50%; font-size: 28px; box-shadow: 0 4px 15px rgba(0,0,0,0.4); }
        .retake-btn { background: #ffc107 !important; }
        .preview-img { border-radius: 12px; margin-bottom: 10px; max-width: 100%; }
    </style>

    <link rel="stylesheet" href="{{ asset('css/jalalidatepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
</head>
<body>

<div class="user-profile-container max-width">
    <div class="u-profile-info">
        <img class="u-profile-info-img" src="{{ asset('/img/no-photo.png') }}" alt="تصویر کاربری">
        <section>
            <div class="u-profile-username">
                <h2>نام کاربری</h2>
                <form method="POST" action="{{ route('logout') }}" style="display:inline" id="logoutForm">
                    @csrf
                    <button type="submit">خروج</button>
                </form>
            </div>
            <div class="u-profile-type">راننده</div>
        </section>
    </div>

    <div class="u-profile-content">
        <div class="u-driver-top">
            <h1>تکمیل اطلاعات</h1>
            <p>راننده گرامی برای دسترسی به سفرها، لطفاً مدارک را با دوربین بگیرید.</p>
        </div>

        @php
            $user = auth()->user();
            $driver = $user->userable;
        @endphp

        <form class="u-driver-form" method="POST" action="{{ route('driver.save') }}" enctype="multipart/form-data">
            @csrf

            <section class="u-driver-personal-info">
                <h2>اطلاعات هویتی</h2>
                <div class="u-driver-grid-2">
                    <input type="text" name="first_name" placeholder="نام" value="{{ $driver->first_name ?? '' }}">
                    <input type="text" name="last_name" placeholder="نام خانوادگی" value="{{ $driver->last_name ?? '' }}">
                    <input type="text" name="father_name" placeholder="نام پدر" value="{{ $driver->father_name ?? '' }}">
                    <input type="text" name="birth_date" data-jdp placeholder="تاریخ تولد" value="{{ $driver->birth_date ?? '' }}">
                    <input type="hidden" name="start_date" id="startDateFinal">
                    <input type="text" name="national_code" placeholder="کد ملی" value="{{ $driver->national_code ?? '' }}">
                    <input type="tel" name="phone" placeholder="شماره موبایل" value="{{ $user->phone ?? '' }}">
                    <input class="full-width" type="text" name="address" placeholder="آدرس محل سکونت" value="{{ $driver->address ?? '' }}">
                </div>

                <div class="u-driver-grid-4">

                    <div class="file-upload camera-upload">
                        @if($driver->id_card_front)<img src="{{ asset('storage/'.$driver->id_card_front) }}" class="preview-img">@endif
                        <div class="camera-preview" id="preview_id_card_front" style="display:none;">
                            <video id="video_id_card_front" autoplay playsinline></video>
                            <canvas id="canvas_id_card_front" style="display:none;"></canvas>
                            <div class="camera-controls">
                                <button type="button" class="capture-btn">Camera</button>
                                <button type="button" class="retake-btn" style="display:none;">Redo</button>
                            </div>
                        </div>
                        <input type="file" file" name="id_card_front" id="file_id_card_front" style="display:none;">
                        <label class="file-button camera-trigger" data-target="id_card_front">عکس روی کارت ملی</label>
                    </div>

                    <div class="file-upload camera-upload">
                        @if($driver->id_card_back)<img src="{{ asset('storage/'.$driver->id_card_back) }}" class="preview-img">@endif
                        <div class="camera-preview" id="preview_id_card_back" style="display:none;">
                            <video id="video_id_card_back" autoplay playsinline></video>
                            <canvas id="canvas_id_card_back" style="display:none;"></canvas>
                            <div class="camera-controls">
                                <button type="button" class="capture-btn">Camera</button>
                                <button type="button" class="retake-btn" style="display:none;">Redo</button>
                            </div>
                        </div>
                        <input type="file" name="id_card_back" id="file_id_card_back" style="display:none;">
                        <label class="file-button camera-trigger" data-target="id_card_back">عکس پشت کارت ملی</label>
                    </div>

                    <div class="file-upload camera-upload">
                        @if($driver->id_card_selfie)<img src="{{ asset('storage/'.$driver->id_card_selfie) }}" class="preview-img">@endif
                        <div class="camera-preview" id="preview_id_card_selfie" style="display:none;">
                            <video id="video_id_card_selfie" autoplay playsinline></video>
                            <canvas id="canvas_id_card_selfie" style="display:none;"></canvas>
                            <div class="camera-controls">
                                <button type="button" class="capture-btn">Camera</button>
                                <button type="button" class="retake-btn" style="display:none;">Redo</button>
                            </div>
                        </div>
                        <input type="file" name="id_card_selfie" id="file_id_card_selfie" style="display:none;">
                        <label class="file-button camera-trigger" data-target="id_card_selfie">سلفی با کارت ملی</label>
                    </div>

                    <div class="file-upload camera-upload">
                        @if($driver->profile_photo)<img src="{{ asset('storage/'.$driver->profile_photo) }}" class="preview-img">@endif
                        <div class="camera-preview" id="preview_profile_photo" style="display:none;">
                            <video id="video_profile_photo" autoplay playsinline></video>
                            <canvas id="canvas_profile_photo" style="display:none;"></canvas>
                            <div class="camera-controls">
                                <button type="button" class="capture-btn">Camera</button>
                                <button type="button" class="retake-btn" style="display:none;">Redo</button>
                            </div>
                        </div>
                        <input type="file" name="profile_photo" id="file_profile_photo" style="display:none;">
                        <label class="file-button camera-trigger" data-target="profile_photo">تصویر پرسنلی</label>
                    </div>

                    @php
                        $carFields = [
                            ['name' => 'license_front', 'label' => 'عکس روی گواهینامه'],
                            ['name' => 'license_back', 'label' => 'عکس پشت گواهینامه'],
                            ['name' => 'car_card_front', 'label' => 'عکس روی کارت خودرو'],
                            ['name' => 'car_card_back', 'label' => 'عکس پشت کارت خودرو'],
                            ['name' => 'car_insurance', 'label' => 'بیمه‌نامه خودرو'],
                            ['name' => 'car_front_image', 'label' => 'نمای جلوی خودرو'],
                            ['name' => 'car_back_image', 'label' => 'نمای عقب خودرو'],
                            ['name' => 'car_left_image', 'label' => 'نمای چپ خودرو'],
                            ['name' => 'car_right_image', 'label' => 'نمای راست خودرو'],
                            ['name' => 'car_front_seats_image', 'label' => 'صندلی‌های جلو و داشبورد'],
                            ['name' => 'car_back_seats_image', 'label' => 'صندلی‌های عقب'],
                        ];
                    @endphp

                    @foreach($carFields as $field)
                    <div class="file-upload camera-upload">
                        @if($driver->{$field['name']})<img src="{{ asset('storage/'.$driver->{$field['name']}) }}" class="preview-img">@endif
                        <div class="camera-preview" id="preview_{{ $field['name'] }}" style="display:none;">
                            <video id="video_{{ $field['name'] }}" autoplay playsinline></video>
                            <canvas id="canvas_{{ $field['name'] }}" style="display:none;"></canvas>
                            <div class="camera-controls">
                                <button type="button" class="capture-btn">Camera</button>
                                <button type="button" class="retake-btn" style="display:none;">Redo</button>
                            </div>
                        </div>
                        <input type="file" name="{{ $field['name'] }}" id="file_{{ $field['name'] }}" style="display:none;">
                        <label class="file-button camera-trigger" data-target="{{ $field['name'] }}">{{ $field['label'] }}</label>
                    </div>
                    @endforeach

                </div>
            </section>

            <button class="u-driver-form-submit" type="submit" style="width:100%; padding:18px; font-size:18px; margin-top:30px;">
                ثبت و ارسال مدارک
            </button>
        </form>
    </div>
</div>

<script>jalaliDatepicker.startWatch();</script>

<script>
document.querySelectorAll('.camera-trigger').forEach(trigger => {
    const target = trigger.dataset.target;
    const preview = document.getElementById(`preview_${target}`);
    const video = document.getElementById(`video_${target}`);
    const canvas = document.getElementById(`canvas_${target}`);
    const captureBtn = preview.querySelector('.capture-btn');
    const retakeBtn = preview.querySelector('.retake-btn');
    const input = document.getElementById(`file_${target}`);
    const label = trigger;

    if (!label.dataset.originalText) label.dataset.originalText = label.textContent.trim();

    trigger.onclick = async () => {
        preview.style.display = 'block';
        label.style.display = 'none';

        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: "environment" }, audio: false
            });
            video.srcObject = stream;
        } catch (err) {
            alert("لطفاً دسترسی به دوربین را فعال کنید");
            preview.style.display = 'none';
            label.style.display = 'block';
        }
    };

    captureBtn.onclick = () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        canvas.toBlob(blob => {
            const file = new File([blob], `${target}.jpg`, { type: 'image/jpeg' });
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;

            const img = document.createElement('img');
            img.src = URL.createObjectURL(blob);
            img.className = 'preview-img';
            preview.before(img);

            video.srcObject.getTracks().forEach(t => t.stop());
            captureBtn.style.display = 'none';
            retakeBtn.style.display = 'block';
            label.textContent = 'عکس گرفته شد';
            label.classList.add('file-selected');
        }, 'image/jpeg', 0.95);
    };

    retakeBtn.onclick = () => {
        if (preview.previousElementSibling?.classList.contains('preview-img')) {
            preview.previousElementSibling.remove();
        }
        input.value = '';
        label.textContent = label.dataset.originalText;
        label.classList.remove('file-selected');
        captureBtn.style.display = 'block';
        retakeBtn.style.display = 'none';

        navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
            .then(stream => video.srcObject = stream);
    };
});
</script>

</body>
</html>