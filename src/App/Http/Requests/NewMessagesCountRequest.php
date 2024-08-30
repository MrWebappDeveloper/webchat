<?php

namespace MrWebappDeveloper\Webchat\App\Http\Controllers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;

class NewMessagesCountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'role' => ['required', Rule::in([ChatMessage::adminRoleName(), ChatMessage::userRoleName()])],
            'channel' => ['nullable', 'exists:chats,channel'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
