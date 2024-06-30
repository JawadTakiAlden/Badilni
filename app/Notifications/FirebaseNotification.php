<?php

namespace App\Notifications;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class FirebaseNotification
{
    protected $messaging;

    public function __construct()
    {

        $firebase = (new Factory())
            ->withServiceAccount(config_path('firebase_credentials.json'));

        $this->messaging = $firebase->createMessaging();
    }
    public function BasicSendNotification($title ,$body, $FcmToken , $data)
    {
        $notification = Notification::create($title, $body);
        foreach ($FcmToken as $token) {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData($data);
            try {
                $this->messaging->send($message);
            } catch (\Exception $e) {
                // Log or handle the exception as needed
                Log::error('Failed to send notification: ' . $e->getMessage());
            }
        }
    }

}
