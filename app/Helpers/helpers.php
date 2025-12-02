<?php

use App\Models\Tariff;
use App\Models\Setting;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;


use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

if (!function_exists('tariff')) {
    /**
     * Retrieve tariff settings
     *
     * Algorithm:
     * 1) First, it checks the cache
     * 2) If not found, it checks the database
     * 3) If the database is empty, it takes the default value from the config
     *
     * @param string|null $key
     * @return mixed
     */

    function tariff($key = null)
    {
        $ttl = now()->addDays(30); 

        $settings = Cache::remember('tariff_settings', $ttl, function () {
            $dbSettings = Tariff::first()?->toArray();

            if (empty($dbSettings)) {
                $dbSettings = config('settings.tariffs', []);
            }

            return $dbSettings;
        });

        Cache::put('tariff_settings', $settings, $ttl);

        if ($key) {
            return $settings[$key] ?? config("settings.tariffs.$key");
        }

        return array_merge(config('settings.tariffs', []), $settings);
    }

    

}
if (! function_exists('setting')) {
    /**
     * Retrieve site settings with a 30-day cache
     *
     * @param string|null $key
     * @return mixed
     */

    function setting($key = null)
    {
        $ttl = now()->addDays(30);

        $settings = Cache::remember('site_settings', $ttl, function () {
            $dbSettings = Setting::first()?->toArray();

            if (empty($dbSettings)) {
                $dbSettings = config('settings.site', []);
            }

            return $dbSettings;
        });

        Cache::put('site_settings', $settings, $ttl);

        if ($key) {
            return $settings[$key] ?? config("settings.site.$key");
        }

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