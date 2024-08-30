<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\ChatOwner;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Modules\Webchat\app\Events\ClientStatusChanged;
use Modules\Webchat\app\Events\OwnerWentOffline;
use Modules\Webchat\app\Events\OwnerWentOnline;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Models\ChatOwner;

class ChatOwnerServiceProxy implements IChatOnwerService
{
    private ChatOwnerService $chatOwnerService;

    public function __construct()
    {
        $this->chatOwnerService = ChatOwnerService::instance();
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
        if($owner = ChatOwner::where('email', $email)->orWhere('session_id', session(Config::get('webchat.chat_session_name')))->first())
            return $owner;


        return $this->chatOwnerService->register($name, $email, $socketId);
    }

    /**
     * @message Search for find owner by its email or mobile if these entries passed. Otherwise, try to find by current sessionId. If it doesn't find , return null
     * @param string|null $email
     * @return ChatOwner|null
     */
    public function search(string $email = null): ChatOwner|null
    {
        return $this->chatOwnerService->search($email);
    }

    /**
     * @message Defines that owner is online by set socket_id column
     * @param string $channelName
     * @param string $socket_id
     * @return bool
     */
    public function setOwnerAsOnline(string $channelName, string $socket_id): bool
    {
        if(!$this->chatOwnerService->setOwnerAsOnline($channelName, $socket_id))
            return false;

        if($chat = Chat::where("channel", $channelName)->first())
            OwnerWentOnline::dispatch($chat->id);

        return true;
    }

    /**
     * Save chat owner new session id
     * @param ChatOwner $owner
     * @return bool
     */
    public function updateOwnerSessionId(ChatOwner $owner):bool
    {
        return $this->chatOwnerService->updateOwnerSessionId($owner);
    }

    /**
     * Defines that owner is offline by set socket_id value column null
     * @param string $socket_id
     * @return bool
     */
    public function setOwnerAsOffline(string $socket_id): bool
    {
        $owner = $this->findBySocketId($socket_id);

        if(!$this->chatOwnerService->setOwnerAsOffline($socket_id))
            return false;

        if($owner)
            if($owner->chat && $owner->chat->hasAnyMessage)
                OwnerWentOffline::dispatch($owner->chat->id);

        return true;
    }

    /**
     * Trys to find chat owner in database that has the entry socket id
     *
     * @param string $socketId
     * @return ChatOwner|null
     */
    public function findBySocketId(string $socketId): ChatOwner|null
    {
        return $this->chatOwnerService->findBySocketId($socketId);
    }
}
