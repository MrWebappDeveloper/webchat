<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\Card;

use Illuminate\Contracts\View\View;
use Modules\Webchat\app\Events\CardUpdated;
use Modules\Webchat\app\Events\NewCard;
use MrWebappDeveloper\Webchat\App\Models\Card;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Http\Requests\StoreCardRequest;
use MrWebappDeveloper\Webchat\App\Http\Requests\UpdateCardRequest;
use MrWebappDeveloper\Webchat\App\Http\Transformers\CardCollection;

class CardServiceProxy implements ICardService
{
    private ICardService $service;

    /**
     * Proxy constructor that initial properties and services
     */
    public function __construct()
    {
        $this->service = CardService::instance();
    }

    /**
     * Creates new card with its messages
     *
     * @param StoreCardRequest $request
     * @return bool
     */
    public function createCardWithMessages(StoreCardRequest $request): bool
    {
        $newCard = $this->service->createCardWithMessages($request);

        if($newCard)
            NewCard::dispatch(\view('vendor.webchat.partials.card_items')->with('items', [$newCard])->render());

        return (bool)$newCard;
    }

    /**
     * Updates exists card properties and its messages
     *
     * @param Card $card
     * @param UpdateCardRequest $request
     * @return Card|false
     */
    public function updateCardWithMessages(Card $card, UpdateCardRequest $request):Card|false
    {
        if($updated = $this->service->updateCardWithMessages($card, $request))
            CardUpdated::dispatch($updated->id, $updated->name);

        return $updated;
    }

    /**
     * Removes card and its messages from database
     *
     * @param Card $card
     * @return bool
     */
    public function deleteCard(Card $card): bool
    {
        return $this->service->deleteCard($card);
    }

    /**
     * Removes all messages of current enter card
     *
     * @param Card $card
     * @return bool
     */
    public function deleteCardMessages(Card $card): bool
    {
        return $this->service->deleteCardMessages($card);
    }

    /**
     * Returns stored cards by paginate method
     *
     * @param int $perPage
     * @param bool $html
     * @return CardCollection|string
     */
    public function fetchByPaginate(int $perPage, bool $html = true): CardCollection|string
    {
        return $this->service->fetchByPaginate($perPage, $html);
    }

    /**
     * Sends the card messages to the chat
     *
     * @param Card $card
     * @param Chat $chat
     * @return bool
     */
    public function send(Card $card, Chat $chat): bool
    {
        return $this->service->send($card, $chat);
    }
}
