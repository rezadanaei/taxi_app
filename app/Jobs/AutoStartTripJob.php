<?php

namespace App\Jobs;

use App\Models\Trip;
use App\Models\TripLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Carbon\Carbon;

class AutoStartTripJob implements ShouldQueue
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

        if (! $trip) {
            return;
        }

        if ($trip->status !== 'paid') {
            return;
        }

        $fromTime = Carbon::now()->subMinutes(3);

        $hasRejectLog = TripLog::where('trip_id', $trip->id)
            ->where('action', 'passenger_interacted')
            ->where('created_at', '>=', $fromTime)
            ->exists();

        if ($hasRejectLog) {
            return;
        }

        $trip->update([
            'status' => 'ongoing',
        ]);
    }
}
