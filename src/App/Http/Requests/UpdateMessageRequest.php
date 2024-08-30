<?php

namespace MrWebappDeveloper\Webchat\App\Http\Controllers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;

class UpdateMessageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'text' => [Rule::requiredIf(!isset($_FILES['file'])), 'string', 'max:' . Config::get('webchat.message_max_length')],
            'file' => [Rule::requiredIf(!isset($_REQUEST['text'])) , 'file', 'max:' . Config::get('webchat.file_message_max_size')],
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
