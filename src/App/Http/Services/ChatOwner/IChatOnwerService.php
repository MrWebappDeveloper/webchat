<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\ChatOwner;

use MrWebappDeveloper\Webchat\App\Models\ChatOwner;

interface IChatOnwerService
{
    /**
     * @message Creates new ChatOwner record in chat_owners table
     * @param string $name
     * @param string $email
     * @param string|null $socketId
     * @return ChatOwner|false
     */
    public function register(string $name, string $email, string $socketId = null):ChatOwner|false;

    /**
     * @message Search for find owner by its email if these entries passed. Otherwise, try to find by current sessionId. If it doesn't find , return null
     * @param string|null $email
     * @return ChatOwner|null
     */
    public function search(string $email = null):ChatOwner|null;

    /**
     * Trys to find chat owner in database that has the entry socket id
     *
     * @param string $socketId
     * @return ChatOwner|null
     */
    public function findBySocketId(string $socketId):ChatOwner|null;

    /**
     * Defines that owner is online by set socket_id column
     * @param string $channelName
     * @param string $socket_id
     * @return bool
     */
    public function setOwnerAsOnline(string $channelName ,string $socket_id):bool;

    /**
     * Save chat owner new session id
     * @param ChatOwner $owner
     * @return bool
     */
    public function updateOwnerSessionId(ChatOwner $owner):bool;

    /**
     * Defines that owner is offline by set socket_id value column null
     * @param string $socket_id
     * @return bool
     */
    public function setOwnerAsOffline(string $socket_id):bool;
}
