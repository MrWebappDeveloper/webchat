<?php

namespace Modules\Webchat\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use MrWebappDeveloper\Webchat\App\Models\Card;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Facade\MessageFacade;

class CardMessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \MrWebappDeveloper\Webchat\App\Models\CardMessage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'content' => json_encode(['type' => 'text', 'text' => 'Hello world !']),
            'send_order_index' => rand(0, 10000),
        ];
    }

    public function text($text)
    {
        return $this->state(function() use ($text){
            return [
                'content' => MessageFacade::storeTextMessageStructure($text),
            ];
        });
    }

    public function file(UploadedFile $file)
    {
        return $this->state(function() use ($file){
            return [
                'content' => MessageFacade::storeFileMessageStructure($file),
            ];
        });
    }
}

