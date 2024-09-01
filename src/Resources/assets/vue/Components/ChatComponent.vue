<script>
import {MessageElementBuilder} from "../../js/Tools/Chat/MessageElementBuilder";
import {Request} from "../../js/Http/Request";
import {Helper} from "../../js/Tools/Helper";
import {FileAnalysor} from "../../js/Tools/FileAnalysor";
import $ from "jquery";
import AlertComponent from "./AlertComponent.vue";
import TextMessageComponent from "./MessageComponents/Text.vue";
import FileMessageComponent from "./MessageComponents/File.vue";
import MessageDetailsComponent from "./MessageComponents/Details.vue";
import {ref} from "vue";
import WizardMenuComponent from "./MessageComponents/WizardMenu.vue";
import FAQMessageComponent from "./MessageComponents/FAQ.vue";
import FullscreenImageDisplayer from "./FullscreenImageDisplayer.vue";

export default {
    computed: {
        Helper() {
            return Helper
        },
        FileAnalysor() {
            return FileAnalysor
        }
    },
    components: {
        FullscreenImageDisplayer,
        FAQMessageComponent,
        WizardMenuComponent,
        MessageDetailsComponent, FileMessageComponent, TextMessageComponent, AlertComponent
    },
    props: {
        data: {
            user_role: String,
            chat_id: Number,
            channel: String,
            messagesSeenEvent: String,
            newMessageEvent: String,
            wizardMenuSentEvent: {
                type: String,
                required: false
            }
        }
    },

    data() {
        return {
            alert: {
                type: null,
                message: '',
            },

            wizardMenuHistory:[], // keeps all of received wizard menus for go back of submenu

            fullscreenImage:null,
            socketId: null,
            isConnectedToOperator: 1,
            chatBodyId: "chat-body", // Chat body element ID
            body: "", // html body of chat view
            lastReceiveMessage: "",// receive message content will set to this prop value
            showSendTextMsgBtn: false, // define that send text type button show or hide
            showSentFileMsgBtn: true, // define that send file type button show or hide
            textInput: '', // value of text input that use for type message
            page_id: 1, // this property is for counting length fetch history messages from server when user scroll to top of chat view
            routes: {
                register: '/chat',
                send_message: '/chat/' + this.data.chat_id + '/message',
                fetch_messages: '/chat/' + this.data.chat_id + '/message',
                online_notify: '/chat/notify-i-am-online',
                messages_seen: '/chat/messages-seen',
                connect_to_operator: '/chat/connect/to/operator',
                send_wizard_menu_through_socket: '/wizard/send/menu'
            }
        }
    },
    setup() {
        const fetchMessageLength = 50; // this cons define that how many messages should fetch from server each request

        const chatMessages = ref([]); // chat messages

        return {
            fetchMessageLength,
            chatMessages
        }
    },
    async created() {
        if(user_role && user_role !== 'admin')
            this.isConnectedToOperator = isConnectedToOperator;

        this.connectToSocket();

        await this.fetchMessageHistory();

        let instance = this;

        setTimeout(function () {
            instance.scrollBodyToBottom()
        }, 2000)
    },
    methods: {
        connectToSocket() {
            let channel = window.Echo.channel(this.data.channel);

            channel
                .listen("." + this.data.newMessageEvent, this.NewReceiveMessageHandler)
                .listen("." + this.data.messagesSeenEvent, this.sentMessagesSeenHandler);

            if (this.data.wizardMenuSentEvent)
                channel.listen("." + this.data.wizardMenuSentEvent, this.wizardMenuHandler)

            channel
                .on('pusher:subscription_succeeded', (member) => {
                    let socketId = window.Echo.socketId()

                    this.socketId = socketId;

                    this.notifyIAmOnline(socketId);
                })

        },

        /**
         * Listening to this.channel for new message event
         */
        NewReceiveMessageHandler(e) {
            this.lastReceiveMessage = e.message;

            this.notifyISawReceiveMessage();
        },

        /**
         * When send message of user seen by against user, this function will get broadcast from server and execute for
         * show double check for seen message
         * @param e
         */
        sentMessagesSeenHandler(e) {
            MessageElementBuilder.oldSendMessagesSeen();
        },

        /**
         * If wizardMenuSentEvent prop is set in app, it will invoke as
         * wizard menu handler for get it through socket and display to
         * client
         */
        wizardMenuHandler(e) {
            this.wizardMenuHistory.push(e);

            this.removeWizardMessage();

            let message = {
                id: Helper.generateRandomNumber(6),
                direction: 'right',
                content: {
                    type: 'wizard',
                    list: e.wizards
                },
                time: Helper.getTimeNow()
            }

            this.pushMsg([message])
        },

        /**
         * Come back from submenu to parent menu of current
         */
        goBackWizardMenu(){
            this.wizardMenuHistory.pop()

            this.wizardMenuHandler(this.wizardMenuHistory[this.wizardMenuHistory.length - 1])
        },

        /**
         * Send request to server for connect to operator
         */
        connectToOperator() {
            if(!this.isConnectedToOperator)
                Request
                    .post()
                    .url(this.routes.connect_to_operator)
                    .data({
                        '_token': csrf,
                        'chat_token': token,
                    })
                    .success(function (response, instance) {
                        instance.isConnectedToOperator = true;

                        instance.removeWizardMessage();

                        instance.clearWizardHistory();
                    })
                    .error(function (response, instance) {
                        alert('خطا');
                    })
                    .use(this)
                    .asyncSend();
            else{
                this.removeWizardMessage();

                this.clearWizardHistory();
            }
        },

        /**
         * Search between chat messages and removes
         * any that its type is wizard
         */
        removeWizardMessage() {
            let app = this;

            this.chatMessages.forEach(function (value, index) {
                if(value.content.type === 'wizard')
                  app.chatMessages.splice(index, 1);
            })

        },

        /**
         * removes all stored histories in the property
         */
        clearWizardHistory(){
            this.wizardMenuHistory = [];
        },

        /**
         * Notifies to server that current user is online
         */
        notifyIAmOnline(socketId) {
            Request
                .post()
                .url(this.routes.online_notify)
                .data({
                    _token: csrf,
                    'channel': this.data.channel,
                    'socket_id': socketId,
                    'role': this.data.user_role
                }).send();
        },

        notifyISawReceiveMessage() {
            Request
                .post()
                .url(this.routes.messages_seen)
                .data({
                    _token: csrf,
                    'channel': this.data.channel,
                    'role': this.data.user_role
                }).send();
        },

        /**
         * Adds messages argument entry to first of messages property
         *
         * @param messages
         */
        unshiftMsg(messages) {
            this.chatMessages.unshift(...messages.reverse());
        },

        /**
         * Adds messages argument entry to end of messages property
         *
         * @param messages
         */
        pushMsg(messages) {
            this.chatMessages.push(...messages);
        },

        /**
         * Set empty for text message input value
         */
        emptyTextInput() {
            this.textInput = '';
        },

        /**
         * Show alert to user
         *
         * @param type
         * @param message that should in [error, success, info, warning]
         * @param autoRemove removes alert automatic when is true
         */
        showAlert(type, message, autoRemove = true) {
            this.alert.type = type;
            this.alert.message = message;

            let thisClass = this;

            if (autoRemove)
                setTimeout(function () {
                    thisClass.alert.type = null;
                    thisClass.alert.message = ''
                }, 3000)
        },

        /**
         * Send request to server for get wizard menu in socket connection
         *
         * When server receives this request , dispatches a broadcast event
         * that sends wizard menu to current user through socket connection
         */
        sendWizardMenuTrigger(){
            Request
                .get()
                .url(this.routes.send_wizard_menu_through_socket)
                .data({
                    chat_token : token,
                })
                .use(this)
                .success(function (response, instance){
                    console.log(response)
                })
                .error(function(response, instance){
                    alert('خطا');
                })
                .asyncSend();
        },

        /**
         * This function will execute when user want to send new text message
         */
        sendTxtMessage() {
            let request = this.sendMessageRequestObj();

            request
                .data({
                    _token: csrf,
                    role: this.data.user_role,
                    text: this.textInput
                }).success(async function (response, instance) {
                response = JSON.parse(response)

                instance.pushMsg([response.data])

                instance.scrollDown()

                instance.emptyTextInput();
            }).send();
        },

        sendFileMessage(e) {
            let request = this.sendMessageRequestObj();

            this.showAlert('info', 'درحال آپلود ...', false);

            request
                .data({
                    _token: csrf,
                    role: this.data.user_role,
                    file: e.target.files[0]
                }).success(async function (response, instance) {
                response = JSON.parse(response)

                instance.pushMsg([response.data])

                instance.scrollDown()

                instance.showAlert('success', 'فایل آپلود شد !');
            }).send();
        },

        /**
         * Create basic send message request object then returns that
         * @returns {Request}
         */
        sendMessageRequestObj() {
            return Request
                .post()
                .url(this.routes.send_message)
                .addHeader('X-Socket-Id', this.socketId)
                .error(function (response, instance) {
                    response = JSON.parse(response)

                    if (response.message)
                        instance.showAlert('error', response.message)
                    else
                        instance.showAlert('error', 'وجود خطا در سرور !')
                })
                .use(this);
        },

        /**
         * Scrolling chat body to end of it
         */
        scrollDown() {
            let ele = document.getElementById(this.chatBodyId);

            $(ele).animate({
                scrollTop: ele.scrollHeight
            }, 800);
        },

        /**
         * Get coupe of last messages history by send request to API
         */
        async fetchMessageHistory() {
            await Request
                .get()
                .data({
                    role: this.data.user_role,
                    page: this.page_id,
                    perpage: this.fetchMessageLength,
                })
                .url(this.routes.fetch_messages)
                .success(function (response, instance) {
                    response = JSON.parse(response);

                    if (response.messages.length !== 0) {
                        instance.unshiftMsg(response.messages)

                        instance.upPageId();// Plus one page_id because current page_id fetched and appended to page
                    } else
                        instance.historyFetchCompleted();// define that all pages fetched
                })
                .error(function () {

                })
                .use(this)
                .asyncSend();
        },

        /**
         * Set null value for page_id property
         * This function uses when all of messages history pages received
         * and wants to prevent send additional request to API
         */
        historyFetchCompleted() {
            this.page_id = null;
        },

        /**
         * Returns a boolean that defines that all of messages history pages received or no
         */
        isHistoryCompleted() {
            return (this.page_id !== null);
        },

        /**
         * Plus one for page id .
         * This function uses when a page of message history received and
         * wants to prevent fetch again received page in next fetch requests
         */
        upPageId() {
            this.page_id++;
        },

        /**
         * Send message by enter key function
         * @param event
         */
        pressEnterKeyEvent(event) {
            if (event.keyCode === 13) {
                if (this.textInput !== '')
                    this.sendTxtMessage();
            }
        },

        /**
         * manually scrolling messages body to end
         */
        scrollBodyToBottom() {
            let body = document.getElementById(this.chatBodyId);

            body.scrollTop = body.scrollHeight
        },

        /**
         * Fire and execute when chat body scroll nd try to receive other last message history from server if scrolled to top
         */
        async chatBodyScrollEvent() {
            let body = document.getElementById(this.chatBodyId);

            if (body.scrollTop === 0 && this.isHistoryCompleted()) {
                await this.fetchMessageHistory();
            }
        },

        showImageFullscreen(img){
            this.fullscreenImage = img;
        },
        closeFullscreenImage(){
            this.fullscreenImage = null;
        }
    },
    mounted() {
        document.addEventListener('keyup', this.pressEnterKeyEvent)

        document.getElementById(this.chatBodyId).addEventListener('scroll', this.chatBodyScrollEvent)
    },
    watch: {
        /**
         * Watch and listen for new message. It will add and show new message and chat view when receive new message
         */
        async lastReceiveMessage() {
            this.pushMsg([this.lastReceiveMessage])

            this.scrollDown();
        },

        chatMessages(){
          this.scrollBodyToBottom();
        },

        /**
         * Show send text type message when text input is not empty and hide when is empty
         */
        textInput() {
            this.showSendTextMsgBtn = !(this.textInput === '')
            this.showSentFileMsgBtn = this.textInput === ''
        },

        /**
         * Update body division tag html content when its property changed
         */
        body() {
            let body = $("#" + this.chatBodyId);

            body.html(this.body)
        }
    }
}
</script>

