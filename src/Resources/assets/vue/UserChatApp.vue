<template>
    <ChatComponent :data="componentData"></ChatComponent>
</template>

<script setup>
import ChatComponent from "./Components/ChatComponent.vue";

console.log('here')

const componentData = {
    user_role: user_role,
    chat_id : chat_id,
    channel : channel,
    messagesSeenEvent : messagesSeenEvent,
    newMessageEvent : newMessageEvent,
    wizardMenuSentEvent: wizardMenuSentEvent
};


</script>

<style scoped>

</style>
