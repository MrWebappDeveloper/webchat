<?php

namespace MrWebappDeveloper\Webchat\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatOwner extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'session_id',
        'socket_id',
    ];

    protected static function newFactory()
    {
        return \Modules\Webchat\Database\factories\ChatOwnerFactory::new();
    }

    public function chat(): HasOne
    {
        return $this->hasOne(Chat::class, 'chat_owner_id');
    }

    public function getIsOnlineAttribute():bool
    {
        return $this->socket_id != null;
    }
}
