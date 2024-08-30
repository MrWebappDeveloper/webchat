<?php

namespace MrWebappDeveloper\Webchat\App\Http\Controllers\Facade;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class ChatFacade
{
    /**
     * session id چت را می سازد که از آن برای شناسایی کاربر
     * در درخواست های بعدی استفاده می شود
     *
     * @return string
     */
    public static function generateChatSessionId():string
    {
        return Str::random(Config::get('webchat.chat_session_id_length'));
    }
}
