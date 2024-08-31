import '../fonts/fontawesome/fontawesome-free-6.4.2-web/css/all.css';
import '../css/icon.css';
import '../css/grid.css';
import '../css/style.css';
import {createApp} from "vue";
import UserApp from "../vue/UserApp.vue";

const UserWebChatVue = createApp(UserApp)

UserWebChatVue.mount('#chat-app');

