<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\FAQ;

use MrWebappDeveloper\Webchat\App\Models\FAQ;
use MrWebappDeveloper\Webchat\App\Http\Requests\FetchFAQRequest;
use MrWebappDeveloper\Webchat\App\Http\Requests\StoreFAQRequest;
use MrWebappDeveloper\Webchat\App\Http\Requests\UpdateFAQRequest;
use MrWebappDeveloper\Webchat\App\Http\Transformers\FAQResource;

interface IFAQService
{
    /**
     *متود سرویس که لیست پاسخ های آماده را به صورت صفحه بندی شده باز می گرداند
     *
     * اگر پارامتر های صفحه بندی (page, perpage) در درخاست
     * کاربر موجود نباشند ,پاسخ بدون صفحه بندی خواهد و ناگزیر  همه
     * ی ویزارد ها را باز می گرداند
     *
     * @param FetchFAQRequest $request
     * @return mixed
     */
    public function fetchByPaginate(FetchFAQRequest $request):mixed;

    /**
     * متود سرویس که از طریق آیدی پاسخ آماده را از دیتابیس استخارج می کند
     *
     * @param int $id
     * @return FAQResource|null
     */
    public function fetchSingle(int $id):null|FAQResource;

    /**
     *
     * سرویس ساخت faq جدید
     *
     * @param StoreFAQRequest $request
     * @return FAQ|false
     */
    public function store(StoreFAQRequest $request):FAQ|false;

    /**
     *
     * سرویس ویرایش faq
     *
     * @param UpdateFAQRequest $request
     * @param FAQ $faq
     * @return bool
     */
    public function update(UpdateFAQRequest $request, FAQ $faq):bool;

    /**
     *
     * سرویس حذف faq
     *
     * @param FAQ $faq
     * @return bool
     */
    public function delete(FAQ $faq):bool;
}
