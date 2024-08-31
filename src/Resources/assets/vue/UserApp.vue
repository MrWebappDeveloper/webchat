<template>
    <i class="fa-solid fa-comments chat-btn cursor-pointer display-none" ref="chat-toggle" @click="show()" v-show="!boxVisibility"></i>
    <div id="web-chat-user" v-show="boxVisibility" class="chat-background">
        <div class="display-flex flex-dir-column user-chat-container">
            <div class="header padding-1 display-flex content-start text-color-light pos-relative">
                <i class="fa-solid fa-comments fa-2x colu-2 radius-circle text-align-center"></i>
                <div class="display-flex flex-dir-column colu-8 align-items-start">
                    <span>گفتگو با پشتیبان</span>
                    <small style="font-size: 10px;">پاسخ دهی در عرض چند ثانیه</small>
                </div>
                <i class="fa-solid fa-xmark pos-absolute cursor-pointer" style="top: 10px; left: 15px; font-size: 20px;" @click="hide()"></i>
            </div>
            <div class="body" id="webchat-body" style="height: 630px">

            </div>
        </div>
    </div>
</template>

<script>
import {Request} from "../js/Http/Request";
import $ from "jquery";

export default {
    data() {
        return {
            appReady: false,// show open app btn when is true

            boxVisibility: false,
            body:"",
            routes:{
                createChat : '/chat/create'
            }
        }
    },
    mounted() {
        let instance = this;
        $(document).ready(function(){
            instance.appReady = true;
        })
    },
    methods: {
        show(){
            if(window.innerWidth <= 720)
                this.disableDocumentScrolling();

            this.boxVisibility = true;
            if(this.body === '')
                this.loadRegisterForm();
        },
        hide(){
            this.enableDocumentScrolling()

            this.boxVisibility = false;
        },
        disableDocumentScrolling(){
            document.querySelector('body').style.overflowY = 'hidden';
        },
        enableDocumentScrolling(){
            document.querySelector('body').style.overflowY = 'auto';
        },
        loadRegisterForm()
        {
            Request.get().url(this.routes.createChat).success(function(response, thisClass){
                thisClass.body = response
            }).error(function(){
                console.error('There is an error in load register chat form !');
            }).use(this).send();
        }
    },
    watch:{
        body(){
            $('#webchat-body').html(this.body);
        },

        appReady(){
            let ele = this.$refs['chat-toggle'];

            $(ele).removeClass('display-none')
        }
    }
}
</script>

<style scoped>

</style>
