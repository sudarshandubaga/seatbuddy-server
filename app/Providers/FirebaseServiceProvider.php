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
        $serverKey = env('FCM_SERVER_KEY');
        if (!$serverKey) {
            \Log::warning("FCM_SERVER_KEY not set. Skipping push to $token: $title - $body");
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

        \Log::info("FCM Response for $token: " . $response);
    }
}
