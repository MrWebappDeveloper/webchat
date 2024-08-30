<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(\MrWebappDeveloper\Webchat\App\Http\Controllers\Api\ChatController::class)->prefix('/chat')->group(function(){
    Route::post('/offline-notify', 'offlineNotify')->name('owner.offline.notify');
});
