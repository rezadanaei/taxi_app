<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }} | {{ config('app.name') }}</title>
    
    <!-- فونت فارسی Vazir -->
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet" type="text/css" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Vazir', Tahoma, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            line-height: 1.8;
            color: #2d3748;
        }

        .payment-result-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            max-width: 480px;
            width: 100%;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .status-badge {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 40px;
            background: #f8fafc;
            border: 4px solid;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(66, 153, 225, 0.4); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(66, 153, 225, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(66, 153, 225, 0); }
        }

        .status-title {
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 10px;
            color: #1a202c;
        }

        .status-subtitle {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 25px;
            opacity: 0.9;
        }

        .payment-details {
            background: #f7fafc;
            border-radius: 16px;
            padding: 20px;
            margin: 25px 0;
            text-align: right;
            border: 1px solid #e2e8f0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #edf2f7;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #718096;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-value {
            color: #2d3748;
            font-weight: 600;
            font-size: 15px;
        }

        .tracking-code {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 17px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .countdown-container {
            margin: 30px 0 25px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 16px;
            border: 2px dashed #cbd5e0;
        }

        .countdown-text {
            color: #4a5568;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .countdown-timer {
            font-size: 32px;
            font-weight: 700;
            color: #4299e1;
            font-family: 'Courier New', monospace;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-width: 140px;
            border: 2px solid transparent;
        }

        .btn-primary {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(72, 187, 120, 0.3);
        }

        .btn-secondary {
            background: #edf2f7;
            color: #4a5568;
            border-color: #e2e8f0;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            color: #4299e1;
            border-color: #4299e1;
        }

        .btn-outline:hover {
            background: #4299e1;
            color: white;
        }

        /* حالت‌های موفق و خطا */
        .success { 
            border-color: #48bb78; 
            color: #48bb78;
            animation: pulseSuccess 2s infinite;
        }

        .error { 
            border-color: #f56565; 
            color: #f56565;
            animation: pulseError 2s infinite;
        }

        @keyframes pulseSuccess {
            0% { box-shadow: 0 0 0 0 rgba(72, 187, 120, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(72, 187, 120, 0); }
            100% { box-shadow: 0 0 0 0 rgba(72, 187, 120, 0); }
        }

        @keyframes pulseError {
            0% { box-shadow: 0 0 0 0 rgba(245, 101, 101, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(245, 101, 101, 0); }
            100% { box-shadow: 0 0 0 0 rgba(245, 101, 101, 0); }
        }

        /* Responsive */
        @media (max-width: 576px) {
            .payment-result-card {
                padding: 30px 20px;
                border-radius: 20px;
            }
            
            .status-badge {
                width: 75px;
                height: 75px;
                font-size: 32px;
            }
            
            .status-title {
                font-size: 22px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                padding: 12px 20px;
            }
            
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            }
            
            .payment-result-card {
                background: #2d3748;
                color: #e2e8f0;
            }
            
            .detail-value, .status-title {
                color: #e2e8f0;
            }
            
            .payment-details {
                background: #4a5568;
                border-color: #718096;
            }
            
            .detail-label {
                color: #cbd5e0;
            }
            
            .btn-secondary {
                background: #4a5568;
                color: #e2e8f0;
                border-color: #718096;
            }
            
            .btn-secondary:hover {
                background: #718096;
            }
        }
    </style>
</head>
<body>
    <div class="payment-result-card">
        <!-- آیکون وضعیت -->
        <div class="status-badge {{ $success ? 'success' : 'error' }}">
            {{ $success ? '✓' : '✗' }}
        </div>
        
        <!-- عنوان اصلی -->
        <h1 class="status-title">{{ $title }}</h1>
        
        <!-- پیام توضیحی -->
        <p class="status-subtitle">{{ $message }}</p>
        
        <!-- جزئیات پرداخت -->
        <div class="payment-details">
            <div class="detail-row">
                <span class="detail-label">
                    <i class="fas fa-receipt"></i>
                    عنوان پرداخت:
                </span>
                <span class="detail-value">{{ $paymentTitle }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">
                    <i class="fas fa-hashtag"></i>
                    شماره:
                </span>
                <span class="detail-value">#{{ $payableId }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">
                    <i class="fas fa-money-bill-wave"></i>
                    مبلغ:
                </span>
                <span class="detail-value">{{ $amount }}</span>
            </div>
            
            @if($success && isset($trackingCode))
                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-fingerprint"></i>
                        کد رهگیری:
                    </span>
                    <div class="tracking-code">{{ $trackingCode }}</div>
                </div>
            @endif
        </div>
        
        <!-- شمارش معکوس -->
        <div class="countdown-container">
            <p class="countdown-text">هدایت خودکار به صفحه بعد در:</p>
            <div class="countdown-timer" id="countdown">{{ $redirectDelay }}</div>
            <p class="countdown-text">ثانیه</p>
        </div>
        
        <!-- دکمه‌های اقدام -->
        <div class="action-buttons">
            @if($success)
                <a href="{{ $redirectTo }}" class="btn btn-primary">
                    <i class="fas fa-tachometer-alt"></i>
                    ادامه به داشبورد
                </a>
            @else
                <a href="{{ $redirectTo }}" class="btn btn-primary">
                    <i class="fas fa-redo"></i>
                    تلاش مجدد پرداخت
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline">
                    <i class="fas fa-home"></i>
                    صفحه اصلی
                </a>
            @endif
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let countdown = {{ $redirectDelay }};
            const countdownElement = document.getElementById('countdown');
            const redirectUrl = "{{ $redirectTo }}";
            
            // تابع شمارش معکوس
            const countdownInterval = setInterval(function() {
                countdown--;
                countdownElement.textContent = countdown;
                
                // تغییر رنگ در 3 ثانیه آخر
                if (countdown <= 3) {
                    countdownElement.style.color = countdown <= 1 ? '#f56565' : '#ed8936';
                    countdownElement.style.transform = 'scale(1.1)';
                } else {
                    countdownElement.style.transform = 'scale(1)';
                }
                
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    window.location.href = redirectUrl;
                }
            }, 1000);
            
            // امکان توقف شمارش با کلیک روی تایمر
            countdownElement.addEventListener('click', function() {
                clearInterval(countdownInterval);
                countdownElement.textContent = 'متوقف شد';
                countdownElement.style.color = '#718096';
                countdownElement.style.animation = 'none';
                
                // نمایش پیام
                alert('شمارش معکوس متوقف شد. می‌توانید به صورت دستی اقدام کنید.');
            });
            
            // جلوگیری از خروج تصادفی صفحه
            window.addEventListener('beforeunload', function(e) {
                if (countdown > 0) {
                    e.preventDefault();
                    e.returnValue = 'آیا مطمئن هستید می‌خواهید از این صفحه خارج شوید؟';
                }
            });
        });
    </script>
</body>
</html>