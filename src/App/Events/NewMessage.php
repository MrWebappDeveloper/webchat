<?php

namespace MrWebappDeveloper\Webchat\App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use MrWebappDeveloper\Webchat\App\Http\Transformers\ChatMessageResource;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public string $channelToken,
        public ChatMessageResource $message
    ){}

    /**
     * The name of the queue on which to place the broadcasting job.
     */
    public function broadcastQueue(): string
    {
        return Config::get('webchat.jobs_queue_name');
    }

    /**
     * Define custom name for event
     *
     * @return string
     */
    public function broadcastAs():string
    {
        return Config::get('webchat.new_message_event');
    }

    /**
     * Defines data array that should broadcast
     *
     * @return array
     */
    public function broadcastWith():array
    {
        return [
            'message' => $this->message
        ];
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [
            new Channel(config('webchat.channel_name_prefix') . $this->channelToken)
        ];
    }
}
