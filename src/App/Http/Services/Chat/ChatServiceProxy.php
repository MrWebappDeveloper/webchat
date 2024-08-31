<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\Chat;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Webchat\app\Events\DeleteChat;
use Modules\Webchat\app\Events\NewChat;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;
use MrWebappDeveloper\Webchat\App\Models\ChatOwner;
use MrWebappDeveloper\Webchat\App\Http\Requests\GetChatListRequest;
use MrWebappDeveloper\Webchat\App\Http\Services\ChatMessage\ChatMessageServiceProxy;
use MrWebappDeveloper\Webchat\App\Http\Services\ChatOwner\ChatOwnerServiceProxy;
use MrWebappDeveloper\Webchat\App\Http\Transformers\ChatCollection;
use MrWebappDeveloper\Webchat\App\Http\Transformers\ChatItemResource;
use MrWebappDeveloper\Webchat\App\Http\Transformers\ChatMessageCollection;

class ChatServiceProxy implements IChatService
{
    private ChatService $chatService;

    public function __construct(
        private ChatOwnerServiceProxy $chatOwnerService,
        private ChatMessageServiceProxy $chatMessageServiceProxy)
    {
        $this->chatService = ChatService::instance();
    }

    /**
     * @message Check that is registered chat for user session id, if registered before it chat view will return else register chat form view will return
     * @return View
     */
    public function registerForm(): View
    {
        if($owner = $this->chatOwnerService->search()){

            if($this->chatOwnerService->updateOwnerSessionId($owner))
                if($chat = $owner->chat)
                    return
                        view('webchat::partials.chat', $this->chatViewData($chat, ChatMessage::userRoleName()));
            else
                Log::error("Update chat owner session id doesn't successful !");

            $owner->delete();
        }

        return $this->chatService->registerForm();
    }

    /**
     * در این متود واسط ، بررسی میشود که آیا پیش از این برای
     * شمراه موبایل و ایمیل چتی در دیتابیس موجود است یا خیر
     * اگر موجود باشد نام کانال همان چت را باز میگرداند در غیر این صورت
     * درخواست را به کلاس سرویس اصلی ChatService میفرستد تا یکی بسازد
     * @param ChatOwner $owner
     * @return View
     */
    public function createChat(ChatOwner $owner): View
    {
        if(!$this->chatOwnerService->updateOwnerSessionId($owner))
            Log::error("Update chat owner session id doesn't successful !");

        if($chat = $owner->chat)
            return view('webchat::partials.chat', $this->chatViewData($chat, ChatMessage::userRoleName()));

        $newChat =  $this->chatService->createChat($owner);

        return view('webchat::partials.chat', $this->chatViewData($newChat, ChatMessage::userRoleName()));
    }

    public function all(GetChatListRequest $request): ChatCollection
    {
        return $this->chatService->all($request);
    }

    public function remove(Chat $chat): bool
    {
        if($res = $this->chatService->remove($chat))
            DeleteChat::dispatch();

        return $res;
    }

    public function fetchMessages(Chat $chat, $perpage = null): ChatMessageCollection
    {
        return $this->chatService->fetchMessages($chat, $perpage);
    }

    public function show(Chat $chat): JsonResponse
    {
        $response = $this->chatService->show($chat);

        $this->chatMessageServiceProxy->messagesSeen($chat->channel, ChatMessage::adminRoleName());

        return $response;
    }

    public function chatViewData(Chat $chat, string $role): array
    {
        return $this->chatService->chatViewData($chat, $role);
    }

    /**
     * Returns chat list items in html tags
     *
     * @param GetChatListRequest $request
     * @return View
     */
    public function allInHtml(GetChatListRequest $request): View
    {
        return $this->chatService->allInHtml($request);
    }

    /**
     * متود سرویسی که کاربر را به کارشناس متصل می کند
     *
     * @param string $chatToken
     * @return bool|Chat
     * @throws \Exception
     */
    public function connectToOperator(string $chatToken): bool|Chat
    {
        if($chat = $this->chatService->connectToOperator($chatToken)){
            NewChat::dispatch(new ChatItemResource($chat));
        }

        return (bool)$chat;
    }
}
