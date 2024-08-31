<script>
import {Request} from "../../../js/Http/Request";

export default {
    name: 'FileUploaderComponent',
    props:{

    },
    data(){
        return {
            showLink: false,
            link: null,
        }
    },
    methods:{
        printLink(link){
            this.link = window.location.protocol + "//" + window.location.host + '/' + link;

            this.showLink = true;
        },
        copyLink(){
          navigator.clipboard.writeText(this.link);
        },
        onClose(){
            this.$emit('close');
        },
        onCancel(){
            this.$emit('cancel')
        },
        onConfirm(){
            this.$emit('confirm');

            this.showLink = false;

            this.link = null;
        },
        onUpload(){
            let file = this.$refs.file.files[0]

            Request
                .post()
                .url('/webchat/file')
                .data({
                    _token : csrf,
                    file : file,
                })
                .success(function(response, instance){
                    let responseJson = JSON.parse(response);

                    instance.printLink(responseJson.link)
                })
                .use(this)
                .asyncSend()
        }
    }
}
</script>

<template>
   <div class="uploader-container">
       <div class="content">
           <div class="uploader-header position-relative">
               <p class="text-color-light">سرویس آپلود فایل</p>
               <i class="i-simple i-x-mark i-size-2 position-absolute cursor-pointer" style="left: 10px" @click="onClose"></i>
           </div>
           <div class="body">
               <div class="form" v-show="!showLink">
                   <div class="frm-group colu-12">
                       <label for="file" class="text-color-dark">فایل :</label>
                       <input type="file" name="" id="file" class="frm-control" ref="file">
                   </div>
                   <div class="frm-group colu-12 display-flex content-end">
                       <button class="button button-primary" @click="onUpload" type="button">آپلود</button>
                       <button class="button button-warning margin-right-1" @click="onCancel">انصراف</button>
                   </div>
               </div>
               <div class="link text-align-center" v-show="showLink">
                   <h5 class="text-color-success text-align-center">فایل آپلود شد</h5>
                   <p class="text-align-center">لینک دسترسی : </p>
                   <textarea class="width-100 text-align-center" readonly>{{link}}</textarea>
                   <button class="button margin-top-2 margin-left-2" @click="copyLink" type="button">کپی</button>
                   <button class="button button-primary margin-top-2" @click="onConfirm" type="button">تایید</button>
               </div>
           </div>
       </div>
   </div>
</template>

<style scoped>
.uploader-container{
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

.uploader-container .content{
    border-radius: 10px;
    overflow: hidden;
}

.uploader-container .content .uploader-header{
    display: flex;
    align-items: center;
    height: 50px;
    padding: 5px;
    color: white;
    background-color: #F2002B;
}

.uploader-container .content .body{
    background-color: white;
}

@media only screen and (min-width: 1400px) {
    .uploader-container .content{
        width: 30%;
    }
}

@media only screen and (max-width: 1400px) {
    .uploader-container .content{
        width: 40%;
    }
}

@media only screen and (max-width: 1200px) {
    .uploader-container .content{
        width: 45%;
    }
}

@media only screen and (max-width: 992px) {
    .uploader-container .content{
        width: 55%;
    }
}

@media only screen and (max-width: 768px) {
    .uploader-container .content{
        width: 75%;
    }
}

@media only screen and (max-width: 576px) {
    .uploader-container .content{
        width: 90%;
    }
}

.uploader-container .content .uploader-header, .uploader-container .content .body{
    width: 100%;
    padding: 10px;
}
</style>