<template>
    <div class="pos-relative height-100">
        <div class="messages-container fm-shabnam chat-background" style="padding-bottom: 50px; height: 92% !important;" id="chat-body"
             ref="messages">
            <div class="width-100 display-flex justify-content-center" v-if="!isConnectedToOperator && chatMessages.length === 0">
                <img src="../../imgs/loading.gif" width="25px" class="margin-x-auto">
            </div>
            <div v-for="message in chatMessages" :key="message.id"
                 :class="message.direction === 'right' ? 'receive' : 'send'">
                <div class="d-block">
                    <div class="message" :class="message.content.type === 'wizard' ? 'width-100' : ''">
                        <TextMessageComponent v-if="message.content.type === 'text'"
                                              :text="message.content.text"></TextMessageComponent>
                        <FileMessageComponent v-if="message.content.type === 'file'"
                                              :filename="message.content.filename"
                                              :uri="message.content.path" @ShowImageFullscreen="showImageFullscreen"></FileMessageComponent>
                        <FAQMessageComponent v-if="message.content.type === 'faq'" :html="message.content.html" @ShowImageFullscreen="showImageFullscreen"></FAQMessageComponent>
                        <WizardMenuComponent v-if="message.content.type === 'wizard'" :menu="message.content.list" :is-connected-to-operator="isConnectedToOperator"
                                             @ConnectToOperator="connectToOperator" @GoBackMenu="goBackWizardMenu"></WizardMenuComponent>
                        <MessageDetailsComponent :direction="message.direction" :time="message.time"
                                                 :status="message.status"></MessageDetailsComponent>
                    </div>
                </div>
            </div>
        </div>
        <div class="alerts position-absolute width-100 top text-center padding-x-4" style="z-index: 1000; top: 12px">
            <AlertComponent :type="alert.type" :message="alert.message" v-if="alert.type"></AlertComponent>
        </div>
        <div class="message-composer display-flex bordering-top fm-shabnam back-white" v-if="isConnectedToOperator">
            <i class="i-simple i-robot-background i-size-3 padding-top-3 padding-x-2 cursor-pointer" title="ربات پاسخگو" @click="sendWizardMenuTrigger" v-show="wizardMenuHistory.length === 0"></i>
            <input type="text" v-model="textInput" placeholder="پیامی بنویسید ..."
                   class="bordering-none radius-none text-input height-100 margin-y-0 padding-x-3 frm-control">
            <input type="file" style="visibility: hidden; width: 0" id="file-input" @change="sendFileMessage">
            <button type="button" class="padding-x-5 back-white button radius-none" style="padding-top: 13px"
                    v-show="showSentFileMsgBtn" onclick="document.getElementById('file-input').click()">
                <i class="i-simple i-paper-clip i-size-2"></i>
            </button>
            <button class="button padding-x-5 back-white radius-none" style="padding-top: 13px"
                    v-show="showSendTextMsgBtn" @click="sendTxtMessage">
                <i class="i-simple i-send i-size-2"></i>
            </button>
        </div>
    </div>
    <FullscreenImageDisplayer v-if="fullscreenImage" :img-tag="fullscreenImage" @CloseFullscreenImage="closeFullscreenImage"></FullscreenImageDisplayer>
</template>

<style scoped>

</style>
