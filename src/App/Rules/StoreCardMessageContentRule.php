<?php

namespace Modules\Webchat\app\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreCardMessageContentRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $index = substr($attribute, strlen('messages.'), 1);

        $type = request()->input('messages.' . $index . '.type');

        if(strtolower($type) === 'text'){
            if(!is_string($value))
                $fail('عدم تطابق محتوا و نوع پیام !');
        } elseif(strtolower($type) === 'file'){
            $format = explode('/', request()->file($attribute)->getMimeType())[1];

            if(!is_uploaded_file($value))
                $fail('عدم تطابق محتوا و نوع پیام !');
            if(!in_array(strtolower($format), $this->validMimies()))
                $fail('فورمت فایل غیر مجاز. فورمت های مجاز :'. implode(',', $this->validMimies()));
        }
    }

    public function validMimies(){
        return ['jpeg', 'jpg','png','pdf','doc','docx','xls','xlsx','ppt','pptx','mp4','mp3','wav','ogg','flac','mov','avi','wmv','mpg','mpeg'];
    }
}
