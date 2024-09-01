<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\ChatMessage;

use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Pure;
use MrWebappDeveloper\Webchat\App\Events\MessagesSeen;
use MrWebappDeveloper\Webchat\App\Events\NewChat;
use MrWebappDeveloper\Webchat\App\Events\NewMessage;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;
use MrWebappDeveloper\Webchat\App\Http\Requests\StoreMessageRquest;
use MrWebappDeveloper\Webchat\App\Http\Requests\UpdateMessageRequest;
use MrWebappDeveloper\Webchat\App\Http\Services\Chat\ChatService;
use MrWebappDeveloper\Webchat\App\Http\Transformers\ChatItemResource;
use MrWebappDeveloper\Webchat\App\Http\Transformers\ChatMessageResource;

class ChatMessageServiceProxy implements IChatMessageService
{
    /**
     * @message Service class instance
     * @var IChatMessageService
     */
    private IChatMessageService $chatMessageService;

    private ChatService $chatService;

    /**
     * @message Constructor
     */
    #[Pure] public function __construct()
    {
        $this->chatMessageService = ChatMessageService::instance();

        $this->chatService = ChatService::instance();
    }

    /**
     * @message Store new message method proxy
     * @param StoreMessageRquest $request
     * @param Chat $chat
     * @return Model
     */
    public function storageNewMessage(StoreMessageRquest $request, Chat $chat): Model
    {
        $message = $this->chatMessageService->storageNewMessage($request, $chat);

        broadcast(new NewMessage($chat->token, (new ChatMessageResource($message))))->toOthers();

        return $message;
    }

    /**
     * @message Update message content
     * @param UpdateMessageRequest $request
     * @param ChatMessage $message
     * @return bool
     */
    public function updateMessage(UpdateMessageRequest $request, ChatMessage $message): bool
    {
        return $this->chatMessageService->updateMessage($request, $message);
    }

    /**
     * @message Proxy for delete message operation
     * @param ChatMessage $message
     * @return bool
     */
    public function deleteMessage(ChatMessage $message): bool
    {
        return $this->chatMessageService->deleteMessage($message);
    }

    /**
     * @message Proxy for change messages to seen status operation
     * @param string $channelName
     * @param string $role
     * @return bool
     */
    public function messagesSeen(string $channelName, string $role): bool
    {
        if($res = $this->chatMessageService->messagesSeen($channelName, $role))
            broadcast(new MessagesSeen($channelName))->toOthers();

        return $res;
    }

    /**
     * Creates new message by set (content) field directly from ($content) argument
     *
     * @param Chat $chat
     * @param array|string $content is an array that contains message (type) and (text) if type is 'text' and (path | filename | format) if type is 'file'
     * @return Model|false
     */
    public function createNewMessageWithDirectContent(Chat $chat, array|string $content):Model|false
    {
        if($message = $this->chatMessageService->createNewMessageWithDirectContent($chat, $content))
            broadcast(new NewMessage($chat->token, (new ChatMessageResource($message))));

        return $message;
    }
}
