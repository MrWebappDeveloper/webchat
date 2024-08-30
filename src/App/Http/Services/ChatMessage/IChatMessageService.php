<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\ChatMessage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\StoreMessageRquest;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\UpdateMessageRequest;
use SebastianBergmann\Diff\Exception;

interface IChatMessageService
{
    /**
     * @message Store new message for the $chat
     * @param StoreMessageRquest $request
     * @param Chat $chat
     * @return Model
     */
    public function storageNewMessage(StoreMessageRquest $request, Chat $chat): Model;

    /**
     * @message Update message content
     * @param UpdateMessageRequest $request
     * @param ChatMessage $message
     * @return bool
     */
    public function updateMessage(UpdateMessageRequest $request, ChatMessage $message): bool;

    /**
     * Delete message
     *
     * @param ChatMessage $message
     * @return bool
     */
    public function deleteMessage(ChatMessage $message): bool;

    /**
     * @message Changes messages of entry role status to seen in database for entry channel name
     * @param string $channelName
     * @param string $role
     * @return bool
     */
    public function messagesSeen(string $channelName, string $role): bool;

    /**
     * Creates new message by set (content) field directly from ($content) argument
     *
     * @param Chat $chat
     * @param array|string $content is an array that contains message (type) and (text) if type is 'text' and (path | filename | format) if type is 'file'
     * @return Model|false
     */
    public function createNewMessageWithDirectContent(Chat $chat, array|string $content):Model|false;
}
