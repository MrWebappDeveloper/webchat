<?php

return [
    'name' => 'Webchat',
    'channel_token_length' => 50,
    'channel_name_prefix' => 'web.chat.',
    'chat_user_roles' => [0 => 'owner', 1 => 'respondent'], // change this config value need to migrate fresh and rebuild database
    'message_max_length' => 1000,
    'file_message_max_size' => 4096, // KB
    'file_message_storage_directory' => 'webchat/messages',
    'messages_length_in_first' => 20, // define how many messages of chat should load and put in chat in loading chat. More messages by scrolling will fetch in AJAX method
    'chat_session_id_length' => 40,
    'chat_session_name' => 'chat_session_id',

    'admin_channel_name' => 'admin',
    'chat_owner_went_offline_event' => 'owner.went.offline',
    'chat_owner_went_online_event' => 'owner.went.online',
    'new_card_event' => 'new.card',
    'update_card_event' => 'card.updated',
    'new_chat_event' => 'new.chat',
    'delete_chat_event' => 'chat.deleted',
    'client_status_changed_event' => 'chat.owner.status',
    'new_message_event' => 'new.message',
    'messages_seen_event' => 'messages.seen',
    'send_wizard_children_event' => 'wizard.children',
    'send_wizard_faqs_event' => 'wizard.faqs',
    'send_wizard_menu_event' => 'wizard.menu',
    'jobs_queue_name' => 'webchat', // define job name of dispatched events from this module

    'telegram_bot_token' => env('WEBCHAT_TELEGRAM_BOT_TOKEN'),
    'webchat_telegram_channel_chat_id' => env('WEBCHAT_TELEGRAM_CHANNEL_CHAT_ID'),
    'send_new_message_notification_limit_hours' => env('SEND_NEW_MESSAGE_NOTIFICATION_LIMIT_HOURS', 2),

    'disconnect_client_form_operator_after_hours' => 24
];

