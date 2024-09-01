<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\CardMessage;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use MrWebappDeveloper\Webchat\App\Models\Card;
use MrWebappDeveloper\Webchat\App\Models\CardMessage;
use MrWebappDeveloper\Webchat\App\Http\Services\Card\CardService;
use MrWebappDeveloper\Webchat\App\Http\Services\Card\ICardService;

class CardMessageServiceProxy implements ICardMessage
{
    private ICardMessage $service;

    /**
     * Proxy constructor that initial properties and services
     */
    public function __construct()
    {
        $this->service = CardMessageService::instance();
    }

    /**
     * Creates new message for the entry card argument model
     *
     * @param Card $card
     * @param array $messagesValue
     * @param array $messagesOrderIndex
     * @return bool
     */

    /**
     * Resets the entry card argument messages by remove its messages and create and assign new messages that enter in messagesContent argument
     *
     * @param Card $card
     * @param array $messages should be associate array compound of : required (value, send_order_index) optional (id)
     * @return bool
     */
    public function updateMessages(Card $card, array $messages): bool
    {
        return $this->service->updateMessages($card, $messages);
    }

    /**
     * Removes card message from database
     *
     * @param CardMessage $cardMessage
     * @return bool
     */
    public function deleteMessage(CardMessage $cardMessage): bool
    {
        return $this->service->deleteMessage($cardMessage);
    }

    /**
     * Removes the card messages from database
     *
     * @param Card $card
     * @return bool
     */
    public function deleteCardMessages(Card $card): bool
    {
        return $this->service->deleteCardMessages($card);
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
        return $this->service->createMessages($card, $messagesValue, $messagesOrderIndex);
    }

    /**
     * Creates new message for the entry card argument model
     *
     * @param Card $card
     * @param string|UploadedFile $message
     * @param int|null $send_order_index
     * @return bool
     */
    public function createMessage(Card $card, string|UploadedFile $message, ?int $send_order_index): bool
    {
        return $this->service->createMessage($card, $message, $send_order_index);
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
        return $this->service->updateMessage($message, $value, $send_order_index);
    }

    /**
     * Returns the card's messages
     *
     * @param Card $card
     * @return Collection
     */
    public function getMessages(Card $card): Collection
    {
        return $this->service->getMessages($card);
    }
}
