<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Webchat Panel</title>
</head>
<body>
<div id="chat-app">

</div>
</body>
</html>

<script>
    var user_role = 'admin'
    var adminChannel = @json(\Illuminate\Support\Facades\Config::get('webchat.admin_channel_name'));
    var ownerWentOnlineEvent = "." + @json(\Illuminate\Support\Facades\Config::get('webchat.chat_owner_went_online_event'));
    var ownerWentOfflineEvent = "." + @json(\Illuminate\Support\Facades\Config::get('webchat.chat_owner_went_offline_event'));
    var newChatEvent = "." + @json(\Illuminate\Support\Facades\Config::get('webchat.new_chat_event'));
    var newCardEvent = "." + @json(\Illuminate\Support\Facades\Config::get('webchat.new_card_event'));
    var newMessageEvent = "." + @json(\Illuminate\Support\Facades\Config::get('webchat.new_message_event'));
    var clientStatusChangedEvent = "." + @json(\Illuminate\Support\Facades\Config::get('webchat.client_status_changed_event'));
    var deleteChatEvent = "." + @json(\Illuminate\Support\Facades\Config::get('webchat.delete_chat_event'));
    var csrf = "{{ csrf_token() }}"
</script>
<script src="{{ asset('vendor/webchat/packages/jquery-3.6.3.min.js') }}"></script>

@vite(["resources/vendor/webchat/js/admin.js"])
