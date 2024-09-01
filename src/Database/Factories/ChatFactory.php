<?php

namespace Modules\Webchat\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Models\ChatOwner;

class ChatFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Chat::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $channelToken = Str::random(Config::get('webchat.channel_token_length'));

        $channelName = Config::get('webchat.channel_name_prefix') . $channelToken;

        return [
            'channel' => $channelName,
            'token' => $channelToken,
            'chat_owner_id' => ChatOwner::factory()->create(),
        ];
    }

    public function connectedToOperator(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'connected_to_operator' => true,
            ];
        });
    }
}

