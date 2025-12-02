<?php

namespace App\Helpers;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;

class FCM
{
    public static function send($deviceToken, $title, $body, $data = [])
    {
        $client = new GoogleClient();
        $client->setAuthConfig(storage_path('app/firebase_key.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $token = $client->fetchAccessTokenWithAssertion()['access_token'];

        $projectId = config('services.fcm.project_id');

        $message = [
            "message" => [
                "token" => $deviceToken,
                "notification" => [
                    "title" => $title,
                    "body"  => $body
                ],
                "data" => $data
            ]
        ];

        return Http::withToken($token)
            ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $message)
            ->json();
    }
}


/*
*use App\Helpers\FCM;
*
*Route::get('/test-fcm', function () {
*    return FCM::send(
*        "DEVICE_TOKEN",
         "سلام کاربر عزیز",
"نوتیفیکیشن تست VIP Taxi ارسال شد!",        
*        ["type" => "test"]
*    );
*});
*/