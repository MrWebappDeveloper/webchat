<?php

namespace MrWebappDeveloper\Webchat\App\Http\Controllers\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFAQRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'question' => 'required|unique:faqs,question',
            'answer' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'question.required' => 'عنوان یا سوال پاسخ ضروری است .',
            'question.unique' => 'پاسخ دیگری با این عنوان ثبت شده است .',

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
