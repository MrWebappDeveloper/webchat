<?php

namespace Modules\Webchat\database\seeders;

use Illuminate\Database\Seeder;
use MrWebappDeveloper\Webchat\App\Models\Wizard;

class WizardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Wizard::factory(4)->create();
    }
}
