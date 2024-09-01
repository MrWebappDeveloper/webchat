<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\CardMessage;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MrWebappDeveloper\Webchat\App\Models\Card;
use MrWebappDeveloper\Webchat\App\Models\CardMessage;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;
use MrWebappDeveloper\Webchat\App\Http\Facade\CardMessageFacade;
use MrWebappDeveloper\Webchat\App\Http\Facade\MessageFacade;

class CardMessageService implements ICardMessage
{

    public function __construct()
    {
    }

    /**
     * @message Create and return new instance of the class
     * @return CardMessageService
     */
    public static function instance(): CardMessageService
    {
        return new self;
    }

    /**
     * Creates new message for the entry card argument model
     *
     * @param Card $card
     * @param string|UploadedFile $message
     * @param int|null $send_order_index
     * @return bool
     */
    public function createMessage(Card $card, string|UploadedFile $message, ?int $send_order_index = null): bool
    {
        DB::beginTransaction();

        $data = [
            'content' => (strtolower(gettype($message)) == 'object' && $message::class == 'Illuminate\Http\UploadedFile' ?
                MessageFacade::storeFileMessageStructure($message) :
                MessageFacade::storeTextMessageStructure($message))
        ];

        if($send_order_index !== null)
            $data = array_merge($data, ['send_order_index' => $send_order_index]);

        if (!$card->messages()->create($data)) {
            Log::error('Create card`s message unsuccessful !', debug_backtrace());
            return false;
        }

        DB::commit();

        return true;
    }

    /**
     * Resets the entry card argument messages by remove its messages and create and assign new messages that enter in messagesContent argument
     *
     * @param Card $card
     * @param array $messages should be associate array compound of : required (value, send_order_index) optional (id)
     * @return bool
     */
    public function updateMessages(Card $card, array $messages): bool
    {
        $isSuccess = true;

        DB::beginTransaction();

        if(!CardMessageFacade::removeDiffs($card, array_column($messages, 'id'))) // remove all messages that there is no in update list ids from db
            $isSuccess = false;

        if($isSuccess){
            foreach ($messages as $message)
                if(isset($message['id'])){
                    if($cardMessage = CardMessage::find($message['id']))
                        $this->updateMessage($cardMessage, $message['value'], $message['send_order_index']);
                }
                else
                    $this->createMessage($card, $message['value'], $message['send_order_index']);

        }

        DB::commit();

        return true;
    }


    /**
     * Removes card message from database
     *
     * @param CardMessage $cardMessage
     * @return bool
     */
    public function deleteMessage(CardMessage $cardMessage): bool
    {
        if (!$cardMessage->delete()) {
            Log::error("Delete card`s message unsuccessful !", debug_backtrace());
            return false;
        }

        return true;
    }

    /**
     * Removes the card messages from database
     *
     * @param Card $card
     * @return bool
     */
    public function deleteCardMessages(Card $card): bool
    {
        if ($card->messages()->count() == 0)
            return true;

        if (!$card->messages()->delete()) {
            Log::error('Delete card`s messages unsuccessful !', debug_backtrace());
            return false;
        }

        return true;
    }

    /**
     * Creates new messages for the entry card argument model
     *
     * @param Card $card
     * @param array $messagesValue Context of message
     * @param array|null $messagesOrderIndex send_order_index column value that define send order of the card messages
     * @return bool
     */
    public function createMessages(Card $card, array $messagesValue, ?array $messagesOrderIndex = null): bool
    {
        DB::beginTransaction();

        foreach ($messagesValue as $index => $value){
            if(!$this->createMessage($card, $value, (count($messagesValue) == count($messagesOrderIndex) ? $messagesOrderIndex[$index] : null)))
                return false;
        }

        DB::commit();

        return true;
    }

    /**
     * Updates card message and set new value and send order index for that
     *
     * @param CardMessage $message
     * @param string|UploadedFile $value
     * @param int $send_order_index
     * @return bool
     */
    public function updateMessage(CardMessage $message, string|UploadedFile $value, int $send_order_index): bool
    {
        $data = [];

        if((strtolower(gettype($value)) == 'object' && $value::class == UploadedFile::class) || $message->value !== $value){
            if($message->type === ChatMessage::FILE_MESSAGE_CONTENT_ARG_TYPE_NAME)
                MessageFacade::deleteFile($message->path);

            $data = [
                'content' => (strtolower(gettype($value)) == 'object' && $value::class === UploadedFile::class ?
                    MessageFacade::storeFileMessageStructure($value) :
                    MessageFacade::storeTextMessageStructure($value))
            ];
        }

        $data['send_order_index'] = $send_order_index;

        return $message->update($data);
    }

    /**
     * Returns the card's messages
     *
     * @param Card $card
     * @return Collection
     */
    public function getMessages(Card $card): Collection
    {
        return $card->messages;
    }
}
