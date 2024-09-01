<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\Wizard;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;
use MrWebappDeveloper\Webchat\App\Models\Wizard;
use MrWebappDeveloper\Webchat\App\Http\Requests\FetchWizardsRequest;
use MrWebappDeveloper\Webchat\App\Http\Requests\StoreWizardRequest;
use MrWebappDeveloper\Webchat\App\Http\Requests\UpdateWizardRequest;
use MrWebappDeveloper\Webchat\App\Http\Services\ChatOwner\ChatOwnerServiceProxy;
use MrWebappDeveloper\Webchat\App\Http\Services\ChatOwner\IChatOnwerService;

class WizardService implements IWizardService
{
    private IChatOnwerService $chatOwnerService;

    /**
     * Service's factory method
     *
     * @return WizardService
     */
    public static function instance()
    {
        return new self;
    }

    private function __construct()
    {
        $this->chatOwnerService = new ChatOwnerServiceProxy();
    }

    /**
     * متود سرویس ساخت ویزارد جدید
     *
     * @param StoreWizardRequest $request
     * @return Wizard|false
     */
    public function store(StoreWizardRequest $request): Wizard|false
    {
        DB::beginTransaction();

        if ($wizard = Wizard::create($request->only('keyword', 'parent_id')))
            if ($request->input('faqs') && !$wizard->faqs()->sync($request->input('faqs')))
                return false;

        DB::commit();

        return $wizard;
    }

    /**
     * متود سرویس ویرایش ویزارد
     *
     * @param UpdateWizardRequest $request
     * @param Wizard $wizard
     * @return bool
     */
    public function update(UpdateWizardRequest $request, Wizard $wizard): bool
    {
        DB::beginTransaction();

        if ($res = $wizard->update($request->only('keyword', 'parent_id'))){
            if ($request->input('faqs') && !$wizard->faqs()->sync($request->input('faqs')))
                return false;
        }

        DB::commit();

        return $res;
    }

    /**
     * متود سرویس حذف ویزارد
     *
     * @param Wizard $wizard
     * @return bool
     */
    public function delete(Wizard $wizard): bool
    {
        return $wizard->delete();
    }


    /**
     *
     * متود سرویس فرزندان ویزارد و پاسخ های آماده آن را به کاربر ارسال میکند
     *
     * اگر ویزارد دارای پاسخ آماده باشد , آن را به تاریخچه پیام های چت کاربر
     * اضافه می کند. اگر عملیات موفقیت آمیز باشد , آرایه مسیج های ارسال شده
     * از نوع پاسخ آماده را باز می گرداند در غیر این صورت فالس
     *
     * @param Wizard|null $wizard
     * @return array|false
     */
    public function send(Wizard|null $wizard): array|false
    {
        if(!$wizard)
            return [];

        if(empty($wizard->faqs))
            return [];

        if(!$chatOwner = $this->chatOwnerService->search()) { // try to find chat owner with her session id
            Log::error('ChatOwner not found in send wizard method service !');
            return false;
        }

        if(!$chat = $chatOwner->chat) {
            Log::error('Chat not found in send wizard method service !');
            return false;
        }

        DB::beginTransaction();

        $messages = []; // FAQ messages

        foreach ($wizard->faqs as $faq)
            if(!$message = $chat->messages()->create([
                'sender' => ChatMessage::adminRoleName(),
                'content' => [
                    'type' => ChatMessage::FAQ_MESSAGE_CONTENT_TYPE_NAME,
                    ChatMessage::FAQ_MESSAGE_CONTENT_KEY_NAME => $faq->answer
                ]
            ])){
                Log::error('Create new message by FAQ content was not successful in send wizard method service !');
                return false;
            }
            else
                $messages[] = $message;

        DB::commit();

        return $messages;
    }

    /**
     *متود سرویس که لیست ویزارد ها را به صورت صفحه بندی شده باز می گرداند
     *
     * اگر پارامتر ضروری صفحه بندی (perpage) در درخاست
     * کاربر موجود نباشند ,پاسخ بدون صفحه بندی خواهد و ناگزیر  همه
     * ی ویزارد ها را باز می گرداند
     *
     * @param FetchWizardsRequest $request
     * @return mixed
     */
    public function fetchByPaginate(FetchWizardsRequest $request): mixed
    {
        $baseQuery = Wizard::when($request->input('parent'), function($query) use ($request){
            $query->where('parent_id', $request->input('parent'));
        })->when($request->input('faq'), function($query) use ($request){
            $query->whereHas('faqs', function($query) use ($request){
                $query->where('faq_id', $request->input('faq'));
            });
        })->when($request->input('just_independent'), function($query){
            $query->whereNull('parent_id');
        });

        return $request->has('perpage') ?
            $baseQuery->paginate($request->input('perpage')) :
            $baseQuery->get();
    }

    /**
     * متود سرویسی که لیست تمامی پاسخ های ویزارد را باز می گرداند
     *
     * @param Wizard|int $wizard
     * @return Collection
     * @throws \Exception
     */
    public function faqs(int|Wizard $wizard): Collection
    {
        if(is_integer($wizard))
            $wizard = Wizard::find($wizard);
        if(!$wizard->id){
            Log::error('Unable wizard for extract its FAQs', debug_backtrace());
            throw new \Exception('Unable wizard for extract its FAQs');
        }

        return $wizard->faqs->pluck('id');
    }

    /**
     * لیست تمامی ویزارد هایی که parent_id آن ها null است را باز می گرداند
     *
     * @return Collection
     */
    public function fetchParents(): Collection
    {
        return Wizard::whereNull('parent_id')->get();
    }
}
