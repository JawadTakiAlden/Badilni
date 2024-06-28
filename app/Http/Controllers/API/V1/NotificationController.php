<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\NotificationResource;
use App\HttpResponse\HTTPResponse;
use App\Models\Notification;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class NotificationController extends Controller
{
    use HTTPResponse;
    public function myNotification(){
        try {
            if (\request('flag') === 'unread'){
                $notifications = Notification::where('notified_user_id' , auth()->user()->id)
                    ->where('is_read' , false)
                    ->get();
                return $this->success(NotificationResource::collection($notifications));
            }
            $notifications = Notification::where('notified_user_id' , auth()->user()->id)
                ->orderBy('created_at' , 'desc')
                ->get();
            return $this->success(NotificationResource::collection($notifications));
        }catch (\Throwable $th){
            return $this->serverError();
        }
    }

    public function numberOfUnReadNotification(){
        try {
            return $this->success([
                'un_read_notification' =>  \request()->user()->unReadNotification()
            ]);
        }catch (\Throwable $th){
            return $this->serverError();
        }
    }

    public static function BasicSendNotification($title , array $notificationData, $FcmToken)
    {
        $firebase = (new Factory())
            ->withServiceAccount(__DIR__.'/../../config/firebase_credentials.json');

        $messaging = $firebase->createMessaging();


        $notification = FirebaseNotification::create($title, json_encode($notificationData));
        // Loop through each FCM token and send the notification
        foreach ($FcmToken as $token) {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification);
            try {
                $messaging->send($message);
            } catch (\Exception $e) {
                // Log or handle the exception as needed
                error_log('Failed to send notification: ' . $e->getMessage());
            }
        }
    }

}
