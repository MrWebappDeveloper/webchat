<?php

namespace MrWebappDeveloper\Webchat\App\Http\Controllers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFAQRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'question' => ['required', 'max:255', Rule::unique('faqs', 'question')->ignore(request()->segment(3))],
            'answer' => 'required'
        ];
    }
    public function messages()
    {
        return [
            'question.required' => 'عنوان یا سوال پاسخ ضروری است .',
            'question.unique' => 'پاسخ دیگری با این عنوان ثبت شده است .',
            'question.max' => 'تعداد حروف سوال یا عنوان نباید بیشتر از 255 حرف باشد .',

            'answer' => 'متن پاسخ ضروری است .'
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
