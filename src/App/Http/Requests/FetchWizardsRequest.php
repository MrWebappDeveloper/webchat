<?php

namespace MrWebappDeveloper\Webchat\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchWizardsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'just_independent' => ['nullable', 'boolean'],
            'faq' => ['nullable', 'numeric', 'exists:faqs,id'],
            'parent' => ['nullable', 'numeric', 'exists:wizards,id'],
            'perpage' => ['nullable', 'numeric'],
            'page' => ['nullable', 'numeric'],
        ];
    }

    public function messages()
    {
        return [
            'parent.exists' => 'آیدی ویزارد پدر معتبر نیست !'
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
