<?php

namespace Modules\Webchat\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MrWebappDeveloper\Webchat\App\Models\Wizard;

class WizardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \MrWebappDeveloper\Webchat\App\Models\Wizard::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'keyword' => $this->faker->name(),
        ];
    }

    public function child():Factory
    {
        return $this->state(function(array $attributes){
            return [
                'parent_id' => Wizard::factory(),
            ];
        });
    }
}

