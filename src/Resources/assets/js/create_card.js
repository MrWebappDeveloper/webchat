import {createApp} from "vue";
import CreateCard from "../vue/CreateCard.vue";

let createCardVue = createApp(CreateCard);

createCardVue.mount('#card-form')
