<?php

use App\Models\Tariff;
use App\Models\Setting;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;


use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

if (!function_exists('tariff')) {
    /**
     * دریافت تنظیمات تعرفه‌ها
     *
     * الگوریتم:
     * 1) ابتدا cache را چک می‌کند
     * 2) اگر نبود، دیتابیس را چک می‌کند
     * 3) اگر دیتابیس خالی بود، مقدار پیش‌فرض از config می‌گیرد
     *
     * @param string|null $key
     * @return mixed
     */
    function tariff($key = null)
    {
        $ttl = now()->addDays(30); // زمان نگهداری در کش

        $settings = Cache::remember('tariff_settings', $ttl, function () {
            // گرفتن تعرفه از دیتابیس
            $dbSettings = Tariff::first()?->toArray();

            // اگر دیتابیس خالی بود → از فایل کانفیگ پر شود
            if (empty($dbSettings)) {
                $dbSettings = config('settings.tariffs', []);
            }

            return $dbSettings;
        });

        // (اختیاری) تمدید مجدد TTL برای اطمینان
        Cache::put('tariff_settings', $settings, $ttl);

        // اگر فقط یک کلید خاص خواسته شده
        if ($key) {
            return $settings[$key] ?? config("settings.tariffs.$key");
        }

        // در غیر این صورت همه تعرفه‌ها را (ترکیب کانفیگ و دیتابیس) برگردان
        return array_merge(config('settings.tariffs', []), $settings);
    }

    

}
if (! function_exists('setting')) {
    /**
     * دریافت تنظیمات سایت با کش سی روزه
     *
     * @param  string|null  $key
     * @return mixed
     */
    function setting($key = null)
    {
        $ttl = now()->addDays(30); // مدت نگهداری کش

        $settings = Cache::remember('site_settings', $ttl, function () {
            // تلاش برای گرفتن تنظیمات از دیتابیس
            $dbSettings = Setting::first()?->toArray();

            // اگر دیتابیس خالی بود → مقداردهی با فایل config
            if (empty($dbSettings)) {
                $dbSettings = config('settings.site', []);
            }

            return $dbSettings;
        });

        // اطمینان از تمدید کش برای دفعات بعد
        Cache::put('site_settings', $settings, $ttl);

        // اگر کلید خاصی خواسته شده
        if ($key) {
            return $settings[$key] ?? config("settings.site.$key");
        }

        // اگر بدون کلید فراخوانی شود، تمام تنظیمات را برگرداند
        return array_merge(config('settings.site'), $settings);
    }
}

if (!function_exists('tripDate')) {
    function tripDate(string $utcDateTime, bool $jalali = true)
    {
        $dt = Carbon::parse($utcDateTime)->timezone('Asia/Tehran');

        if ($jalali) {
            return [
                'date' => Jalalian::fromCarbon($dt)->format('d F Y'),
                'time' => $dt->format('H:i'),
            ];
        }

        return [
            'date' => $dt->format('d F Y'),
            'time' => $dt->format('H:i'),
        ];
    }
}

if (!function_exists('tripToUtc')) {
    function tripToUtc(string $tehranDateTime, string $format = 'Y-m-d H:i:s'): string
    {
        $dt = Carbon::createFromFormat($format, $tehranDateTime, 'Asia/Tehran');
        return $dt->timezone('UTC')->format('Y-m-d H:i:s');
    }
}