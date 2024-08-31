<?php

namespace MrWebappDeveloper\Webchat\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWizardRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'keyword' => 'required|max:255|unique:wizards,keyword',
            'faqs' => 'array|nullable',
            'faqs.*' => 'numeric|exists:faqs,id',
            'parent_id' => 'exists:wizards,id'
        ];
    }

    public function messages()
    {
        return [
            'keyword.required' => 'فیلد کلیدواژه ضروری است .',
            'keyword.unique' => 'ویزارد دیگری با همین کلیدواژه ثبت شده است .',
            'keyword.max' => 'تعداد حروف کلمه کلیدی نباید بیشتر از 255 حرف باشد .'
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
