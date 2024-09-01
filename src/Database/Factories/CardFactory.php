<?php

namespace Modules\Webchat\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \MrWebappDeveloper\Webchat\App\Models\Card::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'shortcut' => $this->faker->name,
            'order_index' => null,
        ];
    }
}

