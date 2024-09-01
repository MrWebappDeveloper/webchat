<?php

namespace Modules\Webchat\database\seeders;

use Illuminate\Database\Seeder;
use MrWebappDeveloper\Webchat\App\Models\Card;

class CardMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Card::factory()->for(Card::factory())->count(5)->create();
    }
}
