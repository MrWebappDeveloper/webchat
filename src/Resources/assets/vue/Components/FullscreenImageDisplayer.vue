<script>
    export default {
        props:{
            imgTag:{
                required: true,
                type: Element,
            }
        },
        data(){
            return {
                show: true,
                img : this.imgTag,
            }
        },
        created() {
            this.img = this.imgTag;
        },
        methods:{
            close(){
                this.$emit('CloseFullscreenImage')
            }
        }
    }
</script>

<template>
    <div class="container" v-if="img">
        <span class="i-simple i-x-mark i-size-3 close" @click="close"></span>
        <img class="img" v-bind:src="img.src" alt="">
    </div>
</template>

<style scoped>
    .container{
        position: absolute;
        top: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0, 0, 0, 0.6);
    }

    .img{
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: 100%;
    }

    .close{
        position: absolute;
        top: 10px;
        left: 10px;
    }
</style>
