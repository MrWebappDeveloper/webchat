<?php

namespace MrWebappDeveloper\Webchat\App\Providers;

use Carbon\Laravel\ServiceProvider;

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
        $this->loadMigrationsFrom(dirname(__DIR__, 2).'/Database/Migrations');

        $this->publishes([
            dirname(__DIR__, 2) . '/Config/webchat.php' => config_path('webchat.php'),
        ], 'webchat-config');
    }
}
