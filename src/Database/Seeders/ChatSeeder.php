<?php

namespace Modules\Webchat\database\seeders;

use Illuminate\Database\Seeder;
use MrWebappDeveloper\Webchat\App\Models\Chat;
use MrWebappDeveloper\Webchat\App\Models\ChatMessage;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Chat::factory()->hasMessages(2)->count(20)->create();
    }
}
