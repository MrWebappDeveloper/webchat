import './Tools/configure_laravel_echo';
import ChatApp from "../vue/UserChatApp.vue";
import {createApp} from "vue";

const ChatVue = createApp(ChatApp)

ChatVue.mount("#chat-room-app")
