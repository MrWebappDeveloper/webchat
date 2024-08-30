<?php

namespace MrWebappDeveloper\Webchat\App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Config;

class ChatMessage extends Model
{
    public const USER_ROLE_NAME_CONFIG_INDEX = 0;

    public const ADMIN_ROLE_NAME_CONFIG_INDEX = 1;

    public const FAQ_MESSAGE_CONTENT_TYPE_NAME = 'faq';

    public const FAQ_MESSAGE_CONTENT_KEY_NAME = 'html';

    public const FILE_MESSAGE_CONTENT_ARG_TYPE_NAME = 'file';

    public const FILE_MESSAGE_CONTENT_KEY_NAME = 'path';

    public const TEXT_MESSAGE_CONTENT_ARG_TYPE_NAME = 'text';

    public const TEXT_MESSAGE_CONTENT_KEY_NAME = 'text';

    public const SEEN_MESSAGE_STATUS_NAME = 'seen';

    public const SEEN_MESSAGE_STATUS_CODE = 2;

    public const SENT_MESSAGE_STATUS_NAME = 'sent';

    public const SENT_MESSAGE_STATUS_CODE = 1;

    use HasFactory;

    protected $fillable = [
        'chat_id',
        'sender',
        'content'
    ];

    protected $casts = [
        'content' => 'json'
    ];

    public static function userRoleName(): string
    {
        return Config::get('webchat.chat_user_roles')[self::USER_ROLE_NAME_CONFIG_INDEX];
    }

    public static function adminRoleName(): string
    {
        return Config::get('webchat.chat_user_roles')[self::ADMIN_ROLE_NAME_CONFIG_INDEX];
    }

    public function scopeUser(Builder $query)
    {
        $query->where('sender', self::userRoleName());
    }

    public function scopeAdmin(Builder $query)
    {
        $query->where('sender', self::adminRoleName());
    }

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    protected static function newFactory()
    {
        return \Modules\Webchat\Database\factories\ChatMessageFactory::new();
    }


}
