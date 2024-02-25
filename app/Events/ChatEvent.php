<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ChatEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $message;
    public $token;
    /**
     * Create a new event instance.
     */
    public function __construct(Message $message,$token)
    {
        $this->message = $message;
        $this->token = $token;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new Channel('chat'.$this->token);
    }
    public function broadcastWith()
    {
        return [
            'content'=> $this->message->content,
            'sender_type'=>$this->message->sender_type,
            'created_at'=>$this->message->created_at,
            'sender_id' =>$this->message->sender_id,
            'recipient_id'=>$this->message->recipient_id
        ];;
    }
}
