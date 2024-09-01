<div id="chat-room-app" class="height-100">

</div>

<script>
    var user_role = @json($user_role);
    var chat_id = @json($chat_id);
    var channel = @json($channel);
    var token = @json($token);// chat token
    var isConnectedToOperator = @json($isConnectedToOperator);
    var messagesSeenEvent = @json($messagesSeenEvent);
    var newMessageEvent = @json($newMessageEvent);
    var wizardMenuSentEvent = @json($wizardMenuSentEvent);
</script>

@vite(["resources/vendor/webchat/js/chat.js"])
