<?php

namespace MrWebappDeveloper\Webchat\App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;
use MrWebappDeveloper\Webchat\App\Http\Facade\MessageFacade;
use MrWebappDeveloper\Webchat\App\Http\Requests\FetchChatMessagesRequest;
use MrWebappDeveloper\Webchat\App\Http\Requests\GetMessageFileRequest;
use MrWebappDeveloper\Webchat\App\Http\Requests\SeenMessageRequest;
use MrWebappDeveloper\Webchat\App\Http\Requests\StoreMessageRquest;
use MrWebappDeveloper\Webchat\App\Http\Requests\UpdateMessageRequest;
use MrWebappDeveloper\Webchat\App\Http\Services\Chat\ChatServiceProxy;
use MrWebappDeveloper\Webchat\App\Http\Services\ChatMessage\ChatMessageService;
use MrWebappDeveloper\Webchat\App\Http\Services\ChatMessage\ChatMessageServiceProxy;
use MrWebappDeveloper\Webchat\App\Http\Transformers\ChatMessageResource;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MessageController extends Controller
{
    public function __construct(
        private ChatMessageServiceProxy $chatMessageService,
        private ChatServiceProxy $chatService,
    ){}

    /**
     * Display a listing of the resource.
     * @param FetchChatMessagesRequest $request
     * @param Chat $chat
     * @return JsonResponse
     */
    public function index(FetchChatMessagesRequest $request, Chat $chat): JsonResponse
    {
        return \response()->json([
            'status' => 'ok',
            'messages' => $this->chatService->fetchMessages($chat, $request->input('perpage'))
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreMessageRquest $request
     * @param Chat $chat
     * @return JsonResponse
     */
    public function store(StoreMessageRquest $request, Chat $chat): JsonResponse
    {
        if($data = $this->chatMessageService->storageNewMessage($request, $chat))// store message
            return response()->json([
                'status' => 'ok',
                'message' => 'stored',
                'data' => new ChatMessageResource($data)
            ]);
        else
            return response()->json([
                'status' => 'error',
                'message' => 'وجود خطا در سرور !',
            ],500);
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateMessageRequest $request
     * @param Chat $chat
     * @param ChatMessage $message
     * @return JsonResponse
     */
    public function update(UpdateMessageRequest $request, Chat $chat, ChatMessage $message): JsonResponse
    {
        return $this->chatMessageService->updateMessage($request, $message)?
            response()->json([
                'status' => 'ok',
                'message' => 'message updated !',
            ]):
            response()->json([
                'status' => 'server error',
                'message' => 'There are some error in processing request in server !',
            ], 500);
    }

    /**
     * Remove the specified resource from storage.
     * @param Chat $chat
     * @param ChatMessage $message
     * @return JsonResponse
     */
    public function destroy(Chat $chat, ChatMessage $message): JsonResponse
    {
        return $this->chatMessageService->deleteMessage($message)?
            response()->json([
                'status' => 'ok',
                'message' => 'message deleted !',
            ]):
            response()->json([
                'status' => 'server error',
                'message' => 'There are some error in processing request in server !',
            ], 500);
    }

    /**
     * Notify client to server that I seen received message
     * @param SeenMessageRequest $request
     * @return JsonResponse
     */
    public function seen(SeenMessageRequest $request):JsonResponse
    {
        return
            $this->chatMessageService->messagesSeen($request->input('channel'), $request->input('role')) ?
                \response()->json([
                    'status' => 'ok'
                ]):
                \response()->json([
                    'status' => 'error',
                    'message' => 'There are some errors in server !',
                ]);

    }

    /**
     * Finds message`s file and return that
     *
     * @param GetMessageFileRequest $request
     * @return BinaryFileResponse|JsonResponse
     */
    public function file(GetMessageFileRequest $request):BinaryFileResponse|JsonResponse
    {
        return MessageFacade::getMessageFile($request->input('path')) ?? \response()->json([
            'status' => 'not found',
            'message' => 'file not found !',
        ], 404);
    }
}
