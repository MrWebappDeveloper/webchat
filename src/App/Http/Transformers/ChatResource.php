<?php

namespace MrWebappDeveloper\Webchat\App\Http\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use MrWebappDeveloper\Webchat\App\Models\Chat;

class ChatResource extends JsonResource
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
            'email' => $this->email,
            'chat_id' => $this->id,
            'last_message' => $this->lastTxtMessage(),
        ];
    }
}
