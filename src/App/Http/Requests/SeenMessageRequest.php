<?php

namespace MrWebappDeveloper\Webchat\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;

class SeenMessageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'channel' => ['required', 'exists:chats,channel'],
            'role' => ['required', Rule::in([ChatMessage::adminRoleName(), ChatMessage::userRoleName()])],
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
