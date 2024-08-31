<?php

namespace MrWebappDeveloper\Webchat\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchFAQRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'wizard' => ['nullable', 'numeric', 'exists:wizards,id'],
            'perpage' => ['nullable', 'numeric'],
            'page' => ['nullable', 'numeric'],
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
