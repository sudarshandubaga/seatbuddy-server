<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;

class FirebaseServiceProvider extends ServiceProvider
{
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
            
            // Get Access Token via Standalone JWT logic to avoid "Class not found" errors
            $accessToken = $this->getFCMV1AccessTokenStandalone($credentials);

            if (!$accessToken) {
                Log::error("FCM v1: Failed to fetch access token.");
                return;
            }

            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            // Add Logo URL if available
            // If you have a public logo URL, replace the image string below
            $logoUrl = "https://seatbuddy.digihawkapps.com/images/logo.png"; 

            $payload = [
                'message' => [
                    'token' => $deviceToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'image' => $logoUrl
                    ],
                    'data' => [
                        'screen' => 'Notifications',
                        'title' => $title,
                        'body' => $body
                    ],
                    'android' => [
                        'notification' => [
                            'image' => $logoUrl,
                            'sound' => 'default',
                            'click_action' => 'OPEN_NOTIFICATIONS_SCREEN'
                        ]
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'mutable-content' => 1,
                                'sound' => 'default'
                            ]
                        ],
                        'fcm_options' => [
                            'image' => $logoUrl
                        ]
                    ]
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

        } catch (\Exception $e) {
            Log::error("FCM v1 Error: " . $e->getMessage());
        }
    }

    /**
     * Helper for base64url encoding as required by JWT
     */
    protected function base64UrlEncode($data)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    /**
     * Standalone JWT token generator for Google OAuth2
     * This avoids using Google's Client Library to prevent "Class not found" errors on servers.
     */
    protected function getFCMV1AccessTokenStandalone($credentials)
    {
        $now = time();
        $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = $this->base64UrlEncode(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ]));

        $headerPayload = $header . '.' . $payload;
        $signature = '';
        if (!openssl_sign($headerPayload, $signature, $credentials['private_key'], 'sha256')) {
            Log::error("FCM Standalone: Signature failed.");
            return null;
        }

        $jwt = $headerPayload . '.' . $this->base64UrlEncode($signature);

        $url = 'https://oauth2.googleapis.com/token';
        $postData = "grant_type=urn:ietf:params:oauth:grant-type:jwt-bearer&assertion=" . urlencode($jwt);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }
}
