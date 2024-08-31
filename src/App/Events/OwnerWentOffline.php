<?php

namespace MrWebappDeveloper\Webchat\App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use MrWebappDeveloper\Webchat\App\Http\Transformers\ChatItemResource;

class OwnerWentOffline implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public int $chat_id)
    {
        //
    }

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
        return Config::get('webchat.chat_owner_went_offline_event');
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(Config::get('webchat.admin_channel_name'))
        ];
    }
}
