<?php

namespace MrWebappDeveloper\Webchat\App\Http\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;

class ChatItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'channel' => $this->channel,
            'owner_name' => $this->owner->name,
            'unseen_messages_count' => $this->unseenMessagesCount(ChatMessage::adminRoleName()),
            'last_message' => $this->lastTxtMessage,
            'last_message_status' => $this->lastMessageStatus,
            'has_any_message' => $this->hasAnyMessage,
            'owner_online' => $this->owner->isOnline,
            'last_message_time' => $this->lastMessageTime,
        ];
    }
}
