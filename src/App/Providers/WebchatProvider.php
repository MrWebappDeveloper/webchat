<?php

namespace MrWebappDeveloper\Webchat\App\Providers;

use Carbon\Laravel\ServiceProvider;
use MrWebappDeveloper\Webchat\App\Commands\InstallWebchatCommand;

class WebchatProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(
            dirname(__DIR__, 2) . '/Routes/web.php'
        );

        $this->loadMigrationsFrom(dirname(__DIR__, 2).'/Database/Migrations');

        $this->publishes([
            dirname(__DIR__, 2) . '/Resources/views' => resource_path('views/vendor/webchat'),
            dirname(__DIR__, 2) . '/Resources/assets' => resource_path('vendor/webchat'),
        ], 'webchat-resources');

        $this->publishes([
            dirname(__DIR__, 2) . '/Config/webchat.php' => config_path('webchat.php'),
        ], 'webchat-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallWebchatCommand::class,
            ]);
        }
    }
}
