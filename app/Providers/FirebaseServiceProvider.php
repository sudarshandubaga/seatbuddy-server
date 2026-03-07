<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Notification;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Notification::created(function ($notification) {
            $user = $notification->user;
            if ($user) {
                $devices = \App\Models\Device::where('user_id', $user->id)
                    ->whereNotNull('device_token')
                    ->where('device_token', '!=', '')
                    ->get();

                foreach ($devices as $device) {
                    $this->sendPushNotification(
                        $device->device_token,
                        $notification->title,
                        $notification->description
                    );
                }
            }
        });
    }

    protected function sendPushNotification($token, $title, $body)
    {
        // Place for Firebase Admin SDK or direct API call
        // For now, logging the action
        \Log::info("Sending push to $token: $title - $body");
    }
}
