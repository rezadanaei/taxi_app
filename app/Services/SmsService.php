<?php

namespace App\Services;

class SmsService
{
    private string $username;
    private string $password;
    private string $from;

    public function __construct()
    {
        $this->username = setting('sms_panel_username');
        $this->password = setting('sms_panel_password');
        $this->from     = setting('sms_panel_number');
    }

    /**
     * ارسال پیامک معمولی
     * @param string|array $to
     * @param string $message
     * @return array
     */
    public function send($to, string $message): array
    {
        if (is_array($to)) {
            $to = implode(',', $to);
        }

        $data = [
            'username' => $this->username,
            'password' => $this->password,
            'to'       => $to,
            'from'     => $this->from,
            'text'     => $message,
            'isflash'  => false
        ];

        $response = $this->curl('https://rest.payamak-panel.com/api/SendSMS/SendSMS', $data);

        return $this->handleResponse($response);
    }

    /**
     * ارسال پیامک الگو (SendByBaseNumber)
     * @param string $to
     * @param array $params
     * @param int $bodyId
     * @return array
     */
    public function sendPattern(string $to, array $params, int $bodyId): array
    {
        $data = [
            'username' => $this->username,
            'password' => $this->password,
            'to'       => $to,
            'bodyId'   => $bodyId,
            'text'     => $params
        ];

        $response = $this->curl('https://rest.payamak-panel.com/api/SendSMS/BaseNumber', $data);

        $result = json_decode($response, true);

        if (!isset($result['RetStatus'])) {
            return [
                'status' => false,
                'message' => 'پاسخ معتبر از سرور دریافت نشد.'
            ];
        }

        if ($result['RetStatus'] == 1) {
            return [
                'status' => true,
                'recId'  => $result['Value'],
                'message' => 'پیامک الگو با موفقیت ارسال شد.'
            ];
        }

        return [
            'status' => false,
            'message' => $this->errorMessage((string)$result['RetStatus'])
        ];
    }

    /**
     * دریافت وضعیت پیامک
     * @param string $recId
     */
    public function delivery(string $recId): array
    {
        $data = [
            'username' => $this->username,
            'password' => $this->password,
            'recId'    => $recId
        ];

        $response = $this->curl('https://rest.payamak-panel.com/api/SendSMS/GetDeliveries2', $data);

        return $this->handleDeliveryResponse($response);
    }

    /**
     * Curl
     */
    private function curl(string $url, array $data)
    {
        $post_data = http_build_query($data);

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, [
            'content-type: application/x-www-form-urlencoded'
        ]);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);

        return curl_exec($handle);
    }

    /**
     * هندل نتیجه ارسال پیامک معمولی
     */
    private function handleResponse($response): array
    {
        $result = json_decode($response, true);

        if (!isset($result['Value'])) {
            return [
                'status' => false,
                'message' => 'پاسخ سرویس نامعتبر است.'
            ];
        }

        $statusCode = (string)$result['Value'];

        if (preg_match('/^\d+(,\d+)*$/', $statusCode)) {
            return [
                'status' => true,
                'recIds' => explode(',', $statusCode),
                'message' => 'پیامک با موفقیت ارسال شد.'
            ];
        }

        return [
            'status' => false,
            'message' => $this->errorMessage($statusCode)
        ];
    }

    /**
     * هندل پاسخ وضعیت پیامک
     */
    private function handleDeliveryResponse($response): array
    {
        $result = json_decode($response, true);

        if (!isset($result['Value'])) {
            return [
                'status' => false,
                'message' => 'پاسخ سرویس نامعتبر است.'
            ];
        }

        return [
            'status' => true,
            'state'  => $this->deliveryStatus((string)$result['Value']),
            'message' => 'وضعیت دریافت بازیابی شد.'
        ];
    }

    /**
     * متن خطاها
     */
    private function errorMessage(string $code): string
    {
        $errors = [
            "-110" => "الزام استفاده از ApiKey",
            "-109" => "الزام تنظیم IP برای استفاده از API",
            "-108" => "مسدود شدن IP به دلیل تلاش ناموفق",
            "0"    => "نام کاربری یا رمز عبور اشتباه است.",
            "2"    => "اعتبار کافی نمی‌باشد.",
            "3"    => "محدودیت ارسال روزانه.",
            "4"    => "محدودیت حجم ارسال.",
            "5"    => "شماره فرستنده نامعتبر است.",
            "6"    => "سامانه در حال بروزرسانی است.",
            "7"    => "متن حاوی کلمه فیلتر شده است.",
            "9"    => "ارسال از خطوط عمومی از طریق وب سرویس ممکن نیست.",
            "10"   => "کاربر غیرفعال است.",
            "12"   => "مدارک کاربر کامل نیست.",
            "14"   => "متن حاوی لینک است.",
            "15"   => "ارسال گروهی نیازمند لغو11",
            "16"   => "شماره گیرنده یافت نشد.",
            "17"   => "متن پیام خالی است.",
            "18"   => "شماره گیرنده نامعتبر است.",
            "21"   => "شناسه الگو (BodyId) موجود نیست.",
            "22"   => "خطا در مقداردهی پارامترهای الگو.",
        ];

        return $errors[$code] ?? "خطای ناشناخته ($code)";
    }

    /**
     * وضعیت تحویل پیامک
     */
    private function deliveryStatus(string $code): string
    {
        return match ($code) {
            "1"  => "تحویل شده",
            "2"  => "در صف ارسال",
            "3"  => "تحویل نشده",
            "11" => "ارسال نشده",
            default => "وضعیت نامشخص ($code)"
        };
    }
}
