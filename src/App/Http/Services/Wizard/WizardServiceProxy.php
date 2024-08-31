<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\Wizard;

use Illuminate\Support\Collection;
use Modules\Webchat\app\Events\NewMessage;
use Modules\Webchat\app\Events\SendWizardChildren;
use Modules\Webchat\app\Events\SendWizardFaqs;
use Modules\Webchat\app\Events\SendWizardMenuEvent;
use MrWebappDeveloper\Webchat\App\Models\Wizard;
use MrWebappDeveloper\Webchat\App\Http\Requests\FetchWizardsRequest;
use MrWebappDeveloper\Webchat\App\Http\Requests\StoreWizardRequest;
use MrWebappDeveloper\Webchat\App\Http\Requests\UpdateWizardRequest;
use MrWebappDeveloper\Webchat\App\Http\Services\ChatOwner\ChatOwnerServiceProxy;
use MrWebappDeveloper\Webchat\App\Http\Services\ChatOwner\IChatOnwerService;
use MrWebappDeveloper\Webchat\App\Http\Transformers\ChatMessageResource;
use MrWebappDeveloper\Webchat\App\Http\Transformers\FAQCollection;
use MrWebappDeveloper\Webchat\App\Http\Transformers\WizardCollection;

class WizardServiceProxy implements IWizardService
{
    private IWizardService $service;
    private IChatOnwerService $chatOwnerService;

    public function __construct()
    {
        $this->service = WizardService::instance();
        $this->chatOwnerService = new ChatOwnerServiceProxy();
    }


    /**
     * متود واسط (proxy) سرویس ساخت ویزارد جدید
     *
     * @param StoreWizardRequest $request
     * @return Wizard|false
     */
    public function store(StoreWizardRequest $request): Wizard|false
    {
        return $this->service->store($request);
    }

    /**
     * متود واسط (proxy) سرویس ویرایش ویزارد
     *
     * @param UpdateWizardRequest $request
     * @param Wizard $wizard
     * @return bool
     */
    public function update(UpdateWizardRequest $request, Wizard $wizard):bool
    {
        return $this->service->update($request, $wizard);
    }

    /**
     * متود واسط (proxy) سرویس حذف ویزارد
     *
     * @param Wizard $wizard
     * @return bool
     */
    public function delete(Wizard $wizard): bool
    {
        return $this->service->delete($wizard);
    }

    /**
     *
     * متود واسط (proxy) سرویس فرزندان ویزارد و پاسخ های آماده آن را به کاربر ارسال میکند
     *
     * اگر ارگومنت wizard$ نال باشد , فقط منوی اصلی را به کاربر ارسال می کند
     * در غیر این صورت :
     * ابتدا متود سرویس را صدا میزند , اگر متود سرویس پاسخ موفقیت آمیز بدهد:
     * اگر ویزارد دارای پاسخ آماده است , event ارسال آن را dispatch می کند,
     * اگر ویزارد دارای ویزارد های فرزند است , event ارسال آن را dispatch می کند
     * در غیر این صورت صفحه ی اول (منوی اصلی) ویزارد ها را برای کابر ارسال می کند.
     *
     * @param Wizard|null $wizard
     * @return array|bool
     */
    public function send(Wizard|null $wizard): array|bool
    {
        $chat = $this->chatOwnerService->search()->chat;

        if(!$wizard){
            $this->sendMainMenu($chat->channel);

            return true;

        }

        if(($res = $this->service->send($wizard)) !== false){

            if(!$chat)
                return false;

            foreach ($res as $message)
                NewMessage::dispatch($chat->token ,new ChatMessageResource($message));

            if($wizard->children()->count() > 0 && !empty($children = $wizard->children))
                SendWizardMenuEvent::dispatch($chat->channel, new WizardCollection($children));
            else
                $this->sendMainMenu($chat->channel);
        }
        return $res !== false;
    }

    /**
     *متود سرویس که لیست ویزارد ها را به صورت صفحه بندی شده باز می گرداند
     *
     * اگر پارامتر های صفحه بندی (page, perpage) در درخاست
     * کاربر موجود نباشند ,پاسخ بدون صفحه بندی خواهد و ناگزیر  همه
     * ی ویزارد ها را باز می گرداند
     *
     * @param FetchWizardsRequest $request
     * @return mixed
     */
    public function fetchByPaginate(FetchWizardsRequest $request): mixed
    {
        return $this->service->fetchByPaginate($request);
    }

    /**
     * متود واسط (proxy) سرویسی که لیست تمامی پاسخ های ویزارد را باز می گرداند
     *
     * @param Wizard|int $wizard
     * @return Collection
     * @throws \Exception
     */
    public function faqs(int|Wizard $wizard): Collection
    {
        return $this->service->faqs($wizard);
    }

    /**
     *متود واسط (proxy) متودی که لیست تمامی ویزارد هایی که parent_id آن ها
     * null است را باز می گرداند
     *
     * @return Collection
     */
    public function fetchParents(): Collection
    {
        return $this->service->fetchParents();
    }

    /**
     * منوی اصلی ویزارد ها ( صفحه اول ) را به کاربر ارسال می کند
     *
     * @param string $channelName
     * @return void
     */
    private function sendMainMenu(string $channelName):void
    {
        SendWizardMenuEvent::dispatch($channelName, new WizardCollection($this->fetchParents()));
    }
}
