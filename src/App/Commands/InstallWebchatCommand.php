<?php

namespace MrWebappDeveloper\Webchat\App\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallWebchatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webchat:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute required scaffolding for install webchat package.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->publish();

        $this->npmScaffolding();

        $this->output->writeln("<info>Done.</info>");
    }

    private function npmScaffolding()
    {
        $this->output->writeln('<info>Scaffolding npm package.json ...</info>');

        $packageJson = json_decode(file_get_contents(base_path('package.json')), true);

        $packageJson['devDependencies'] = array_merge($packageJson['devDependencies'] ?? [], [
            "dotenv" => "^10.0.0",
            "dotenv-expand" => "^5.1.0",
            "laravel-echo" => "^1.15.3",
            "lodash" => "^4.17.21",
            "postcss" => "^8.3.7",
            "pusher-js" => "^8.3.0",
            "bootstrap" => "*",
            "@vitejs/plugin-vue" => "4.6.2",
            "jquery" => "*",
        ]);

        $packageJson['dependencies'] = array_merge($packageJson['dependencies'] ?? [], [
            "vue" => "^3.3.6"
        ]);

        file_put_contents(base_path('package.json'),json_encode($packageJson, JSON_PRETTY_PRINT));

        $this->output->writeln('<info>Please execute npm run build command to install dependencies.</info>');
    }

    private function publish()
    {
        $this->output->writeln('<info>Publishing ...</info>');

        Artisan::call('vendor:publish', ['--tag' => 'webchat-config']);

        Artisan::call('vendor:publish', ['--tag' => 'webchat-resources']);
    }
}
