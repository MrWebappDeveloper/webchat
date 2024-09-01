<?php

namespace MrWebappDeveloper\Webchat\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Webchat\Database\factories\FAQFactory;

class FAQ extends Model
{
    use HasFactory;

    protected $table = 'faqs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'question',
        'answer'
    ];

    /**
     * ویزارد های متصل به هر پاسخ آماده را باز می گرداند
     *
     * هر پاسخ آماده (faq) ممکن است به یک یا چند ویزارد متصل باشد
     * این رابطه که از نوع many to many است از طریق جدول wizard_answers  برقرار می شود
     *
     * @return BelongsToMany
     */
    public function wizards():BelongsToMany
    {
        return $this->belongsToMany(Wizard::class, 'wizard_answers_pivot', 'faq_id', 'wizard_id');
    }

    /**
     * Returns the FAQ model factory class
     *
     * @return FAQFactory
     */
    protected static function newFactory(): FAQFactory
    {
        return FAQFactory::new();
    }
}
