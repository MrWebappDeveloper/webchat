<?php

namespace MrWebappDeveloper\Webchat\App\Http\Facade;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use MrWebappDeveloper\Webchat\App\Models\CardMessage;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MessageFacade
{
    /**
     * این متود فایل را در فولدر storage ذخیره می کند و در نهایت آدرس محل ذخیره سازی را باز می گرداند
     * @message Save file in storage directory then return its path
     * @param UploadedFile $file
     * @return string
     */
    public static function saveFile(UploadedFile $file): string
    {
        $storePath = $file->store(self::storeFileLocation());

        return trim(URL::route('chat.message.file', [] , false) . "?path=" . trim(substr($storePath, (strpos($storePath, self::baseDirectory()) + strlen(self::baseDirectory())), strlen($storePath)) , '/\\ '), '/\\ ');
    }

    /**
     * Returns path of message`s files base directory that defined at module config
     *
     * @return string
     */
    public static function baseDirectory():string
    {
        return Config::get('webchat.file_message_storage_directory');
    }

    public static function isFileMessageAnImage(string $format):bool
    {
        return in_array($format, ['jpg', 'png', 'jpeg', 'webp', 'webp2', 'svg']);
    }

    /**
     * @message Checks that message type is file, if true fill will delete
     * @param ChatMessage|CardMessage $message
     * @return void
     */
    public static function removeMessageFile(ChatMessage|CardMessage $message):void
    {
        if(strtolower($message->content['type']) === ChatMessage::FILE_MESSAGE_CONTENT_ARG_TYPE_NAME){
            self::deleteFile($message->content['path']);
        }
    }

    /**
     * @message Delete file of entry path
     * @param string $path
     * @return void
     */
    public static function deleteFile(string $path):void
    {
        $realPath = storage_path('app' . DIRECTORY_SEPARATOR .  'public static' . DIRECTORY_SEPARATOR
            . Str::substr($path, strpos($path, self::baseDirectory()), strlen($path)));

        if(is_file($realPath))
            unlink($realPath);
    }

    /**
     * این فایل آدرس فولدر ذخیره سازی فایل پیام را در پوشه storage
     * و با تفکیک زمانی را تعیین می کند
     * @message Returns directory path for store file type message
     * @return string
     */
    public static function storeFileLocation(): string
    {
        $base_directory = trim(
            str_replace("/\\", DIRECTORY_SEPARATOR, self::baseDirectory()),
            "/\ ."
        );

        return
            'public' . DIRECTORY_SEPARATOR . $base_directory . DIRECTORY_SEPARATOR . date('Y-m-d');
    }

    /**
     * این متود متن ارسالی پیام را در بانک اطلاعاتی ذخیره سازی می کند
     * @message Store message text in database
     * @param string $text
     * @return array
     */
    #[ArrayShape(['type' => "string", 'text' => "string"])] public static function storeTextMessageStructure(string $text): array
    {
        return [
            'type' => ChatMessage::TEXT_MESSAGE_CONTENT_ARG_TYPE_NAME,
            'text' => $text
        ];
    }

    /**
     * این متود فایل ارسال شده پیام را بر روی هارد ذخیره می کند
     * @message Store file of file type message
     * @param UploadedFile $file
     * @return array
     */
    #[ArrayShape(['type' => "string", 'path' => "false|string", 'filename' => "string", 'format' => "string"])] public static function storeFileMessageStructure(UploadedFile $file): array
    {
        return [
            'type' => ChatMessage::FILE_MESSAGE_CONTENT_ARG_TYPE_NAME,
            'path' => self::saveFile($file),
            'filename' => $file->getClientOriginalName(),
            'format' => $file->getClientOriginalExtension(),
            'is_image'
        ];
    }

    /**
     * Finds message file and return that
     *
     * @param string $path
     * @return BinaryFileResponse|null
     */
    public static function getMessageFile(string $path):?BinaryFileResponse
    {
        $realPath = storage_path('app/public/' . trim(self::baseDirectory(), '/\\ ') . DIRECTORY_SEPARATOR . $path );

        return file_exists($realPath) ? response()->file($realPath) : null;
    }
}
