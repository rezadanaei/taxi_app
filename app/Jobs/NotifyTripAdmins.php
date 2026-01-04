<?php

namespace App\Jobs;

use App\Facades\SMS;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Trip;
use App\Models\Admin;
use App\Services\AdminNotificationService;
use Illuminate\Queue\SerializesModels;

class NotifyTripAdmins implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected Trip $trip;

    public function __construct(Trip $trip)
    {
        $this->trip = $trip;
    }

    public function handle(): void
    {
        $trip = $this->trip;

        if ($trip->driver_id) {
            return;
        }

        SMS::sendPattern('09352482751', [$trip->id], 401679);

        app(AdminNotificationService::class)->create(
            "هشدار: سفر بدون راننده",
            "یک سفر ثبت شده ولی هیچ راننده‌ای ندارد.",
            ['url' => url('/admin/trips')]

            
        );
    }
}