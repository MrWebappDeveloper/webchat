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
            showSuccessResponse: false,

            id: null,
            question: '',
        }
    },
    mounted() {
        this.question = this.data.faq.question;

        this.id = this.data.faq.id;
    },
    methods: {
        submit(){
            Request
                .delete()
                .url('/chat/faq/' + this.id)
                .data({
                    _token: csrf
                })
                .success(function(response, instance){
                    instance.wasSuccessful(response);
                })
                .use(this)
                .asyncSend();
        },
        wasSuccessful(response){
            this.showSuccessResponse = true;
        },
        onConfirm(){
            this.question = '';

            this.showSuccessResponse = false;

            this.$emit('DeleteFaqConfirm')
        },
        onCancel(){
            this.$emit('DeleteFaqCanceled')
        }
    }
}
</script>

<template>
    <div class="alert-container">
        <div class="content">
            <div class="alert-header position-relative">
                <p class="text-color-light">حذف پاسخ</p>
                <i class="i-simple i-x-mark i-size-2 position-absolute cursor-pointer" style="left: 10px" @click="onClose"></i>
            </div>
            <div class="body">
                <div class="form" v-show="!showSuccessResponse">
                    <p class="text-color-dark padding-5">
                        آیا از حذف پاسخ با عنوان
                        "{{ question }}"
                        اطمینان دارید ؟
                    </p>
                    <div class="colu-12 display-flex justify-content-end">
                        <button class="button button-danger margin-x-1" @click="submit">بله</button>
                        <button class="button margin-x-1" @click="onCancel">خیر</button>
                    </div>
                </div>
                <div class="success-response width-100 display-flex content-center" v-if="showSuccessResponse">
                    <div class="box-shadow text-align-center border-radius padding-5 width-100">
                        <i class="i-simple i-success i-size-5"></i>
                        <p class="text-align-center padding-top-5">حذف شد !</p>
                        <button class="button button-primary button-sm margin-top-3" @click="onConfirm">تایید</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
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

