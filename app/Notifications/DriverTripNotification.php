<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\DriverTripChannel;

class DriverTripNotification extends Notification
{
    use Queueable;

    public $data;

    public function __construct($data)
    {
        $this->data = (array) $data;
    }

    public function via($notifiable)
    {
        return [DriverTripChannel::class];
    }

    public function toDriverTrip($notifiable)
    {
        return [
            'driver_id' => $this->data['driver_id'],
            'trip_id'   => $this->data['trip_id'],
            'is_sent'   => false,
        ];
    }
}
