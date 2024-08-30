<?php

namespace Modules\Webchat\database\seeders;

use Illuminate\Database\Seeder;
use MrWebappDeveloper\Webchat\App\Models\FAQ;
use MrWebappDeveloper\Webchat\App\Models\Wizard;

class FAQSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Wizard::factory()->has(FAQ::factory(10))->create();
    }
}
