<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\Wizard;

use Illuminate\Support\Collection;
use MrWebappDeveloper\Webchat\App\Models\Wizard;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\FetchWizardsRequest;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\StoreWizardRequest;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\UpdateWizardRequest;

interface IWizardService
{
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
    public function fetchByPaginate(FetchWizardsRequest $request):mixed;

    /**
     * متود سرویسی که لیست تمامی پاسخ های ویزارد را باز می گرداند
     *
     * @param Wizard|int $wizard
     * @return Collection
     */
    public function faqs(Wizard|int $wizard):Collection;

    /**
     * متود سرویس ساخت ویزارد جدید
     *
     * @param StoreWizardRequest $request
     * @return Wizard|false
     */
    public function store(StoreWizardRequest $request):Wizard|false;

    /**
     * متود سرویس ویرایش ویزارد
     *
     * @param UpdateWizardRequest $request
     * @param Wizard $wizard
     * @return bool
     */
    public function update(UpdateWizardRequest $request, Wizard $wizard):bool;

    /**
     * متود سرویس حذف ویزارد
     *
     * @param Wizard $wizard
     * @return bool
     */
    public function delete(Wizard $wizard):bool;

    /**
     *
     * متود سرویس فرزندان ویزارد و پاسخ های آماده آن را به کاربر ارسال میکند
     *
     * بررسی می کند اگر ویزارد انتخابی دارای پاسخ آماده است آن ها را برای
     * کاربر ارسال می کند. سپس بررسی می کند  ویزارد دارای ویزارد های
     * فرزند و زیر مجموعه است و در صورت صحیح بودن ویزارد های فرزند
     *را هم ارسال می کند.توجه: کاربری که برای او باید ویزارد ارسال شود از
     *طریق session شناسای می شود
     *
     * @param Wizard|null $wizard
     * @return array|bool
     */
    public function send(?Wizard $wizard):array|bool;

    /**
     * لیست تمامی ویزارد هایی که parent_id آن ها null است را باز می گرداند
     *
     * @return Collection
     */
    public function fetchParents():Collection;
}
