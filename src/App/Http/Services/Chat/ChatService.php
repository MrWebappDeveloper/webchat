<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\Chat;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;
use MrWebappDeveloper\Webchat\App\Models\ChatOwner;
use MrWebappDeveloper\Webchat\App\Http\Requests\GetChatListRequest;
use MrWebappDeveloper\Webchat\App\Http\Transformers\ChatCollection;
use MrWebappDeveloper\Webchat\App\Http\Transformers\ChatMessageCollection;
use Nwidart\Modules\Collection;
use PHPUnit\Exception;
use function abort;

class ChatService implements IChatService
{
    private function __construct(){}

    /**
     * @message Create and return new instance of the class
     * @return ChatService
     */
    public static function instance():ChatService
    {
        return new self;
    }

    /**
     * این متود چت روم جدیدی را می سازد و آن را در بانک اطلاعات ذخیره می کند
     * @message Create new chat record in chats tb in database
     * @param ChatOwner $owner
     * @return Chat
     */
    public function createChat(ChatOwner $owner):Chat
    {
        $channel = $this->generateChannelName();

        try {
            return $owner->chat()->create([
                'channel' => $channel['name'],
                'token' => $channel['token'],
            ]);
        } catch (Exception $e){
            abort(500,'Create webchat channel unsuccessful !');
        }
    }

    /*
        * این متود لیست تمامی چت های ثبت شده در سیستم را
     * به صورت صفحه بندی شده (paginate) باز میگرداند
     */
    public function all(GetChatListRequest $request): ChatCollection
    {
        $query = Chat::isConnectedToOperator();

        return $request->input('perpage') ?
            new ChatCollection($query->orderBy('created_at', 'ASC')->paginate($request->input('perpage'))):
            new ChatCollection($query->get());
    }

    /**
     * Returns chat list items in html tags
     *
     * @param GetChatListRequest $request
     * @return View
     */
    public function allInHtml(GetChatListRequest $request): View
    {
        $items = Chat::orderBy('created_at', 'ASC')->has('messages')->paginate($request->input('perpage'));

        return view('vendor.webchat.partials.chat_items', compact('items'));
    }

    /**
     * این متود سرویس وظیفه حذف چت موجود را دارد
     * @message Delete chat record in database
     * @param Chat $chat
     * @return bool
     */
    public function remove(Chat $chat): bool
    {
        try {
            $chat->delete();
        } catch (\Exception $e){
            return false;
        }

        return true;
    }

    /**
     * این متود نام کانال چت و توکن آن را به صورت رندم میسازد و در یک آرایه باز میگرداند
     * @message Generate new channel name and token
     * @return array
     */
    #[ArrayShape(['token' => "string", 'name' => "string"])] public function generateChannelName():array
    {
        $token = Str::random(Config::get('webchat.channel_token_length'));

        return [
            'token' => $token,
            'name' => Config::get('webchat.channel_name_prefix') . $token
        ];
    }

    /**
     * این متود سرویس برای استخراج و واکشی پیام های هر چت استفاده می شود
     * @message This method service fetch all messages of defined chat. If $perpage and $page args has been pass , It will return result as pagination.
     * @param Chat $chat
     * @param null $perpage
     * @return ChatMessageCollection
     */
    public function fetchMessages(Chat $chat, $perpage = null): ChatMessageCollection
    {
        return new ChatMessageCollection($perpage ?
            $chat->messages()->orderByDesc('id')->paginate($perpage):
            $chat->messages()->orderByDesc('id')->get()
        );
    }

    /**
     * این متود فرم ثبت نام کاربر برای شروع گفتگو را باز می گرداند
     * @return View
     */
    public function registerForm(): View
    {
        return view('vendor.webchat.partials.register_form');
    }

    /**
     * این متود partial view قسمت چت را باز میگرداند به همراه data های مورد نیاز
     * @param Chat $chat
     * @return JsonResponse
     */
    public function show(Chat $chat): JsonResponse
    {
        return response()->json($this->chatViewData($chat, ChatMessage::adminRoleName()));
    }

    public function chatViewData(Chat $chat, string $role):array
    {
        return [
            'chat_id' => $chat->id,
            'channel' => $chat->channel,
            'token' => $chat->token,
            'isConnectedToOperator' => $chat->connected_to_operator,
            'newMessageEvent' => Config::get('webchat.new_message_event'),
            'messagesSeenEvent' => Config::get('webchat.messages_seen_event'),
            'wizardMenuSentEvent' => Config::get('webchat.send_wizard_menu_event'),
            'user_role' => $role,
        ];
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
        $chat = Chat::where('token', $chatToken)->first();

        if(!$chat)
            throw new \Exception('Chat not found with ' . $chatToken . " token for make connected to operator !");

        $chat->update([
            'connected_to_operator' => 1
        ]);

        return $chat;
    }
}
