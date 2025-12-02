<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\DriverNotification;
use App\Notifications\Channels\DriverDatabaseChannel;

class DriverSubmitted extends Notification
{
    use Queueable;

    protected $driver;

    /**
     * Create a new notification instance.
     */
    public function __construct($driver)
    {
        $this->driver = $driver;

    }

    /**
     * Define the notification delivery channel.
     */
    public function via($notifiable)
    {
        return [DriverDatabaseChannel::class];
    }

    /**
     * Store the notification in the custom driver_notifications table.
     */
    public function toDriverDatabase($notifiable)
    {
        return [
            'driver_id' => $this->driver->id,
            'message'   => "راننده {$this->driver->userable->first_name} {$this->driver->userable->last_name} ثبت شد و در انتظار بررسی است.",
        ];

    }
}
