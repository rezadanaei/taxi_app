<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\AdminDatabaseChannel;

class AdminNotification extends Notification
{
    use Queueable;

    public $title;
    public $body;
    public $data;

    public function __construct($title, $body, $data )
    {
        $this->title = $title;
        $this->body= $body;
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return [AdminDatabaseChannel::class]; 
    }

    public function toAdminDatabase($notifiable)
    {
        $notification =[
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data
        ];

        

        return $notification;
    }
}
