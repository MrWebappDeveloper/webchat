<?php

namespace MrWebappDeveloper\Webchat\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MrWebappDeveloper\Webchat\App\Rules\StoreCardMessageContentRule;
use MrWebappDeveloper\Webchat\App\Rules\UpdateCardMessageContentRule;

class UpdateCardRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('cards', 'name')->ignore(request()->segment(2))],
            'shortcut' => ['required', 'string', 'max:255', Rule::unique('cards', 'shortcut')->ignore(request()->segment(2))],
            'messages' => ['required', 'array'],
            'messages.*.id' => ['nullable', 'exists:card_messages,id'],
            'messages.*.type' => ['required', 'in:file,text'],
            'messages.*.value' => [new UpdateCardMessageContentRule()],
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
            'messages.*.id.exists' => 'آیدی پیام معتبر نیست',
            'messages.*.type.in' => 'نوع پیام انتخاب شده معتبر نیست',
            'messages.*.type.required' => 'نوع پیام الزامی است',
            'messages.*.send_order_index.required' => 'آیدی ترتیبی پیام مشخص نشده است',
            'messages.*.send_order_index.numeric' => 'آیدی ترتیبی باید عدد باشد',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
