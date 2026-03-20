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
            }
            else {
                Log::warning("No user found for notification ID: {$notification->id}");
            }
        });
    }

    protected function sendPushNotification($deviceToken, $title, $body)
    {
        $credentialsPath = base_path('firebase-service-account.json');

        if (!file_exists($credentialsPath)) {
            Log::error("FCM v1: Service account file NOT found at $credentialsPath. Push notification cancelled.");
            return;
        }

        try {
            $credentials = json_decode(file_get_contents($credentialsPath), true);
            $projectId = $credentials['project_id'];

            $accessToken = $this->getFCMV1AccessToken($credentialsPath);

            if (!$accessToken) {
                Log::error("FCM v1: Failed to fetch access token.");
                return;
            }

            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $payload = [
                'message' => [
                    'token' => $deviceToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                ],
            ];

            $headers = [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            Log::info("FCM v1 Response [HTTP $httpCode] for $deviceToken: " . $response);

        }
        catch (\Exception $e) {
            Log::error("FCM v1 Error: " . $e->getMessage());
        }
    }

    protected function getFCMV1AccessToken($credentialsPath)
    {
        try {
            $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
            $credentials = new \Google\Auth\Credentials\ServiceAccountCredentials($scopes, $credentialsPath);
            $token = $credentials->fetchAuthToken();
            return $token['access_token'] ?? null;
        }
        catch (\Exception $e) {
            Log::error("FCM Token Error: " . $e->getMessage());
            return null;
        }
    }
}