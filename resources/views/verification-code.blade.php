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
      </style>

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
  
</body>
</html>