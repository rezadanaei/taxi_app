<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AutoEndTripJob implements ShouldQueue
{
    use Queueable;

    protected int $tripId;

    public function __construct(int $tripId)
    {
        $this->tripId = $tripId;
    }

    public function handle(): void
    {
        $trip = Trip::find($this->tripId);

        if (! $trip || $trip->status !== 'ongoing') {
            return;
        }

        $fromTime = now()->subMinutes(3);

        $hasRejectLog = TripLog::where('trip_id', $trip->id)
            ->where('action', 'passenger_end_interacted')
            ->where('created_at', '>=', $fromTime)
            ->exists();

        if ($hasRejectLog) {
            return;
        }

        $trip->update([
            'status' => 'completed'
        ]);
    }
}
