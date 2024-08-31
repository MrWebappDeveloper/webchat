<script>
import {FileAnalysor} from "../../../js/Tools/FileAnalysor";
import {Helper} from "../../../js/Tools/Helper";

export default {
    name: 'FileMessageComponent',
    computed: {
        Helper() {
            return Helper
        },
        FileAnalysor() {
            return FileAnalysor
        }
    },
    props:{
        filename:{
            require: true,
            type: String
        },
        uri:{
            require: true,
            type: String
        }
    },
    data(){
        return {
            messageFilename: '',
            messageUri: '',
            isImage: null
        }
    },
    created() {
        this.messageFilename = this.filename;

        this.messageUri = this.uri

        this.isImage = FileAnalysor.isImage(FileAnalysor.format(this.filename));
    },
    methods:{
        fullscreen(e){
            this.$emit('ShowImageFullscreen', e.target)
        }
    }
}
</script>

<template>
    <img v-if="isImage" :src="Helper.url(messageUri)" @click="fullscreen" class="image-file-message" alt="">
    <div class="row d-flex align-items-center px-3" v-if="!isImage">
        <div class="col-3 mr-3 d-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 50px; height: 50px;">
            <a target="_blank" :href="Helper.url(messageUri)">
                <i class="i-simple i-download text-secondary" style="font-size: 20px;"></i>
            </a>
        </div>
        <div class="col px-3">
            <span class="d-block">{{ messageFilename }}</span>
        </div>
    </div>
</template>

