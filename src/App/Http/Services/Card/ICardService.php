<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\Card;

use Illuminate\Contracts\View\View;
use MrWebappDeveloper\Webchat\App\Models\Card;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\StoreCardRequest;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\UpdateCardRequest;
use MrWebappDeveloper\Webchat\App\Http\Transformers\CardCollection;

interface ICardService
{
    /**
     * Returns stored cards by paginate method
     *
     * @param int $perPage
     * @param bool $html
     * @return CardCollection|View
     */
    public function fetchByPaginate(int $perPage, bool $html = true):CardCollection|string;

    /**
     * Creates new card with its messages
     *
     * @param StoreCardRequest $request
     * @return bool|Card
     */
    public function createCardWithMessages(StoreCardRequest $request):bool|Card;

    /**
     * Updates exists card properties and its messages
     *
     * @param Card $card
     * @param UpdateCardRequest $request
     * @return Card|false
     */
    public function updateCardWithMessages(Card $card, UpdateCardRequest $request):Card|false;

    /**
     * Removes card and its messages from database
     *
     * @param Card $card
     * @return bool
     */
    public function deleteCard(Card $card):bool;

    /**
     * Removes all messages of current enter card
     *
     * @param Card $card
     * @return bool
     */
    public function deleteCardMessages(Card $card):bool;

    /**
     * Sends the card messages to the chat
     *
     * @param Card $card
     * @param Chat $chat
     * @return bool
     */
    public function send(Card $card, Chat $chat):bool;
}
