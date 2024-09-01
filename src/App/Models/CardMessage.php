<?php

namespace MrWebappDeveloper\Webchat\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Webchat\Database\factories\CardMessageFactory;

class CardMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'content',
        'send_order_index',
    ];

    protected $casts = [
        'content' => 'json'
    ];

    public function card():BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    /**
     * Returns content text or path according message content type
     *
     * @return string
     */
    public function getValueAttribute():string
    {
        $content = is_string($this->content) ? json_decode($this->getOriginal('content')) : $this->content;

        if(is_array($content))
            return $content['type'] == ChatMessage::FILE_MESSAGE_CONTENT_ARG_TYPE_NAME ? $content[ChatMessage::FILE_MESSAGE_CONTENT_KEY_NAME] : $content[ChatMessage::TEXT_MESSAGE_CONTENT_KEY_NAME];

        return $content->type == ChatMessage::FILE_MESSAGE_CONTENT_ARG_TYPE_NAME ? $content->{ChatMessage::FILE_MESSAGE_CONTENT_KEY_NAME} : $content->{ChatMessage::TEXT_MESSAGE_CONTENT_KEY_NAME};
    }

    /**
     * Returns content type
     *
     * @return string
     */
    public function getTypeAttribute():string
    {
        return is_string($this->content) ? json_decode($this->getOriginal('content'))->type : (is_array($this->content) ? $this->content['type'] : $this->content->type);
    }

    /**
     * Returns path of the message of its type is file. return null if message type isn't file
     *
     * @return string|null
     */
    public function getPathAttribute():?string
    {
        if(is_string($this->content))
            return (is_array($this->content) ?
                ($this->content['type'] === ChatMessage::FILE_MESSAGE_CONTENT_ARG_TYPE_NAME ? $this->content[ChatMessage::FILE_MESSAGE_CONTENT_KEY_NAME] : null) :
                ($this->content->type === ChatMessage::FILE_MESSAGE_CONTENT_ARG_TYPE_NAME ? $this->content->{ChatMessage::FILE_MESSAGE_CONTENT_KEY_NAME} : null));

        return $this->content['type'] === ChatMessage::FILE_MESSAGE_CONTENT_ARG_TYPE_NAME ? $this->content[ChatMessage::FILE_MESSAGE_CONTENT_KEY_NAME] : null;
    }

    protected static function newFactory(): CardMessageFactory
    {
        return CardMessageFactory::new();
    }
}
