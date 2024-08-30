<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\ChatMessage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;
use MrWebappDeveloper\Webchat\App\Models\ChatOwner;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Facade\MessageFacade;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\StoreMessageRquest;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\UpdateMessageRequest;

use SebastianBergmann\Diff\Exception;

class ChatMessageService implements IChatMessageService
{

    public function __construct()
    {
    }

    /**
     * @message Create and return new instance of the class
     * @return ChatMessageService
     */
    #[Pure] public static function instance(): ChatMessageService
    {
        return new self;
    }

    /**
     * @message Store new message for the $chat
     * @param StoreMessageRquest $request
     * @param Chat $chat
     * @return Model
     */
    public function storageNewMessage(StoreMessageRquest $request, Chat $chat): Model
    {
        return $chat->messages()->create([
            'sender' => $request->input('role'),
            'content' =>
                $request->hasFile('file') ?
                    MessageFacade::storeFileMessageStructure($request->file('file')) :
                    MessageFacade::storeTextMessageStructure($request->input('text')),
        ]);
    }

    /**
     * @message Update message content
     * @param UpdateMessageRequest $request
     * @param ChatMessage $message
     * @return bool
     */
    public function updateMessage(UpdateMessageRequest $request, ChatMessage $message): bool
    {
        MessageFacade::removeMessageFile($message); // delete message file if its type is file from server storage

        return $message->update([
            'content' =>
                $request->hasFile('file') ?
                    MessageFacade::storeFileMessageStructure($request->file('file')) :
                    MessageFacade::storeTextMessageStructure($request->input('text')),
        ]);
    }

    /**
     * Delete message
     *
     * @param ChatMessage $message
     * @return bool
     */
    public function deleteMessage(ChatMessage $message): bool
    {
        MessageFacade::removeMessageFile($message);

        return $message->delete();
    }

    /**
     * @message Changes messages of entry role status to seen in database for entry channel name
     * @param string $channelName
     * @param string $role
     * @return bool
     */
    public function messagesSeen(string $channelName, string $role): bool
    {
        try {
            if(!$chat = Chat::where('channel', $channelName)->first()){
                Log::error('Chat not found by ' . $channelName . ' name for set seen status messages of ' . $role . ' role');
                return false;
            }

            return ChatMessage::where('chat_id', $chat->id)->where('sender', '!=' , $role)->update([
                'status' => 'seen'
            ]);

        } catch(Exception $e){
            Log::error('Change messages status of ' . $channelName . ' chat channel to seen for ' . $role . ' role , unsuccessful !');
            return false;
        }
    }

    /**
     * Creates new message by set (content) field directly from ($content) argument and with admin role
     *
     * @param Chat $chat
     * @param array|string $content is an array that contains message (type) and (text) if type is 'text' and (path | filename | format) if type is 'file'
     * @return Model|false
     */
    public function createNewMessageWithDirectContent(Chat $chat, array|string $content):Model|false
    {
        return $chat->messages()->create([
            'sender' => ChatMessage::adminRoleName(),
            'content' => $content,
        ]);
    }
}
