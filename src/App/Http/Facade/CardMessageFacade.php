<?php

namespace MrWebappDeveloper\Webchat\App\Http\Facade;

use MrWebappDeveloper\Webchat\App\Models\Card;
use MrWebappDeveloper\Webchat\App\Models\CardMessage;

class CardMessageFacade
{
    /**
     * Returns all the card's messages ids in an array
     *
     * @param Card $card
     * @return array
     */
    public static function cardMessagesIds(Card $card):array
    {
        return $card->messages()->pluck('id')->toArray();
    }

    /**
     * Removes all the card's messages that there is no those ids in $keepIds
     *
     * @param Card $card
     * @param array $keepIds
     * @return bool
     */
    public static function removeDiffs(Card $card, array $keepIds):bool
    {
        $storedMessagesIds = self::cardMessagesIds($card);

        $shouldRemoveIds = array_diff($storedMessagesIds, $keepIds);

        CardMessage::whereIn('id', $shouldRemoveIds)->delete();

        return true;
    }

    /**
     * Delete message file from server but doesn't change message content path in database
     *
     * @param CardMessage $message
     * @return bool
     */
    public static function deleteFile(CardMessage $message):bool
    {
        if(!$path = $message->path)
            return true;

        MessageFacade::deleteFile($path);

        return true;
    }
}
