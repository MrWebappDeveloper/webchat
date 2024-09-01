<?php

namespace MrWebappDeveloper\Webchat\App\Http\Requests;

use Illuminate\Validation\Rule;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;
use Illuminate\Foundation\Http\FormRequest;

class FetchChatMessagesRequest extends FormRequest
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
            'perpage' => ['nullable', 'numeric']
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
