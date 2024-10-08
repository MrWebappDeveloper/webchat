<?php

namespace MrWebappDeveloper\Webchat\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendWizardMenuManuallyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'chat_token' => ['required', 'exists:chats,token']
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
