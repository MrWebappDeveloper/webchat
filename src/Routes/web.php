 <?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

 use App\Models\User;
 use Illuminate\Support\Facades\Broadcast;
 use Illuminate\Support\Facades\Route;
 use MrWebappDeveloper\Webchat\WebSockets\WebSocketHandler;

 Route::middleware(['web'])->group(function(){
    /**
     * WizardController API
     */
    Route::resource('wizard', \MrWebappDeveloper\Webchat\App\Http\Controllers\Api\WizardController::class);
    Route::get('/wizard/send/menu', [\MrWebappDeveloper\Webchat\App\Http\Controllers\Api\WizardController::class, 'sendMenu'])->name('wizard.send.menu');

    /**
     * FAQController API
     */
    Route::name('chat.')->prefix('/chat')->group(function(){
        Route::resource('faq', \MrWebappDeveloper\Webchat\App\Http\Controllers\Api\FAQController::class);
    });

    /**
     * ChatController API
     */
    Route::resource('chat', \MrWebappDeveloper\Webchat\App\Http\Controllers\Api\ChatController::class)->except(['update']);
    Route::controller(\MrWebappDeveloper\Webchat\App\Http\Controllers\Api\ChatController::class)->prefix('/chat')->group(function(){
        Route::post('/notify-i-am-online', 'onlineNotify')->name('owner.online.notify');
        Route::post('/connect/to/operator', 'connectToOperator')->name('chat.connect.operator');
    });

    /**
     * MessageController API
     */
    Route::apiResource('chat.message', \MrWebappDeveloper\Webchat\App\Http\Controllers\Api\MessageController::class)->except('show');
    Route::controller(\MrWebappDeveloper\Webchat\App\Http\Controllers\Api\MessageController::class)->prefix('/chat')->group(function(){

        Route::post('/messages-seen', 'seen')->name('chat');
        Route::get('/message/file', 'file')->name('chat.message.file');
    });

    /**
     * CardController API
     */
    Route::resource('card', \MrWebappDeveloper\Webchat\App\Http\Controllers\Api\CardController::class)->except('edit');
    Route::post('/card/send/{card}', [\MrWebappDeveloper\Webchat\App\Http\Controllers\Api\CardController::class, 'send'])->name('card.send');

    /**
     * Upload file API
     */
    Route::post('/webchat/file', [\MrWebappDeveloper\Webchat\App\Http\Controllers\Api\FileController::class, 'store'])->name('webchat.upload.file');

     /**
      * Webchat panel
      */
     Route::view('/webchat-panel', 'vendor.webchat.admin');

    /**
     * Set custom socket handler endpoint
     */

    app()->singleton(\BeyondCode\LaravelWebSockets\Server\Logger\WebsocketsLogger::class, function () {
        return (new \BeyondCode\LaravelWebSockets\Server\Logger\WebsocketsLogger(new \Symfony\Component\Console\Output\NullOutput()))->enable(false);
    });

    \BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter::webSocket('/app/{appKey}', WebsocketHandler::class);

    Route::get('/test-csrf-token', function(){
        dd(csrf_token());
    });

});

Route::middleware(['api'])->group(function (){
    Route::controller(\MrWebappDeveloper\Webchat\App\Http\Controllers\Api\ChatController::class)->prefix('/chat')->group(function(){
        Route::post('/offline-notify', 'offlineNotify')->name('owner.offline.notify');
    });

});

 Broadcast::channel('admin', function (User $user) {
     return true;
 });
