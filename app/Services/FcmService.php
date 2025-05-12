<?php

namespace App\Services;

use Google\Client as GoogleClient;
use App\Models\Pengguna;

class FcmService
{
    public static function sendNotification($fcmToken, $title, $body)
    {
        $credentialFilePath = storage_path('app/json/p3l-pushnotification-firebase-adminsdk-fbsvc-47f9ee96d0.json');
        $credentials = json_decode(file_get_contents($credentialFilePath), true);
        $projectId = $credentials['project_id'];

        $client = new GoogleClient();
        $client->setAuthConfig($credentialFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken()['access_token'];

        $headers = [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ];

        $data = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ]
            ],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'message' => $err];
        }

        return ['success' => true, 'response' => json_decode($response, true)];
    }
}
