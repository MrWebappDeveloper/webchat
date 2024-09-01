<?php

namespace MrWebappDeveloper\Webchat\App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'token',
        'connected_to_operator'
    ];

    public function messages():HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }


    public function owner():BelongsTo
    {
        return $this->belongsTo(ChatOwner::class, 'chat_owner_id');
    }

    public function scopeIsConnectedToOperator($query):void
    {
        $query->where('connected_to_operator', 1);
    }

    public function getLastTxtMessageAttribute():string|null
    {
        if($message =  self::messages()->where('content->type', 'text')->latest()->first())
            return $message->content['text'];
        return '';
    }

    public function unseenMessagesCount(string $role):int
    {
        return $this->messages()
            ->where('sender', '!=', $role)
            ->where('status', '!=', ChatMessage::SEEN_MESSAGE_STATUS_NAME)
            ->count();
    }

    public function getHasAnyMessageAttribute():bool
    {
        return $this->messages()->latest()->first() != null;
    }

    public function getLastMessageStatusAttribute():string
    {
        if($message = $this->messages()->latest()->first())
            return $message->status;

        return '';
    }

    public function getLastMessageTimeAttribute():string
    {
        if($last = $this->messages()->latest()->first())
            return $last->created_at->format('H:i');

        return '';
    }

    protected static function newFactory()
    {
        return \Modules\Webchat\Database\factories\ChatFactory::new();
    }
}
