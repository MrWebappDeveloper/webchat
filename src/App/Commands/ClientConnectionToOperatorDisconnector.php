<?php

namespace MrWebappDeveloper\Webchat\App\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;

/**
 * این کامند پیاده سازی الگوریتم باطل کردن ارتباط کاربر با کارشناس در چت است
 *
 * وقتی آخرین پیام کاربر به کارشناس مربوط به مدت ها پیش می شود , این کامند
 * آن را باطل می کند و ارتباط مستقیم کاربر با کارشناس را قطع می کند
 */
class ClientConnectionToOperatorDisconnector extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'operatorOldChatConnections:disconnect';

    /**
     * The console command description.
     */
    protected $description = 'Disconnects connection between clients and
    operators when last activity in chat related to many time ago ( period
    predefined in the Webchat module configs by key "disconnect_client_form_operator_after_hours"';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chats = $this->getAllConnectedToOperatorChats('id');

        foreach ($chats as $chat_id)
            $this->runAlgorithm($this->getChat($chat_id));
    }

    /**
     *
     * الگوریتم باطل کردن ارتباط کاربر با کارشناس را اجرا می کند
     *
     * @param Chat $chat
     * @return bool
     */
    public function runAlgorithm(Chat $chat):bool
    {
        if(!$this->hasMessageHistory($chat)){
            $this->disconnectClientFromOperator($chat);
            return true;
        }

        $lastMessage = $chat->messages()->latest()->first();

        if($this->isMessageSentWithClient($lastMessage)){
            if($this->isMessageDateForManyTimeAgo($lastMessage))
                $this->disconnectClientFromOperator($chat);

            return true;
        }

        if($this->isMessageTypeFAQ($lastMessage)){
            $this->disconnectClientFromOperator($chat);
            return true;
        }

        if($this->isMessageDateForManyTimeAgo($lastMessage))
            $this->disconnectClientFromOperator($chat);

        return true;
    }

    /**
     * چت مربوط آیدی ورودی را باز می گرداند
     *
     * @param int $chat_id آیدی ورودی
     * @return Chat
     */
    public function getChat(int $chat_id):Chat
    {
        return Chat::find($chat_id);
    }

    /**
     * لیست تمامی چت هایی را کاربر در آن ها به کارشناس متصل شده است
     * را باز می گرداند
     *
     * @param string $selectColumns
     * @return array
     */
    public function getAllConnectedToOperatorChats(string $selectColumns):array
    {
        return Chat::isConnectedToOperator()->get($selectColumns)->pluck('id')->toArray();
    }

    /**
     * بررسی می کند که نوع پیام ارسال شده از نوع پاسخ آماده است یا خیر
     *
     * @param ChatMessage $message
     * @return bool
     */
    public function isMessageTypeFAQ(ChatMessage $message):bool
    {
        $content = is_string($message->content) ? json_decode($message->content) : $message->content;

        return ($content->type === ChatMessage::FAQ_MESSAGE_CONTENT_TYPE_NAME);
    }

    /**
     * بررسی می کند که آیا آخرین پیام توسط خود کاربر ارسال شده یا نه و مثلا توسط پشتیبان ارسال شده است
     *
     * @param ChatMessage $message
     * @return bool
     */
    public function isMessageSentWithClient(ChatMessage $message):bool
    {
        return $message->sender === ChatMessage::userRoleName();
    }

    /**
     *
     * بررسی می کند که بازه زمانی پیام ارسال شده برای مدت ها پیش است یا خیر
     *
     * بازه زمانی در فایل کانفیگ ماژول با نام disconnect_client_form_operator_after_hours تعیین شده است
     *
     * @param ChatMessage $message
     * @return bool
     */
    public function isMessageDateForManyTimeAgo(ChatMessage $message):bool
    {
        $messageDate = new Carbon($message->updated_at);

        return ($messageDate->diffInHours(Carbon::now()) >= Config::get('webchat.disconnect_client_form_operator_after_hours'));
    }

    /**
     *
     * بررسی می کند که چت وارد شده تاریخچه پیام دارد یا هیچ پیامی در تارخچه آن موجود نیست
     *
     * @param Chat $chat
     * @return bool
     */
    public function hasMessageHistory(Chat $chat):bool
    {
        return $chat->messages()->count() > 0;
    }

    /**
     * ارتباط کاربر و پشتبیان را در چت قطع می کند
     *
     * @param Chat $chat
     * @return bool
     */
    public function disconnectClientFromOperator(Chat $chat):bool
    {
        return $chat->update([
            'connected_to_operator' => 0,
        ]);
    }

}
