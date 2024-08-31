<?php

namespace MrWebappDeveloper\Webchat\App\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use MrWebappDeveloper\Webchat\App\Helpers\TelegramApi\Telegram;
use MrWebappDeveloper\Webchat\App\Http\Services\Notifications\Telegram\TelegramNotify;

class NotifyNewChatToTelegram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Telegram notify service instance
     *
     * @var TelegramNotify
     */
    private TelegramNotify $telegramNotify;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->telegramNotify = new TelegramNotify();
    }

    /**
     * Define queue name
     *
     * @return string
     */
    public function viaQueue(): string
    {
        return Config::get('webchat.jobs_queue_name');
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        if(!$this->telegramNotify->send("New Chat", "A new chat created some moment ago !")){
            $this->fail();
            Log::error('Send notification for new chat to Telegram error !');
        }
    }
}
