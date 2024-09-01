<?php

namespace MrWebappDeveloper\Webchat\App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use MrWebappDeveloper\Webchat\App\Http\Transformers\WizardCollection;

class SendWizardMenuEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        private string $channelName,
        private WizardCollection $wizards)
    {}

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
        return Config::get('webchat.send_wizard_menu_event');
    }

    /**
     * Defines data array that should broadcast
     *
     * @return array
     */
    public function broadcastWith():array
    {
        return [
            'wizards' => $this->wizards
        ];
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel($this->channelName)
        ];
    }
}
