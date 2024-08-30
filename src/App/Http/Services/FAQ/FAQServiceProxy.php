<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\FAQ;

use MrWebappDeveloper\Webchat\App\Models\FAQ;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\FetchFAQRequest;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\StoreFAQRequest;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\UpdateFAQRequest;
use MrWebappDeveloper\Webchat\App\Http\Transformers\FAQResource;

class FAQServiceProxy implements IFAQService
{
    private IFAQService $service;

    public function __construct()
    {
        $this->service = FAQService::instance();
    }

    /**
     *
     * متود واسط سرویس (proxy) ساخت faq جدید
     *
     * @param StoreFAQRequest $request
     * @return FAQ|false
     */
    public function store(StoreFAQRequest $request): FAQ|false
    {
        return $this->service->store($request);
    }

    /**
     *
     * متود واسط سرویس (proxy) ویرایش faq
     *
     * @param UpdateFAQRequest $request
     * @param FAQ $faq
     * @return bool
     */
    public function update(UpdateFAQRequest $request, FAQ $faq): bool
    {
        return $this->service->update($request, $faq);
    }

    /**
     *
     * متود واسط سرویس (proxy) حذف faq
     *
     * @param FAQ $faq
     * @return bool
     */
    public function delete(FAQ $faq): bool
    {
        return $this->service->delete($faq);
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
        return $this->service->fetchByPaginate($request);
    }

    /**
     * متود سرویس که از طریق آیدی پاسخ آماده را از دیتابیس استخارج می کند
     *
     * @param int $id
     * @return FAQResource|null
     */
    public function fetchSingle(int $id): null|FAQResource
    {
        return $this->service->fetchSingle($id);
    }
}
