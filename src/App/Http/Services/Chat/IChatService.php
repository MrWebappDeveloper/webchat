<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\Chat;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Models\ChatOwner;
use MrWebappDeveloper\Webchat\App\Http\Requests\GetChatListRequest;
use MrWebappDeveloper\Webchat\App\Http\Transformers\ChatCollection;
use MrWebappDeveloper\Webchat\App\Http\Transformers\ChatMessageCollection;

interface IChatService
{
    public function registerForm():View;

    public function createChat(ChatOwner $owner):View|Chat;

    public function all(GetChatListRequest $request):ChatCollection;

    /**
     * Returns chat list items in html tags
     *
     * @param GetChatListRequest $request
     * @return View
     */
    public function allInHtml(GetChatListRequest $request):View;

    public function remove(Chat $chat):bool;

    public function fetchMessages(Chat $chat, $perpage = null):ChatMessageCollection;

    public function show(Chat $chat):JsonResponse|View;

    public function chatViewData(Chat $chat, string $role):array;

    /**
     * متود سرویسی که کاربر را به کارشناس متصل می کند
     *
     * @param string $chatToken
     * @return bool|Chat
     */
    public function connectToOperator(string $chatToken):bool|Chat;
}
