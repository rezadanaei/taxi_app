<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\ChannelManager;
use App\Notifications\Channels\DriverDatabaseChannel;
use App\Notifications\Channels\DriverTripChannel;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->make(ChannelManager::class)->extend('driver_database', function ($app) {
            return new DriverDatabaseChannel();
        });

        $this->app->make(ChannelManager::class)->extend('driver_trip', function ($app) {
            return new DriverTripChannel();
        });
    }

}
