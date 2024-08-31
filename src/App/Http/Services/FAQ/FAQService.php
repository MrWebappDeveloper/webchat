<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\FAQ;

use MrWebappDeveloper\Webchat\App\Models\FAQ;
use MrWebappDeveloper\Webchat\App\Http\Requests\FetchFAQRequest;
use MrWebappDeveloper\Webchat\App\Http\Requests\StoreFAQRequest;
use MrWebappDeveloper\Webchat\App\Http\Requests\UpdateFAQRequest;
use MrWebappDeveloper\Webchat\App\Http\Transformers\FAQResource;

class FAQService implements IFAQService
{
    /**
     * Service's factory method
     *
     * @return FAQService
     */
    public static function instance()
    {
        return new self;
    }

    private function __construct()
    {
    }

    /**
     *
     * faq جدید را ذخیره می کند
     *
     * @param StoreFAQRequest $request
     * @return FAQ|false
     */
    public function store(StoreFAQRequest $request): FAQ|false
    {
        return FAQ::create($request->only(['question', 'answer']));
    }

    /**
     *
     * سرویس ویرایش faq
     *
     * @param UpdateFAQRequest $request
     * @param FAQ $faq
     * @return bool
     */
    public function update(UpdateFAQRequest $request, FAQ $faq): bool
    {
        return $faq->update($request->only(['question', 'answer']));
    }

    /**
     *
     * سرویس حذف faq
     *
     * @param FAQ $faq
     * @return bool
     */
    public function delete(FAQ $faq): bool
    {
        return $faq->delete();
    }

    /**
     *متود سرویس که لیست پاسخ های آماده را به صورت صفحه بندی شده باز می گرداند
     *
     * اگر پارامتر های صفحه بندی (page, perpage) در درخاست
     * کاربر موجود نباشند ,پاسخ بدون صفحه بندی خواهد و ناگزیر  همه
     * ی ویزارد ها را باز می گرداند
     *
     *
     * @param FetchFAQRequest $request
     * @return mixed
     */
    public function fetchByPaginate(FetchFAQRequest $request):mixed
    {
        $baseQuery = FAQ::select('id', 'question')->when($request->input('wizard'), function($query) use ($request){
            $query->whereHas('wizards', function($query) use ($request){
                $query->where('wizard_id', $request->input('wizard'));
            });
        });

        return $request->has('perpage') ?
            $baseQuery->paginate($request->input('perpage')) :
            $baseQuery->get() ;
    }

    /**
     * متود سرویس که از طریق آیدی پاسخ آماده را از دیتابیس استخارج می کند
     *
     * @param int $id
     * @return FAQResource|null
     */
    public function fetchSingle(int $id): null|FAQResource
    {
        $faq = FAQ::find($id);

        return $faq ? new FAQResource($faq) : null;
    }
}
