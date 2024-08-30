<?php

namespace MrWebappDeveloper\Webchat\App\Http\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request
     * @return array
     */
    public function toArray($request)
    {
        $model = $this;

        return [
            'id' => $this->id,
            'content' => $this->content,
            'direction' => strtolower($request->input('role')) == strtolower($model->sender) ? 'left' : 'right',
            'status' => $this->status,
            'time' => date('h:i', strtotime($this->created_at)),
        ];
    }
}
