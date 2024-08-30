<?php

namespace MrWebappDeveloper\Webchat\App\Http\Controllers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Modules\Webchat\app\Rules\StoreCardMessageContentRule;
use function PHPUnit\Framework\isNull;

class StoreCardRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:cards,name'],
            'shortcut' => ['required', 'string', 'max:255', 'unique:cards,shortcut'],
            'messages' => ['required', 'array'],
            'messages.*.type' => ['required', 'in:file,text'],
            'messages.*.value' => [new StoreCardMessageContentRule()],
            'messages.*.send_order_index' => ['required', 'numeric']
        ];
    }

    /**
     * Error messages
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.unique' => 'این عنوان قبلا ثبت شده است',
            'name.required' => 'فیلد عنوان ضروری است',
            'shortcut.required' => 'فیلد میانبر الزامی است',
            'shortcut.unique' => 'این کلید میانبر قبلا ثبت شده است ',
            'messages.*.type.in' => 'نوع پیام انتخاب شده معتبر نیست',
            'messages.*.type.required' => 'نوع پیام الزامی است',
            'messages.*.send_order_index.required' => 'آیدی ترتیبی پیام مشخص نشده است',
            'messages.*.send_order_index.numeric' => 'آیدی ترتیبی باید عدد باشد',
        ];
    }

    /**+
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
