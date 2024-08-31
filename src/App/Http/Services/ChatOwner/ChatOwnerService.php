<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\ChatOwner;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Models\ChatOwner;
use Modules\Webchat\Events\RefreshChatsItem;
use MrWebappDeveloper\Webchat\App\Http\Facade\ChatFacade;

class ChatOwnerService implements IChatOnwerService
{
    private function __construct(){}

    /**
     * @message Create and return new instance of the class
     * @return ChatOwnerService
     */
    public static function instance():ChatOwnerService
    {
        return new self;
    }

    /**
     * @message Creates new ChatOwner record in chat_owners table
     * @param string $name
     * @param string $email
     * @param string|null $socketId
     * @return ChatOwner|false
     */
    public function register(string $name, string $email, string $socketId = null): ChatOwner|false
    {
        try {
            $sessionId = ChatFacade::generateChatSessionId();

            if($chatOwner = ChatOwner::create([
                'name' => $name,
                'email' => $email,
                'session_id' => ChatFacade::generateChatSessionId(),
                'socket_id' => $socketId
            ]))
                session()->put(Config::get('webchat.chat_session_name'), $sessionId);

             return $chatOwner;
        } catch (\Exception $e){
            Log::error("Register new chat owner unsuccessful ! Error Message:\n" . $e->getMessage());

            return false;
        }
    }

    /**
     * @message Search for find owner by its email if these entries passed. Otherwise, try to find by current sessionId. If it doesn't find , return null
     * @param string|null $email
     * @return ChatOwner|null
     */
    public function search(string $email = null): ChatOwner|null
    {
        $session = \session(Config::get('webchat.chat_session_name'));

        if(!$email && !$session)
            return null;

        return ChatOwner::when($email && $session, function($query) use ($email, $session){
            $query->where('email',$email)->orWhere('session_id', $session);
        })->when($email && !$session, function($query) use ($email){
            $query->where('email', $email);
        })->when(!$email && $session, function ($query) use ($session){
            $query->where('session_id', $session);
        })->first();
    }

    /**
     * این متود با ثبت سوکت آیدی کاربر در جدول مربوطه ، کاربر را به عنوان آنلاین نشان می کند.
     * با قطع سوکت توسط کاربر ، این سوکت آیدی از روی جدول مربوطه پاک میشود و مقدار null قرار داده میشود
     * @param string $channelName
     * @param string $socket_id
     * @return bool
     */
    public function setOwnerAsOnline(string $channelName, string $socket_id): bool
    {
        try {
            if(!$chat = Chat::where('channel', $channelName)->first()){
                Log::error('Channel by ' . $channelName . ' not found for set owner as online !');
                return false;
            }

            if(!$owner = $chat->owner){
                Log::error('Owner of chat by ' . $channelName . ' name not found');
                return false;
            }

            $owner->update([
                'socket_id' => $socket_id
            ]);

            return true;
        } catch (\Exception $e){
            Log::error('Set owner as online for chat ' . $channelName . ' unsuccessful. Error :' . $e->getMessage());
            return false;
        }
    }

    /**
     * Save chat owner new session id
     * @param ChatOwner $owner
     * @return bool
     */
    public function updateOwnerSessionId(ChatOwner $owner):bool
    {
        $sessionId = ChatFacade::generateChatSessionId();


        if($owner->update([
            'session_id' => $sessionId
        ])){
            session()->put(Config::get('webchat.chat_session_name'), $sessionId);

            return true;
        }

        return false;
    }

    /**
     * Defines that owner is offline by set socket_id value column null
     * @param string $socket_id
     * @return bool
     */
    public function setOwnerAsOffline(string $socket_id): bool
    {
        try {
            $owner = ChatOwner::where('socket_id', $socket_id)->first();

            if($owner){
                $owner->socket_id = null;

                $owner->save();
            }

            return true;
        }catch (\Exception $e){
            Log::error('Set offline status for chat owner unsuccessful. Error:' . $e->getMessage());
            return false;
        }
    }

    /**
     * Trys to find chat owner in database that has the entry socket id
     *
     * @param string $socketId
     * @return ChatOwner|null
     */
    public function findBySocketId(string $socketId): ChatOwner|null
    {
        return ChatOwner::where('socket_id', $socketId)->first();
    }
}
