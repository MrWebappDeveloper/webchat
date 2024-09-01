<?php

namespace MrWebappDeveloper\Webchat\App\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;
use MrWebappDeveloper\Webchat\App\Http\Services\Notifications\Telegram\TelegramNotify;

class NotifyRecentNewMessageToTelegram implements ShouldQueue
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
     * Returns chat model instance of that's event dispatched for
     *
     * @param string $channelToken
     * @return Chat|null
     */
    private function chat(string $channelToken):?Chat
    {
        return Chat::where('token', $channelToken)->first();
    }

    /**
     * It shouldn't send a Telegram notify message for each new message that stores in system
     * So this function is for check limitation send message condition. Such as conditioning
     * regarding compare one left to last message create-at with new message create-at
     *
     * @param $event
     * @return bool
     */
    private function condition($event):bool
    {
        if(!$chat = $this->chat($event->channelToken))
            return false;

        $lastMessage = $chat->messages()->user()->orderBy('id', 'DESC')->skip(1)->take(1)->first();

        if (!$lastMessage)
            return false;

        $newMessage = $chat->messages()->latest()->first();

        return ($newMessage->sender == ChatMessage::userRoleName() && $newMessage->created_at->diffInHours($lastMessage->created_at) >= Config::get('webchat.send_new_message_notification_limit_hours'));
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        if($this->condition($event)){
            if(!$this->telegramNotify->send("New messages", "There are new messages in user chat !")){
                Log::error('Send notification for new chat to Telegram error !');
                $this->fail();
            }
        }
    }
}
