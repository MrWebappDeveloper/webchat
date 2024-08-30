<?php

namespace MrWebappDeveloper\Webchat\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use MrWebappDeveloper\Webchat\App\Models\Card;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\FetchCardsRequest;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\SendCardMessagesRequest;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\StoreCardRequest;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\UpdateCardRequest;
use MrWebappDeveloper\Webchat\App\Http\Services\Card\CardServiceProxy;
use MrWebappDeveloper\Webchat\App\Http\Services\CardMessage\CardMessageServiceProxy;
use MrWebappDeveloper\Webchat\App\Http\Transformers\CardResource;

class CardController extends Controller
{
    public function __construct(
        private CardServiceProxy $cardService,
    ){
    }

    /**
     * Display a listing of the resource.
     */
    public function index(FetchCardsRequest $request)
    {
        return $this->cardService->fetchByPaginate($request->input('perpage'), $request->input('format') == 'json' ? 0 : 1);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('webchat::partials.create_card');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCardRequest $request): JsonResponse
    {
        return $this->cardService->createCardWithMessages($request) ?
            response()->json([
                'status' => 'ok',
                'message' => 'New card and its messages stored !',
            ]):
            response()->json([
                'status' => 'server error',
                'message' => 'There are some errors in store new card !'
            ], 500);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show(Card $card)
    {
        return new CardResource($card);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCardRequest $request, Card $card): JsonResponse
    {
        return $this->cardService->updateCardWithMessages($card, $request) ?
            response()->json([
                'status' => 'ok',
                'message' => 'Updated !',
            ]):
            response()->json([
                'status' => 'server error',
                'message' => 'There are some errors in update card !',
            ], 500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Card $card)
    {
        return $this->cardService->deleteCard($card) ?
            response()->json([
                'status' => 'ok',
                'messages' => 'Deleted !',
            ]):
            response()->json([
                'status' => 'server error',
                'message' => 'There are some errors in delete card !',
            ], 500);
    }

    /**
     * Call related service for send the card's messages to the chat
     *
     * @param Card $card
     * @param SendCardMessagesRequest $request
     * @return JsonResponse
     */
    public function send(Card $card, SendCardMessagesRequest $request):JsonResponse
    {
        return ($this->cardService->send($card, Chat::find($request->input('chat_id'))) ?
            response()->json([
                'status' => 'ok' ,
                'message' => 'Card messages sent !',
            ]):
            response()->json([
                'status' => 'server error',
                'message' => 'There are some errors in server !',
            ], 500));
    }
}
