<?php

namespace Modules\Webchat\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;

class ChatMessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \MrWebappDeveloper\Webchat\App\Models\ChatMessage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'sender' => array(ChatMessage::adminRoleName(), ChatMessage::userRoleName())[rand(0,1)],
            'content' => json_encode([
                'type' => 'text',
                ChatMessage::TEXT_MESSAGE_CONTENT_KEY_NAME => $this->faker->text,
            ]),
            'status' => array(ChatMessage::SENT_MESSAGE_STATUS_NAME, ChatMessage::SEEN_MESSAGE_STATUS_NAME)[rand(0,1)]
        ];
    }

    public function file():Factory
    {
        return $this->state(function(){
            return [
              'content' => json_encode([
                  'type' => ChatMessage::FILE_MESSAGE_CONTENT_ARG_TYPE_NAME,
                  ChatMessage::FILE_MESSAGE_CONTENT_KEY_NAME => $this->faker->imageUrl
              ])
            ];
        });
    }

    public function faq():Factory
    {
        return $this->state(function(){
           return [
               'content' => json_encode([
                   'type' => ChatMessage::FAQ_MESSAGE_CONTENT_TYPE_NAME,
                   ChatMessage::FAQ_MESSAGE_CONTENT_KEY_NAME => $this->faker->randomHtml,
               ])
           ];
        });
    }
}

