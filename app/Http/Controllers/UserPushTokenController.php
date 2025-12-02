<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Minishlink\WebPush\Subscription;
use App\Models\UserPushToken;
use Minishlink\WebPush\WebPush;
use App\Models\User;
use App\Services\PushNotificationService;

use App\Services\AdminNotificationService;


class UserPushTokenController extends Controller
{


    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:web_push,fcm',
            'token' => 'required|string'
        ]);

        $user = Auth::user();

        if ($request->type === 'web_push') {                

            $pushToken = UserPushToken::updateOrCreate(
                ['user_id' => $user->id, 'type' => $request->type],
                    ['token' => $request->token]
            );

            return response()->json([
                'success' => true,
                'message' => 'Push token saved successfully',
                'data' => ''
            ]);
        };
    }
    public function send(){
        $Usertoken = User::first();
        
        $pushService = new PushNotificationService();
        $pushService->sendToUsers(
            $Usertoken->id,
            "Test Notification",
            "This is a test notification sent from the server.",
            ["url" => "/profile"] 
        );
        
    }



}