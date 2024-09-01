<?php

namespace Modules\Webchat\database\seeders;

use Illuminate\Database\Seeder;
use MrWebappDeveloper\Webchat\App\Models\Card;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Card::factory()->count(10)->create();
    }
}
