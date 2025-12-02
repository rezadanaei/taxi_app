<?php

namespace App\Notifications\Channels;

use App\Models\DriverNotification;
use App\Services\PushNotificationService;
use App\Models\Admin;
use App\Facades\SMS;

class DriverDatabaseChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, $notification)
    {
        // دریافت داده از Notification class
        $data = $notification->toDriverDatabase($notifiable);

        // ذخیره در دیتابیس
        DriverNotification::create([
            'driver_id' => $data['driver_id'] ?? null,
            'message'   => $data['message'] ?? null,
        ]);
        $allPhones = Admin::pluck('phone')->toArray();
        
        $result = SMS::send($allPhones, $data['message'] ?? 'یک راننده جدید در سامانه ثبت شده و منتظر تایید شماست.');
        
    }
}
