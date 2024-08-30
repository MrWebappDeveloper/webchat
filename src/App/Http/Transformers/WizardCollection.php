<?php

namespace MrWebappDeveloper\Webchat\App\Http\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WizardCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
