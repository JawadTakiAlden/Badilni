<?php

namespace App\Events;

use App\Http\Resources\API\V1\MessageResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $exchangeID;
    public $message;
    /**
     * Create a new event instance.
     */
    public function __construct($exchangeID , $message)
    {
        $this->exchangeID =$exchangeID;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.'.$this->exchangeID),
        ];
    }

//    public function boradcastWith() {
//        return [
//          'message' => MessageResource::make($this->message)
//        ];
//    }

    public function handle(){
        return [
            'message' => MessageResource::make($this->message)
        ];
    }
}
