import {createApp} from "vue";
import UpdateCard from "../vue/UpdateCard.vue";

let createCardVue = createApp(UpdateCard);

createCardVue.mount('#card-form')
