<script>
export default {
    name: 'TextMessageComponent',
    props:{
        text:{
            require: true,
            type: String
        }
    },
    data(){
        return {
            messageText: ''
        }
    },
    created() {
        this.messageText = this.text;
    }
}
</script>

<template>
    <span>{{ messageText }}</span>
</template>

