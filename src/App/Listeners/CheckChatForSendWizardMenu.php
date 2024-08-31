<?php

namespace MrWebappDeveloper\Webchat\App\Listeners;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use MrWebappDeveloper\Webchat\App\Events\SendWizardMenuEvent;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;
use MrWebappDeveloper\Webchat\App\Models\Wizard;
use MrWebappDeveloper\Webchat\App\Http\Transformers\WizardCollection;

/**
 * این listener وظیفه بررسی وضیعت ارتباط کاربر با کارشناس
 * و ارسال منوی ویزارد ها به آن در صورت متصل نبودن کاربر به
 * کارشناس را دارد
 */
class CheckChatForSendWizardMenu implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Define queue name
     *
     * @return string
     */
    public function viaQueue(): string
    {
        return Config::get('webchat.jobs_queue_name');
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        if(!$chat = Chat::find($event->chat_id)){
            Log::error('Can not found chat by id ' . $event->chat_id . " for send wizard menu to it!");
            $this->fail();
            exit;
        }

        if(!$this->isConnectedToOperator($chat))
            $this->broadcastWizardMenu($chat);
    }

    /**
     * بررسی می کند که آیا کاربر در چت به کارشناس متصل شده است
     *
     * @param Chat $chat
     * @return bool
     */
    public function isConnectedToOperator(Chat $chat):bool
    {
        return $chat->connected_to_operator;
    }

    /**
     * لیست منو ویزارد را برای کاربر از طریق اتصال سوکت ارسال می کند
     *
     * @param Chat $chat
     * @return void
     */
    public function broadcastWizardMenu(Chat $chat):void
    {
        $wizards = Wizard::whereNull('parent_id')->get();

        SendWizardMenuEvent::dispatch($chat->channel, new WizardCollection($wizards));
    }
}
