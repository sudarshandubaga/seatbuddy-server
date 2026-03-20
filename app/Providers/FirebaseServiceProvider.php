<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
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
        Log::debug("FirebaseServiceProvider bootstrapping...");
        Notification::created(function ($notification) {
            Log::info("Notification created event fired for ID: {$notification->id}");
            $user = $notification->user;
            if ($user) {
                Log::info("Target user found: {$user->id} ({$user->name})");
                $devices = \App\Models\Device::where('user_id', $user->id)
                    ->whereNotNull('device_token')
                    ->where('device_token', '!=', '')
                    ->get();

                Log::info("Found " . $devices->count() . " devices for user {$user->id}");

                foreach ($devices as $device) {
                    $this->sendPushNotification(
                        $device->device_token,
                        $notification->title,
                        $notification->description
                    );
                }
            } else {
                Log::warning("No user found for notification ID: {$notification->id}");
            }
        });
    }

    protected function sendPushNotification($token, $title, $body)
    {
        $serverKey = env('FCM_SERVER_KEY');
        if (!$serverKey) {
            Log::error("CRITICAL: FCM_SERVER_KEY is NOT set in .env. Push notification cancelled.");
            Log::warning("Skipping push to $token: $title - $body");
            return;
        }

        $data = [
            "to" => $token,
            "notification" => [
                "title" => $title,
                "body" => $body,
                "sound" => "default"
            ]
        ];
        
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        
        $response = curl_exec($ch);
        curl_close($ch);

        Log::info("FCM Response for $token: " . $response);
    }
}
