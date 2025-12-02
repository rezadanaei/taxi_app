<?php

namespace App\Services;

use App\Models\UserPushToken;
use Minishlink\WebPush\WebPush; 
use Minishlink\WebPush\Subscription;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;

class PushNotificationService
{
    /**
     * Send a notification to drivers
     *
     * @param $userId
     * @param string $title
     * @param string $body
     * @param array $data
     */

    public function sendToUsers( $userId, string $title, string $body, array $data = [])
    {
        $tokens = UserPushToken::where('user_id', $userId)->get();
        foreach ($tokens as $token) {
            if ($token->type === 'web_push') {
                $this->sendWebPush($token->token, $title, $body, $data);
            }

            if ($token->type === 'fcm') {
                $this->sendFCM($token->token, $title, $body, $data);
            }
        }
            
    }

    /**
     * Send Web Push
     */
    protected function sendWebPush(string $token, string $title, string $body, array $data = [])
    {
        // decode token as array
        $subscriptionData = json_decode($token, true);

        $subscription = Subscription::create($subscriptionData);

        $webPush = new WebPush([
            "VAPID" => [
                "subject" => "mailto:your-email@example.com",
                "publicKey" => env("VAPID_PUBLIC_KEY"),
                "privateKey" => env("VAPID_PRIVATE_KEY"),
            ],
        ]);

        $payload = json_encode([ 
            "title" => $title,
            "body"  => $body,
            "icon"  => asset("/img/fav.png"),
            "badge" => asset("/img/fav.png"),
            "data"  => $data,
        ]);

        $webPush->queueNotification($subscription, $payload);

        $results = [];
        
        foreach ($webPush->flush() as $report) {
            $results[] = [
                'endpoint' => $report->getRequest()->getUri()->__toString(),
                'success' => $report->isSuccess(),
                'reason' => $report->isSuccess() ? null : $report->getReason()
            ];
        }

        return ;
    }


    /**
     * Send FCM
     */
    protected function sendFCM(string $token, string $title, string $body, array $data)
    {
        $client = new GoogleClient();
        $client->setAuthConfig(storage_path('app/firebase_key.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];
        $projectId = config('services.fcm.project_id');

        $message = [
            "message" => [
                "token" => $token,
                "notification" => [
                    "title" => $title,
                    "body"  => $body
                ],
                "data" => $data,
            ]
        ];

        Http::withToken($accessToken)
            ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $message);
    }


    
}
