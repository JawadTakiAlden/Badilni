<?php

namespace App\Listeners;

use App\Events\SendMessageEvent;
use App\Models\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMessageListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SendMessageEvent $event): void
    {
        $user = auth()->user();
            $message = Message::create([
                'content' => $event->message,
                'from_user_id' => $user->id,
                'to_user_id' => $event->toUser,
            ]);
    }
}
