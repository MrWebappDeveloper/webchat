import '../css/icon.css';
import '../css/grid.css';
import '../css/style.css';
import './Tools/configure_laravel_echo';
import 'bootstrap/dist/js/bootstrap.bundle'
import {createApp} from "vue";
import AdminApp from "../vue/AdminApp.vue";
import CKEditor from '@ckeditor/ckeditor5-vue';

const AdminWebChatVue = createApp(AdminApp).use(CKEditor);

AdminWebChatVue.mount('#chat-app');

