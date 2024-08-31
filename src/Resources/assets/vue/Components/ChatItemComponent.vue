<script>
export default {
    name: "ChatItemComponent",
    props:{
      info: Object,
    },
    data(){
        return {
            chatInfo : this.info
        }
    },
    methods:{
        open(){
            this.setUnseenMessageCountZero();

            this.$emit('openChat', this.chatInfo.id)
        },

        setUnseenMessageCountZero(){
            this.chatInfo.unseen_messages_count = 0;
        }
    }
}
</script>

<template>
    <div class="chat-item display-flex content-between flex-reverse padding-2 bordering-bottom">
        <div class="glass open-chat-btn" @click="open"></div>
        <div class="display-flex flex-dir-column text-align-right padding-right-5 pos-relative">
            <i :class="chatInfo.owner_online ? 'i-radio-btn-success' : 'i-radio-btn-secondary'" class="i-simple pos-absolute" style="right: 0; top: 12px;"></i>
            <span class="padding-y-2 padding-right-1">{{ this.chatInfo.owner_name }}</span>
            <span class="text-sm padding-right-1">{{ this.chatInfo.last_message }}</span>
        </div>
        <div class="pos-relative padding-top-2">
            <span class="pos-absolute back-red text-color-light padding-x-2 broder-rounded" style="bottom: 30%; left:35px" v-if="chatInfo.unseen_messages_count !== 0">{{ chatInfo.unseen_messages_count }}</span>
            <span v-if="chatInfo.has_any_message" class="pos-absolute padding-x-2 i-simple i-size-2" :class="(chatInfo.last_message_status === 'seen') ? 'i-check-double' : 'i-check'" style="bottom: 0px;"></span>
            <span v-if="chatInfo.has_any_message" class="text-sm padding-top-5">{{ chatInfo.last_message_time }}</span>
        </div>
    </div>
</template>

<style scoped>

</style>
