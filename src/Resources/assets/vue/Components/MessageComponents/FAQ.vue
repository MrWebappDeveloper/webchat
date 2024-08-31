<script>
import FullscreenImageDisplayer from "../FullscreenImageDisplayer.vue";

export default {
    name: 'FAQMessageComponent',
    components: {FullscreenImageDisplayer},
    props:{
        html:{
            require: true,
            type: String
        }
    },
    data(){
        return {
            faqHtml: '',
        }
    },
    created() {
        this.faqHtml = this.html;
    },
    mounted() {
        this.setFullscreenImageHandler()
    },
    methods:{
        setFullscreenImageHandler(){
            let app = this;

            $('.faq-html img').click(function(e){
                console.log('here')
                app.showImageFullScreen(e.target)
            })
        },
        showImageFullScreen(img){
            this.$emit('ShowImageFullscreen', img)
        }
    }
}
</script>

<template>
    <div v-html="html" class="faq-html">

    </div>
</template>

<style>
.faq-html figure.image img{
    height: auto !important;
}
</style>

