<?php

namespace MrWebappDeveloper\Webchat\App\Http\Controllers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;

class StoreMessageRquest extends FormRequest
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
            'text' => [Rule::requiredIf(!isset($_FILES['file'])), 'string', 'max:' . Config::get('webchat.message_max_length')],
            'file' => [Rule::requiredIf(!isset($_REQUEST['text'])) , 'file', 'max:' . Config::get('webchat.file_message_max_size') , 'mimes:jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mp3'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'text.required' => 'The text field is required when the file field not present',
            'file.required' => 'The file field is required when the text field not present',
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
