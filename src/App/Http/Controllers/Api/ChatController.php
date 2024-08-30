<?php

namespace MrWebappDeveloper\Webchat\App\Http\Controllers\Api;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Controller;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\ConnectToOpeartorRequest;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\GetChatListRequest;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\OfflineNotifyRequest;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\OnlineNotifyRequest;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\StoreNewChatRequest;
use MrWebappDeveloper\Webchat\App\Http\Services\Chat\ChatServiceProxy;
use MrWebappDeveloper\Webchat\App\Http\Services\ChatOwner\ChatOwnerServiceProxy;
use MrWebappDeveloper\Webchat\App\Http\Transformers\ChatItemResource;

class ChatController extends Controller
{
    public function __construct(
        private ChatOwnerServiceProxy $chatOwnerServiceProxy,
        private ChatServiceProxy $chatService
    ){}

    /**
     * این متود لیست تمامی چت های ثبت شده در سیستم را
     * به صورت صفحه بندی شده (paginate) باز میگرداند
     * @param GetChatListRequest $request
     * @return JsonResponse|View
     * @throws AuthorizationException
     */
    public function index(GetChatListRequest $request): JsonResponse|View
    {
        $this->authorize('adminOperation', Chat::class);

        if(!$request->input('format') || $request->input('format') == 'json')
            return response()->json([
                'status' => 'ok',
                'chats' => ChatItemResource::collection($this->chatService->all($request)),
            ]);

        return $this->chatService->allInHtml($request);
    }

    /**
     * این ای پی آی فرم html لازم برای دریافت مشخصات کاربر برای شروع گفتگو را باز می گرداند
     * @message Returns register for start chat form html content
     * @return View
     */
    public function create():view
    {
        return $this->chatService->registerForm();
    }

    /**
     * این ای پی آی وظیفه ساخت و ایجاد چت جدید با کانال اختصاصی متعلق به آن است
     * این ای پی ای ، مخصوص کاربری است که سازنده چت و شروع کننده آن است
     * Create new chat by its channel
     * @param StoreNewChatRequest $request
     * @return View
     */
    public function store(StoreNewChatRequest $request): View
    {
        $owner = $this->chatOwnerServiceProxy->register($request->input('name') ,$request->input('email'));

        return $this->chatService->createChat($owner);
    }

    /**
     * این API وظیفه پیدا کردن و بازگداندن نام کانال آن را دارد
     * این چت مختص کاربر یا کاربران ادمین است یک به چت های کاربران پاسخ میدهند
     * @param Chat $chat
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Chat $chat): JsonResponse
    {
        $this->authorize('adminOperation', Chat::class);

        return $this->chatService->show($chat);
    }


    /**
     * Remove the specified resource from storage.
     * @param Chat $chat
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Chat $chat)
    {
        $this->authorize('adminOperation', Chat::class);

        return
            $this->chatService->remove($chat) ?
                \response()->json([
                    'status' => 'ok',
                    'message' => 'chat removed !',
                ]) :
                \response()->json([
                    'status' => 'server error',
                    'message' => 'remove chat unsuccessful !',
                ]);

    }

    /**
     * Client user notify to server that I am online and listen to channel
     * @param OnlineNotifyRequest $request
     * @return JsonResponse
     */
    public function onlineNotify(OnlineNotifyRequest $request):JsonResponse
    {
        if($request->input('role') == ChatMessage::adminRoleName())
            return response()->json([
                'status' => 'ok'
            ]);

        return
            $this->chatOwnerServiceProxy->setOwnerAsOnline($request->input('channel'), $request->input('socket_id')) ?
            response()->json([
                'status' => 'ok'
            ]):
            response()->json([
                'status' => 'server error',
                'message' => 'there are some errors in server !'
            ], 500);
    }

    /**
     * Socket server notify to main server that client user have offline
     * @param OfflineNotifyRequest $request
     * @return JsonResponse
     */
    public function offlineNotify(OfflineNotifyRequest $request):JsonResponse
    {
        return
            $this->chatOwnerServiceProxy->setOwnerAsOffline($request->input('socket_id')) ?
                response()->json([
                    'status' => 'ok'
                ]):
                response()->json([
                    'status' => 'server error',
                    'message' => 'there are some errors in server !'
                ], 500);
    }

    /**
     * متود API اتصال چت کاربر به کارشناس
     *
     * @param ConnectToOpeartorRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function connectToOperator(ConnectToOpeartorRequest $request):JsonResponse
    {
        return $this->chatService->connectToOperator($request->input('chat_token')) ?
            response()->json([
               'status' => 'ok',
               'message' => 'Connected !'
            ]):
            response()->json([
                'status' => 'server error',
                'message' => 'There are some errors in server !',
            ]);
    }
}
