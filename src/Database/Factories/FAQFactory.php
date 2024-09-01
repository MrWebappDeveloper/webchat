<?php

namespace Modules\Webchat\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FAQFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \MrWebappDeveloper\Webchat\App\Models\FAQ::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'question' => $this->faker->text,
            'answer' => $this->faker->text,
        ];
    }
}

