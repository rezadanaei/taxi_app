<?php

namespace App\Jobs;

use App\Facades\SMS;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Trip;
use App\Models\Admin;
use App\Services\AdminNotificationService;

class NotifyTripAdmins implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    protected $trip;

    public function __construct(Trip $trip)
    {
        $this->trip = $trip;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $trip = $this->trip;

        if ($trip->driver_id) {
            return;
        }

        $adminPhones = Admin::pluck('phone')->toArray();

       foreach ($adminPhones as $adminPhone) {
            // SMS::send(
            //     $adminPhone,
            //     "هشدار: سفر بدون راننده\nیک سفر ثبت شده ولی هیچ راننده‌ای ندارد. لطفاً بررسی کنید."
            // );
            
        }

        $title = "هشدار: سفر بدون راننده";
        $body = "یک سفر ثبت شده ولی هیچ راننده‌ای ندارد. لطفاً بررسی کنید.";

        $data = [
            'url' => url('/admin/trips'),
        ];

    
        app(AdminNotificationService::class)->create( $title, $body, $data);
    }
}
