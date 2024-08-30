<?php

namespace Modules\Webchat\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class WebchatDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

//        $this->call(CardSeeder::class);

//        $this->call(ChatSeeder::class);

        $this->call(FAQSeeder::class);

        $this->call(WizardSeeder::class);
    }
}
