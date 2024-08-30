<?php

namespace Modules\Webchat\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Webchat\Http\Facade\ChatFacade;

class ChatOwnerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \MrWebappDeveloper\Webchat\App\Models\ChatOwner::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'socket_id' => null,
            'session_id' => ChatFacade::generateChatSessionId(),
        ];
    }

    public function online():Factory
    {
        return $this->state(function(){
            return [
                'socket_id' => rand(11111111, 99999999) . '.' . rand(11111111, 99999999),
            ];
        });
    }
}

