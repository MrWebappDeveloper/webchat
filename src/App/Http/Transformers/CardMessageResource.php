<?php

namespace MrWebappDeveloper\Webchat\App\Http\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;

class CardMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return array_merge([
            'id' => $this->id,
            'type' => $this->content['type'],
            'value' => ($this->content['type'] == ChatMessage::FILE_MESSAGE_CONTENT_ARG_TYPE_NAME ? $this->content['path'] : $this->content['text']),
            'send_order_index' => $this->send_order_index
        ], ($this->content['type'] == ChatMessage::FILE_MESSAGE_CONTENT_ARG_TYPE_NAME ? ['link' => asset($this->content['path']), 'filename' => $this->content['filename']] : []));
    }
}
