<script>
import FileUploaderComponent from "../../../FileUploader.vue";
import {Request} from "../../../../../../js/Http/Request";

export default {
    components: {FileUploaderComponent},
    props:{
        data: {
            type: Array,
            default: () => [],
            validator: function (value) {
                return value.every(item => typeof item.faq === 'object');
            }
        }
    },
    data(){
        return{
            faq: null
        }
    },
    async created() {
      await this.loadFaq();
    },
    methods: {
        loadFaq(){
            Request
                .get()
                .url('/chat/faq/' + this.data.faq.id)
                .success(function(response, instance){
                    let responseJson = JSON.parse(response);

                    instance.faq = responseJson.data;
                })
                .error(function(response){
                    alert('خطا')
                })
                .use(this)
                .asyncSend();
        },
        onClose(){
            this.$emit('CloseShowFAQ')
        }
    }
}
</script>

<template>
    <div class="alert-container">
        <div class="content">
            <div class="alert-header position-relative">
                <p class="text-color-light padding-left-4">{{ this.faq.question }}</p>
                <i class="i-simple i-x-mark i-size-2 position-absolute cursor-pointer" style="left: 10px" @click="onClose"></i>
            </div>
            <div class="show-faq-body padding-3" v-html="this.faq.answer">

            </div>
        </div>
    </div>
</template>

<style>
.show-faq-body{
    background-color: white;
}

.show-faq-body .image{
    width: 100% !important;
}

.show-faq-body .image img{
    width: 100%;
    height: auto;
}

.alert-container{
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background-color: rgba(192, 192, 192, 0.51);
    z-index: 99999999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.alert-container .content{
    border-radius: 10px;
    overflow: hidden;
}

.alert-container .content .alert-header{
    display: flex;
    align-items: center;
    height: 50px;
    padding: 5px;
    color: white;
    background-color: #F2002B;
}

.alert-container .content .body{
    background-color: white;
}

@media only screen and (min-width: 1400px) {
    .alert-container .content{
        width: 30%;
    }
}

@media only screen and (max-width: 1400px) {
    .alert-container .content{
        width: 40%;
    }
}

@media only screen and (max-width: 1200px) {
    .alert-container .content{
        width: 45%;
    }
}

@media only screen and (max-width: 992px) {
    .alert-container .content{
        width: 55%;
    }
}

@media only screen and (max-width: 768px) {
    .alert-container .content{
        width: 75%;
    }
}

@media only screen and (max-width: 576px) {
    .alert-container .content{
        width: 90%;
    }
}

.alert-container .content .uploader-header, .alert-container .content .body{
    width: 100%;
    padding: 10px;
}
</style>

