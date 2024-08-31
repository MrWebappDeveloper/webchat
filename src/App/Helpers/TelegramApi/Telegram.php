<?php

namespace MrWebappDeveloper\Webchat\App\Helpers\TelegramApi;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Telegram
{
    public function __construct(
        private string $bot_token,
        private string $chat_id)
    {
    }

    /**
     * Send message to Telegram chat through its API
     *
     * @param string $text
     * @return bool
     */
    public function sendMessage(string $text): bool
    {
        $response = Http::post("https://api.telegram.org/bot{$this->bot_token}/sendMessage", [
            'chat_id' => $this->chat_id,
            'text' => $text
        ]);

        if(!$response->json()['ok']){
            Log::error('Send message to telegram error !', $response->json());
            return false;
        }

        return true;
    }
}
