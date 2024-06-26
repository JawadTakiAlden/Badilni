<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class Notification extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'notifications';

    public static function BasicSendNotification($title, $body, $FcmToken)
    {
        $firebase = (new Factory())
            ->withServiceAccount(__DIR__.'/../../config/firebase_credentials.json');

        $messaging = $firebase->createMessaging();


        $notification = FirebaseNotification::create($title, $body);
        dd($FcmToken);
        // Loop through each FCM token and send the notification
        foreach ((array)$FcmToken as $token) {
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
