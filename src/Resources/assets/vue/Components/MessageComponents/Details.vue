<script>
export default {
    name: 'MessageDetailsComponent',
    props:{
        status:{
            require: true,
            type: String
        },
        direction:{
            require: true,
            type: String
        },
        time:{
            require: true,
            type: String
        }
    },
    data(){
        return {
            messageStatus: '',
            messageDirection:'',
            messageTime:''
        }
    },
    created() {
        this.messageStatus = this.status;

        this.messageDirection = this.direction;

        this.messageTime = this.time;
    }
}
</script>

<template>
    <div class="message-detail">
        <span class="status-icon i-simple" :class="messageStatus === 'seen' ||  messageDirection === 'right' ? 'i-check-double' : 'i-check'"></span>
        <span>{{ messageTime }}</span>
    </div>
</template>

