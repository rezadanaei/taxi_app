<?php

namespace App\Services;

use App\Models\AdminNotification;

class AdminNotificationService
{
    
    
    public function create(string $title, string $body = null , array $data = [])
    {
        return AdminNotification::create([
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);
    }
}
