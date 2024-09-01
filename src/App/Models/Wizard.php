<?php

namespace MrWebappDeveloper\Webchat\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Webchat\Database\factories\WizardFactory;

class Wizard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'keyword',
        'parent_id',
    ];

    /**
     * لیست فرزندان یک ویزارد را باز می گرداند
     *
     * هر ویزارد می تواند تعدادی ویزارد فرزند داشته باشد
     * این رابطه از طریق فیلد parent_id ایجاد می شود
     *
     * @return HasMany
     */
    public function children():HasMany
    {
        return $this->hasMany(Wizard::class, 'parent_id');
    }

    /**
     * وایزارد پدر را باز می گرداند
     *
     * هر ویزارد می تواند ویزارد پدری داشته باشد
     * این رابطه از طریق فیلد parent_id ایجاد می شود. مقداردهی این فیلد ضروری نیست.
     * یعنی ویزارد می تواند پدر نداشته باشد. در این صورت آن ویزارد , ویزارد سرشاخه است و در لیست منوی ابتدائی ویزارد ها نمایش داده می شود
     *
     * @return BelongsTo
     */
    public function parent():BelongsTo
    {
        return $this->belongsTo(Wizard::class, 'parent_id');
    }

    /**
     * پاسخ های آماده ویزارد را باز میگرداند
     *
     * هر ویزارد ممکن است دارای لیستی از پاسخ های آماده باشد .
     * این رابطه که از نوع many to many است به واسطه جدول wizard_answers  برقرار می شود.
     * داشتن پاسخ آماده برای ویزاد ضروری نیست . لذا ممکن است این متود لیست خالی باز گرداند .
     *
     * @return BelongsToMany
     */
    public function faqs():BelongsToMany
    {
        return $this->belongsToMany(FAQ::class, 'wizard_answers_pivot', 'wizard_id', 'faq_id');
    }

    /**
     * Returns the Wizard model factory class
     *
     * @return WizardFactory
     */
    protected static function newFactory(): WizardFactory
    {
        return WizardFactory::new();
    }
}
