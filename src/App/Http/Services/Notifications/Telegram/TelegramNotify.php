<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\Notifications\Telegram;

use Illuminate\Support\Facades\Config;
use MrWebappDeveloper\Webchat\App\Helpers\TelegramApi\Telegram;
use MrWebappDeveloper\Webchat\App\Http\Services\Notifications\INotification;

class TelegramNotify implements INotification
{
    /**
     * Returns instance of Telegram helper for easy send message to it
     *
     * @return Telegram
     */
    private function telegram():Telegram
    {
        return new Telegram(Config::get('webchat.telegram_bot_token'), Config::get('webchat.webchat_telegram_channel_chat_id'));
    }

    /**
     * Sends notification context to notify API service
     *
     * @param string $title
     * @param string $message
     * @return bool
     */
    public function send(string $title, string $message): bool
    {
        return $this->telegram()->sendMessage("** " . $title . " **" . "\n\n" . $message);
    }
}
