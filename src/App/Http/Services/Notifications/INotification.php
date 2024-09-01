<?php

namespace MrWebappDeveloper\Webchat\App\Http\Services\Notifications;

interface INotification
{
    /**
     * Sends notification context to notify API service
     *
     * @param string $title
     * @param string $message
     * @return bool
     */
    public function send(string $title, string $message):bool;
}
