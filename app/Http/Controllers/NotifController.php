<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Storage;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;

class NotifController extends Controller
{
    public function sendFcmNotification(Request $request)
    {
        $request->validate([
            'id_pengguna' => 'required|exists:penggunas,id_pengguna',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);
        
        $user = Pengguna::find($request->id_pengguna);
        $fcmToken = $user->fcm_token;

        if (!$fcmToken) {
            return response()->json(['message' => 'FCM token tidak ditemukan'], 404);
        }

        $title = $request->title;
        $description = $request->body;
        
        $credentialFilePath = storage_path('app/json/p3l-pushnotification-firebase-adminsdk-fbsvc-47f9ee96d0.json');
        $credentialFilePath = storage_path('app/json/p3l-pushnotification-firebase-adminsdk-fbsvc-47f9ee96d0.json');

        $credentials = json_decode(file_get_contents($credentialFilePath), true);
        $projectId = $credentials['project_id'];
        
        $client = new GoogleClient();
        $client->setAuthConfig($credentialFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken()['access_token'];
        
        
        $headers=[
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ];

        $data = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $description,
                ]
            ],
        ];
        $payload = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return response()->json([
                'message' => 'Curl Error: ' . $err
            ], 500);
        } else {
            return response()->json([
                'message' => 'Notification has been sent',
                'response' => json_decode($response, true)
            ]);
        }
    }
}
