<?php

namespace App\Events;

use App\Http\Resources\API\V1\MessageResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $exchangeID;
    public $message;
    public $recipientId;
    /**
     * Create a new event instance.
     */
    public function __construct($exchangeID , $recipientId , $message)
    {
        $this->exchangeID =$exchangeID;
        $this->message = $message;
        $this->recipientId = $recipientId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            'conversation.'.$this->exchangeID.'.'.$this->recipientId,
        ];
    }

    public function boradcastWith() {
        return [
          'message' => MessageResource::make($this->message)
        ];
    }
}
