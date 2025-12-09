<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Page Title -->
  <title>ورود | عضویت</title>
  <link rel="shortcut icon" href="{{ asset('/img/fav.png') }}" type="image/x-icon">
  <!-- Page Style -->
  <style>
    :root {

        --main-color: {{ setting('colers_primary') }};
        --second-color: {{ setting('colers_secondary') }};
        --Third-color: {{ setting('colers_tertiary') }};
      }
  </style>
  <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
</head>
<body>

  <!-- Code page -->
   <div class="login-container">
    
    <div class="login-signup-container max-width">

      <form action="{{ route('login.code.verify') }}" method="POST" class="login">
        @csrf

        <h1>کد تایید</h1>
        <p>کد تایید ارسال شده را در قسمت زیر وارد کنید.</p>

        <div class="tel-input">
          @php
            $codeError = $errors->first('code');
            $inputValue = old('code', '');
          @endphp

          <input
            type="tel"
            name="code"
            id="code"
            maxlength="6"
            placeholder="کد تایید"
            value="{{ old('code', $inputValue) }}"
            class="{{ $codeError ? 'input-error' : '' }}"
            autocomplete="one-time-code"
          >

          {{-- preserve phone and role in submission --}}
          <input type="hidden" name="phone" value="{{ old('phone', $phone ?? '') }}">
          <input type="hidden" name="role" value="{{ old('role', $role ?? '') }}">

          <input class="button" type="submit" value="ادامه">
        </div>
      </form>
        @if($codeError)
            <div class="error-text" id="code-error">{{ $codeError }}</div>
        @endif
      <style>
        .input-error { border-color: #c0392b; }
        .error-text { color: #c0392b; margin-top: 6px; font-size: 0.95rem; }
        .resend-wrapper {
            margin-top: 25px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .resend-btn {
            background: var(--main-color);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s ease-in-out;
            opacity: 0.5;        
        }

        .resend-btn:hover:not([disabled]) {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .resend-btn:disabled {
            cursor: not-allowed;
            opacity: 0.4;
        }

        .resend-timer {
            margin-top: 10px;
            font-size: 0.95rem;
            color: #666;
            letter-spacing: 0.5px;
            font-weight: 400;
        }

      </style>
      <div class="resend-wrapper">
          <button id="resend-btn" disabled class="resend-btn">ارسال مجدد کد</button>
          <div id="timer" class="resend-timer"></div>
      </div>

      <script>
        // hide error message when the user starts typing
        (function(){
          var codeInput = document.getElementById('code');
          var codeErrorEl = document.getElementById('code-error');
          if (!codeInput) return;
          codeInput.addEventListener('input', function(){
            if (codeErrorEl) {
              codeErrorEl.style.display = 'none';
            }
            codeInput.classList.remove('input-error');
          });
        })();
      </script>

    </div>

   </div>
  <!-- Code page end -->
  <script>
    let timerElement = document.getElementById('timer');
    let resendBtn = document.getElementById('resend-btn');

    let phone = "{{ $phone }}"; // شماره کاربر
    let countdown = 120; // دو دقیقه

    startTimer();

    function startTimer() {
        resendBtn.disabled = true;
        resendBtn.style.opacity = "0.5";

        let interval = setInterval(() => {

            let minutes = Math.floor(countdown / 60);
            let seconds = countdown % 60;

            timerElement.innerText = `ارسال مجدد کد تا ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

            countdown--;

            if (countdown < 0) {
                clearInterval(interval);
                timerElement.innerText = "";
                resendBtn.disabled = false;
                resendBtn.style.opacity = "1";
            }

        }, 1000);
    }

    // درخواست ارسال مجدد
    resendBtn.addEventListener('click', function () {

        resendBtn.disabled = true;
        resendBtn.style.opacity = "0.5";
        timerElement.innerText = "در حال ارسال...";

        fetch("{{ route('resend.code') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ phone: phone })
        })
        .then(response => response.json())
        .then(data => {

            if (data.message) {
                timerElement.innerText = "کد دوباره ارسال شد";
            }

            countdown = 120; 
            startTimer(); 

        })
        .catch(() => {
            timerElement.innerText = "خطا در ارسال کد";
            resendBtn.disabled = false;
            resendBtn.style.opacity = "1";
        });
    });
</script>

</body>
</html>