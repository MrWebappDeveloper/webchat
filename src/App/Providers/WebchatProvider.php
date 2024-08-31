<?php

namespace MrWebappDeveloper\Webchat\App\Providers;

use Carbon\Laravel\ServiceProvider;
use MrWebappDeveloper\Webchat\App\Listeners\NotifyNewChatToTelegram;
use MrWebappDeveloper\Webchat\App\Listeners\NotifyRecentNewMessageToTelegram;
use MrWebappDeveloper\Webchat\App\Commands\InstallWebchatCommand;
use MrWebappDeveloper\Webchat\App\Events\NewChat;
use MrWebappDeveloper\Webchat\App\Events\NewMessage;
use MrWebappDeveloper\Webchat\App\Events\OwnerWentOnline;
use MrWebappDeveloper\Webchat\App\Listeners\CheckChatForSendWizardMenu;

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
            dirname(__DIR__, 2) . '/Public' => public_path('vendor/webchat'),
        ], 'webchat-resources');

        $this->publishes([
            dirname(__DIR__, 2) . '/Config/webchat.php' => config_path('webchat.php'),
        ], 'webchat-config');

        $this->app['events']->listen(NewChat::class, NotifyNewChatToTelegram::class);

        $this->app['events']->listen(NewMessage::class, NotifyRecentNewMessageToTelegram::class);

        $this->app['events']->listen(OwnerWentOnline::class, CheckChatForSendWizardMenu::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallWebchatCommand::class,
            ]);
        }
    }
}
