<?php

namespace App\Notifications\Channels;

use App\Models\AdminNotification;

class AdminDatabaseChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, $notification)
    {
        $data = $notification->toAdminDatabase($notifiable);

        if (!$data) return;

        $notificationModel = AdminNotification::create([
            'title'   => $data['title'] ?? null,
            'body' => $data['message'] ?? null,
            'data'    => $data['data'] ?? null, 
        ]);
        
    }
}
