<?php

namespace App\Notifications\Channels;

use App\Models\User;
use App\Models\Trip;
use App\Models\DriverTripNotification;
use App\Services\PushNotificationService;
class DriverTripChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, $notification)
    {
        $data = $notification->toDriverTrip($notifiable);

        DriverTripNotification::create([
            'driver_id' => $data['driver_id'],
            'trip_id'   => $data['trip_id'],
            'is_sent'   => false,
        ]);

        $tripModel = Trip::with(['carType', 'passenger', 'driver'])->find($data['trip_id']);
        if ($tripModel) {
            $trip = $tripModel->toArray();

            $trip['type'] = 'trip'; 
            $trip['url']  = route('user.profile');
            $tripDT = tripDate($trip['start_date']); 
            $trip['formatted_date'] = $tripDT['date'];
            $trip['formatted_time'] = $tripDT['time'];
        } else {
            $trip = null;
        }
        $pushService = new PushNotificationService();

        $pushService->sendToUsers(
            userId: $data['driver_id'],
            title: 'شما یک سفر جدید دارید',
            body: 'یک سفر جدید مناسب شما ثبت شده است.',
            data: $trip ?? []
        );
    }
}
