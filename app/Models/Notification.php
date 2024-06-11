<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'notifications';

    public static function BasicSendNotification($title, $body, $FcmToken)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $server_key = env('firebase_server_key');
        $date = [
            'registration_ids' => $FcmToken,
            'notification' => [
                'title' => $title,
                'body' => $body
            ]
        ];

        $encodedData = json_encode($date);

        $headers = [
            'Authorization: key=' . $server_key,
            'Content-Type: application/json'
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

        $result = curl_exec($ch);
//
//        if ($result === FALSE) {
//            return HelperFunction::ServerErrorResponse();
//        }
//        curl_close($ch);
//        return $this->success($result, __('messages.notification_controller.send_successfully'));
    }

    protected static function boot()
    {
        parent::boot();
        static::created(function ($notification) {
            $notificationToken = $notification->user->userDevices->pluck('notification_token');
            self::BasicSendNotification($notification->title , $notification->body , $notificationToken);
        });
    }

    public function user(){
        return $this->belongsTo(User::class , 'notified_user_id');
    }
}
