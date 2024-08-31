<?php

namespace MrWebappDeveloper\Webchat\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetMessageFileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'path' => 'required|string'
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
