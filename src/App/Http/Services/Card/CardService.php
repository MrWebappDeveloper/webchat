<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\Card;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MrWebappDeveloper\Webchat\App\Models\Card;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Http\Requests\StoreCardRequest;
use MrWebappDeveloper\Webchat\App\Http\Requests\UpdateCardRequest;
use MrWebappDeveloper\Webchat\App\Http\Services\CardMessage\CardMessageServiceProxy;
use MrWebappDeveloper\Webchat\App\Http\Services\CardMessage\ICardMessage;
use MrWebappDeveloper\Webchat\App\Http\Services\ChatMessage\ChatMessageServiceProxy;
use MrWebappDeveloper\Webchat\App\Http\Services\ChatMessage\IChatMessageService;
use MrWebappDeveloper\Webchat\App\Http\Transformers\CardCollection;
use MrWebappDeveloper\Webchat\App\Http\Transformers\CardResource;

class CardService implements ICardService
{
    private ICardMessage $cardMessageService;

    private IChatMessageService $chatMessageService;

    private function __construct(){
        $this->cardMessageService = new CardMessageServiceProxy();

        $this->chatMessageService = new ChatMessageServiceProxy();
    }

    /**
     * @message Create and return new instance of the class
     * @return CardService
     */
    public static function instance():CardService
    {
        return new self;
    }

    /**
     * Creates new card with its messages
     *
     * @param StoreCardRequest $request
     * @return Card|false
     */
    public function createCardWithMessages(StoreCardRequest $request): Card|false
    {
        DB::beginTransaction();

        $card = Card::create([
            'name' => $request->input('name'),
            'shortcut' => $request->input('shortcut'),
        ]);

        $messages = $request->all()['messages'];

        if(!$card || !$this->cardMessageService->createMessages($card, array_column($messages, 'value'), array_column($messages, 'send_order_index'))){
            Log::error('Create new card unsuccessful !', debug_backtrace());
            return false;
        }

        DB::commit();

        return $card;
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
        DB::beginTransaction();

        if(!$card->update([
            'name' => $request->input('name'),
            'shortcut' => $request->input('shortcut'),
        ]) ||
        !$this->cardMessageService->updateMessages($card, $request->all()['messages'])){
            Log::error('Update card unsuccessful !', debug_backtrace());
            return false;
        }

        DB::commit();

        return $card;
    }

    /**
     * Removes card and its messages from database
     *
     * @param Card $card
     * @return bool
     */
    public function deleteCard(Card $card): bool
    {
        DB::beginTransaction();

        if(!$this->cardMessageService->deleteCardMessages($card) || !$card->delete()){
            Log::error('Delete card unsuccessful !', debug_backtrace());
            return false;
        }

        DB::commit();

        return true;
    }

    /**
     * Removes all messages of current enter card
     *
     * @param Card $card
     * @return bool
     */
    public function deleteCardMessages(Card $card): bool
    {
        DB::beginTransaction();

        if(!$this->cardMessageService->deleteCardMessages($card)){
            Log::error('Delete card`s messages error !', debug_backtrace());
            return false;
        }

        DB::commit();

        return true;
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
        $cards = Card::paginate($perPage);

        if($html)
            return \view('vendor.webchat.partials.card_items')->with('items', $cards)->render();

        return new CardCollection($cards);
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
        $cardMessages = $this->cardMessageService->getMessages($card);

        DB::beginTransaction();

        foreach ($cardMessages as $message){
            if(!$this->chatMessageService->createNewMessageWithDirectContent($chat, $message->content))
                return false;
        }

        DB::commit();

        return true;
    }
}
