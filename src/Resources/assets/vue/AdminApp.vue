<template>
    <i class="i-simple i-messages i-size-4 chat-btn cursor-pointer display-none" ref="chat-toggle" @click="show()"
       v-show="!boxVisibility"></i>
    <div class="webchat display-flex height-100 content-center fm-shabnam padding-0 direction-ltr " id="web-chat">
        <div class="colu-2 height-100 chat-list bordering-right" style="overflow: auto" @scroll="chatItemsListScrollEvent">
            <!-- BEIGN chat list section header  -->
            <div class="chat-list-header back-red display-flex content-between flex-reverse padding-3">
                <div class="display-flex align-center">
                    <span class="padding-right-3 text-color-light">لیست چت ها</span>
                    <i class="i-simple i-messages i-size-4 fa-2x text-color-light"></i>
                </div>
                <div class="display-flex align-center">
                    <i class="i-simple i-size-3 padding-top-2 i-gear text-color-light padding-right-3 cursor-pointer"
                       @click="openSetting"></i>
                </div>
            </div>
            <!-- END chat list section header  -->

            <!-- BEING chat list section items  -->
            <div class="chat-list-items position-relative" v-for="chat in chats" v-if="!inLoadingChatItems">
                <ChatItemComponent :info="chat" @openChat="openChatEvent"></ChatItemComponent>
            </div>
            <div class="chat-list-items position-relative text-align-center padding-top-2" v-if="inLoadingChatItems">
                <img src="../imgs/loading.gif" alt="" style="width: 25px">
            </div>
            <!-- END chat list section items  -->
        </div>


        <!-- BEING module body -->
        <div class="colu-8 body overflow-y-auto" id="webchat-admin-body" v-if="!loadingComponent">
            <ChatComponent v-if="componentData.name === 'chat'" :data="componentData.data"></ChatComponent>
            <CardFormComponent v-if="componentData.name === 'card-form'" :form-data="componentData.data"
                               @updated="cardUpdatedEvent"></CardFormComponent>
            <SettingComponent v-if="componentData.name === 'setting'"></SettingComponent>
        </div>
        <div class="colu-8 body text-align-center padding-top-2" id="webchat-admin-body" v-if="loadingComponent">
            <img src="../imgs/loading.gif" alt="" style="width: 25px">
        </div>
        <!-- END module body -->


        <div class="colu-2 back-white bordering-left overflow-y-auto" @scroll="cardItemsListScrollEvent">
            <!-- BEIGN cart list section header  -->
            <div class="cart-list-header back-red display-flex content-between flex-reverse padding-3">
                <div class="display-flex align-center">
                    <span class="padding-right-3 text-color-light">کارت های پشتیبان</span>
                    <i class="i-cards i-simple i-size-4 text-color-light"></i>
                </div>
                <div class="display-flex align-center">
                    <i class="i-simple i-plus-circle i-size-4 text-color-light cursor-pointer"
                       @click="openCreateCardForm"></i>
                </div>
            </div>
            <!-- END cart list section header  -->

            <!-- BEING cart list section items -->
            <div class="cart-list-items padding-left-1 padding-y-1" v-html="cardItemsHtml">
            </div>
            <!-- END cart list section items -->
        </div>
    </div>

</template>

<script>
import {Request} from "../js/Http/Request";
import $ from "jquery";
import ChatComponent from "./Components/ChatComponent.vue";
import CardFormComponent from "./Components/CardFormComponent.vue";
import ChatItemComponent from "./Components/ChatItemComponent.vue";
import SettingComponent from "./Components/Admin/Setting/SettingComponent.vue";

export default {
    components: {SettingComponent, ChatItemComponent, CardFormComponent, ChatComponent},
    data() {
        return {
            componentData: {
                name: ''
            },
            loadingComponent: false,

            appReady: false,// show open app btn when is true

            bodyHtml: '',
            bodyId: "webchat-admin-body",

            chats: [],
            inLoadingChatItems: false,
            allChatListPagesLoaded: false, // when last fetch request returns empty, this property set true that cause prevent send request
            loadChatItemsPerPage: 20,
            chatListPage: 1,
            openedChatId: null,

            cardItemsHtml: '',
            loadCardItemsPerPage: 20,
            inLoadingCardItems: false,
            allCardListPagesLoaded: false, // when last fetch request returns empty, this property set true that cause prevent send request
            cardListPage: 1,
            openedCardId: null,

            card_id_for_update: null, // keep id of card that client want update

            boxVisibility: false, // show or hide chat box section
            routes: { // API routes
                fetch_chats: "/chat",
                fetch_single_chat: "/chat/{chat_id}",
                open_chat: "/chat/{chat_id}",
                delete_chat: "/chat/{chat_id}",

                fetch_card: '/card/{card_id}',
                fetch_cards: '/card',
                delete_card: '/card/{card_id}',
                create_card: '/card/create',
                send_card: '/card/send/{card_id}'
            }
        }
    },


    setup() {
        const perpage = 10; // define paginate per page how many items should get on each fetch request

        return {
            perpage
        }
    },

    async created() {
        this.listenToNewChat()

        this.listenToChatOwnerWentOfflineEvent();

        this.listenToChatOwnerWentOnlineEvent();

        this.listenToDeleteChatEvent()

        this.listenToNewCardEvent();

        await this.reloadChatList();

        await this.reloadCardList();
    },
    mounted() {
        let instance = this;
        $(document).ready(function () {
            instance.appReady = true;
        })
    },

    methods: {
        /**
         * Show webchat section
         */
        show() {
            this.boxVisibility = true;
        },

        /**
         * Hide webchat section
         */
        hide() {
            this.boxVisibility = false;
        },

        /**
         * Listen to server through socket for new chat
         */
        listenToNewChat() {
            window.Echo.private(adminChannel)
                .listen(newChatEvent, (e) => {
                    this.addChatItem(e.chat)
                })
        },

        /**
         * Listen for chat owner went offline event
         */
        listenToChatOwnerWentOfflineEvent() {
            window.Echo.private(adminChannel)
                .listen(ownerWentOfflineEvent, (e) => {
                    this.changeChatItemOnlineStatus(e.chat_id, false)
                })
        },

        /**
         * Listen for chat owner went online event
         */
        listenToChatOwnerWentOnlineEvent() {
            window.Echo.private(adminChannel)
                .listen(ownerWentOnlineEvent, (e) => {
                    this.changeChatItemOnlineStatus(e.chat_id, true)
                })
        },

        /**
         * Listen to the delete chat event that will broadcast when a chat deleted
         */
        listenToDeleteChatEvent() {
            let instance = this;

            window.Echo.private(adminChannel)
                .listen(deleteChatEvent, (e) => {
                    instance.reloadChatList();
                })
        },

        listenToNewCardEvent() {
            let instance = this;

            window.Echo.private(adminChannel)
                .listen(newCardEvent, (e) => {
                    instance.pushFirstCardItemsHtml(e.item)
                })
        },

        /**
         * Listen to new message broadcast for update chat items new messages count and last message text
         */
        listenToNewMessages(){
            this.chats.forEach(function (item, index) {
                console.log(item)
                window.Echo.channel(item.channel)
                    .listen(newMessageEvent, (e) => {
                        item.unseen_messages_count += 1;
                        item.last_message_status = 'sent'
                        item.last_message = e.message.content.type === 'text' ? e.message.content.text : '';
                    })
            })
        },

        openSetting() {
            this.setComponentData('setting', []);
        },

        /**
         * Empty loaded chat list then reload that
         */ async reloadChatList() {
            this.chatListPage = 1;

            this.chatItemsHtml = '';

            await this.fetchChatList();
        },

        /**
         * Finds chat item with chat_id entry and set its owner online status offline
         *
         * @param chat_id
         * @param online
         */
        async changeChatItemOnlineStatus(chat_id, online) {
            let instance = this;

            let found = false;

            await new Promise(function (resolve, reject) {
                instance.chats.forEach(function (item, index) {
                    if (item.id === chat_id) {
                        instance.chats[index].owner_online = online;
                        found = true;
                    }
                })

                resolve()
            })

            if (found)
                await this.reorderChatList();
        },

        /**
         * Add an item to chats item list
         *
         * @param chat
         */
        async addChatItem(chat) {
            this.chats.push(chat)
            await this.reorderChatList()
        },

        /**
         * Re-orders chat list for put online chat items at first of list
         */
        async reorderChatList() {
            this.inLoadingChatItems = true;

            let instance = this;

            let online = [];

            let offline = [];

            await new Promise(function (resolve, reject) {
                instance.chats.forEach(function (item) {
                    item.owner_online ? online.push(item) : offline.push(item);
                });

                resolve();
            })

            this.chats = []

            setTimeout(function () {
                instance.chats = online.concat(offline)

                instance.inLoadingChatItems = false;
            }, 500);
        },

        /**
         * Takes exists chat items from server and display in UI
         *
         * @returns {Promise<void>}
         */
        async fetchChatList() {
            this.inLoadingChatItems = true;

            await Request.get().url(this.routes.fetch_chats).data({
                perpage: this.loadChatItemsPerPage,
                page: this.chatListPage
            }).success(function (response, instance) {
                let responseJson = JSON.parse(response);
                if (responseJson.chats.length === 0)
                    instance.allChatListPagesLoaded = true;
                instance.chats = instance.chats.concat(responseJson.chats);
                instance.chatListPage++;

                instance.listenToNewMessages()
            }).error(function () {
                console.error('There are error in load chat list items !')
            }).use(this).asyncSend();

            this.inLoadingChatItems = false;

            await this.reorderChatList();
        },

        /**
         * set componentData property value
         *
         * @param name
         * @param data
         */
        setComponentData(name, data) {
            this.loadingComponent = true;

            let instance = this;

            this.componentData = {
                name: '',
            };

            setTimeout(function () {
                instance.componentData.name = name;
                instance.componentData.data = data;

                instance.loadingComponent = false;
            }, 500)
        },

        /**
         * Endless scrolling event for chat items list
         *
         * @param e
         */
        chatItemsListScrollEvent(e) {
            let ele = e.target;

            if ((ele.scrollTop + ele.offsetHeight) === ele.scrollHeight && !this.inLoadingChatItems && !this.allChatListPagesLoaded) {
                this.fetchChatList();
            }
        },

        /**
         * Empty loaded card list then reload that
         */
        reloadCardList() {
            this.cardListPage = 1;

            this.cardItemsHtml = '';

            this.fetchCardList();
        },

        /**
         * Takes exists card items from server and display in UI
         *
         * @returns {Promise<void>}
         */
        async fetchCardList() {
            this.inLoadingCardItems = true;

            await Request.get().url(this.routes.fetch_cards).data({
                perpage: this.loadCardItemsPerPage,
                page: this.cardListPage,
                format: 'html'
            }).success(function (response, instance) {
                if (response === '')
                    instance.allCardListPagesLoaded = true;
                instance.pushCardItemsHtml(response);
                instance.cardListPage++;
            }).error(function () {
                console.error('There are error in load card list items !')
            }).use(this).asyncSend();

            this.inLoadingCardItems = false;
        },

        /**
         * Insert (html) argument at start of card items html
         *
         * @param html
         */
        pushFirstCardItemsHtml(html) {
            this.cardItemsHtml = html + this.cardItemsHtml;
        },

        /**
         * Insert (html) argument at end of card items html
         *
         * @param html
         */
        pushCardItemsHtml(html) {
            this.cardItemsHtml = this.cardItemsHtml + html;
        },

        /**
         * Endless scrolling event for chat items list
         *
         * @param e
         */
        cardItemsListScrollEvent(e) {
            let ele = e.target;

            if ((ele.scrollTop + ele.offsetHeight) === ele.scrollHeight && !this.inLoadingCardItems && !this.allChatListPagesLoaded) {
                this.fetchCardList();
            }
        },

        /**
         * set on click event for delete and open chat buttons
         */
        setCardItemBtnsEvent() {
            let instance = this;

            setTimeout(function () {
                let delete_btns = document.getElementsByClassName('del-card-btn');
                instance.setDeleteCardBtnClickEvent(delete_btns);

                let update_btns = document.getElementsByClassName('update-card-btn');
                instance.setUpdateCardBtnClickEvent(update_btns);

                let send_btns = document.getElementsByClassName('send-card-btn');
                instance.setSendCardBtnClickEvent(send_btns);
            }, 2000)
        },

        /**
         * Sets click event handler for delete card buttons
         *
         * @param buttons
         */
        setDeleteCardBtnClickEvent(buttons) {
            let instance = this;

            for (let x = 0; x < buttons.length; x++) {
                let card_id = buttons[x].getAttribute('data-card-id');

                buttons[x].addEventListener('click', function () {
                    instance.deleteCardEventHandler(card_id);
                })
            }
        },

        /**
         * Sets click event handler for update card buttons
         *
         * @param buttons
         */
        setUpdateCardBtnClickEvent(buttons) {
            let instance = this;

            for (let x = 0; x < buttons.length; x++) {
                let card_id = buttons[x].getAttribute('data-card-id');

                buttons[x].addEventListener('click', function () {
                    instance.updateCardEventHandler(card_id);
                })
            }
        },

        /**
         * Sets click event handler for send card buttons
         *
         * @param buttons
         */
        setSendCardBtnClickEvent(buttons) {
            let instance = this;

            for (let x = 0; x < buttons.length; x++) {
                let card_id = buttons[x].getAttribute('data-card-id');

                buttons[x].addEventListener('click', function () {
                    instance.sendCardHandler(card_id);
                })
            }
        },

        /**
         * Removes the element that has entry card_id from cards list
         *
         * @param card_id
         */
        removeCardItem(card_id) {
            $("[data-card-id=" + card_id + "]").remove();

            this.componentData = {
                name: ''
            }
        },

        /**
         * Replaces card_id in related url then return that
         *
         * @param card_id
         */
        getDeleteCardUrl(card_id) {
            return this.routes.delete_card.replace("{card_id}", card_id);
        },

        /**
         * Sends delete card request to server
         *
         * @param card_id
         */
        deleteCardEventHandler(card_id) {
            Request.delete().url(this.getDeleteCardUrl(card_id))
                .data({
                    _token: csrf,
                })
                .success(function (response, instance) {
                    instance.removeCardItem(card_id)
                }).error(function (response, instance) {
                console.error('There are some errors in delete card');
            }).use(this).asyncSend();
        },

        /**
         * Sends update card request to server for take update card form
         *
         * @param card_id
         */
        updateCardEventHandler(card_id) {
            this.card_id_for_update = card_id;
        },

        /**
         * Dispatch when card updated successful and set null for card_id_for_update property
         */
        cardUpdatedEvent() {
            this.card_id_for_update = null;
        },

        /**
         * Set same value for fetch chat items request then return that
         */
        fetchRequestObject() {
            return Request
                .get().url(this.routes.fetch_chats)
                .error(function (response) {
                    console.error(response);
                })
                .use(this);
        },

        /**
         * Returns get chat url regarded chat id
         * @param chat_id
         * @returns {string}
         */
        getChatUrl(chat_id) {
            return this.routes.fetch_single_chat.replace('{chat_id}', chat_id);
        },

        /**
         * set on click event for delete and open chat buttons
         */
        setChatItemBtnsEvent() {
            let instance = this;

            setTimeout(function () {
                let open_btns = document.querySelectorAll('.open-chat-btn , .open-chat-btn *');
                instance.setOpenBtnClickEvent(open_btns);

                let delete_btns = document.getElementsByClassName('del-chat-btn');
                instance.setDeleteBtnClickEvent(delete_btns);
            }, 2000)
        },

        /**
         * Set open chat button click event handler
         *
         * @param btns
         */
        setOpenBtnClickEvent(btns) {
            for (let x = 0; x < btns.length; x++)
                btns[x].addEventListener('click', this.openChatEvent)
        },

        /**
         * Set delete chat button click event handler
         *
         * @param btns
         */
        setDeleteBtnClickEvent(btns) {
            for (let x = 0; x < btns.length; x++)
                btns[x].addEventListener('click', this.deleteChatEvent)
        },

        /**
         * Returns the delete chat api url
         *
         * @param chat_id
         * @returns {string}
         */
        deleteChatUrl(chat_id) {
            return this.routes.delete_chat.replace('{chat_id}', chat_id);
        },

        /**
         * Delete chat event
         *
         * @param e
         * @returns {Promise<void>}
         */
        async deleteChatEvent(e) {
            let targetElement = e.target;

            if (targetElement.tagName.toLowerCase() === 'i') {
                targetElement = targetElement.parentElement
            }

            let chatId = targetElement.getAttribute('data-chat-id');

            await this.deleteChatRequest(chatId);
        },

        /**
         * Delete chat request object
         *
         * @param chat_id
         * @returns {Promise<void>}
         */
        async deleteChatRequest(chat_id) {
            await Request
                .delete()
                .data({
                    _token: csrf
                })
                .url(this.deleteChatUrl(chat_id))
                .asyncSend();
        },

        /**
         * Get chat view from server and open in body
         * @param chatId
         */
        async openChatEvent(chatId) {
            await this.openChatRequest(chatId);

            this.openedChatId = chatId;
        },

        /**
         * Send request to server for get chat view and information for open and show that
         * @param chat_id
         * @returns {Promise<void>}
         */
        async openChatRequest(chat_id) {
            await Request
                .get()
                .url(this.getChatUrl(chat_id))
                .success(function (response, instance) {
                    console.log(response)
                    instance.setComponentData('chat', JSON.parse(response))
                })
                .error(function (response) {

                }).use(this)
                .asyncSend();
        },

        /**
         * Make display show create card form
         *
         * @returns {Promise<void>}
         */
        async openCreateCardForm(e) {
            this.setComponentData('card-form', {
                card_id: null,
                name: '',
                shortcut: '',
                messages: [
                    {
                        type: "text",
                        value: null
                    }
                ]
            })
        },

        /**
         * Send request to server for get card info for pass to update form
         *
         * @returns {Promise<void>}
         */
        async openUpdateCardForm() {
            await Request.get().url(this.fetchCardApiUrl()).success(function (response, instance) {
                let data = JSON.parse(response).data;
                instance.setComponentData('card-form', {
                    card_id: data.id,
                    name: data.name,
                    shortcut: data.shortcut,
                    messages: data.messages,
                })
            }).error(function (response) {
                console.error(response)
            }).use(this).asyncSend();
        },

        /**
         * Fetch card info api url address
         *
         * @returns {string}
         */
        fetchCardApiUrl() {
            return this.routes.fetch_card.replace('{card_id}', this.card_id_for_update);
        },

        /**
         * Sends the selected card to opened chat that its id set in openedChatId.
         * This function should set as send card btn click event handler
         *
         * @param card_id
         */
        sendCardHandler(card_id) {
            if (!this.openedChatId || this.componentData.name !== 'chat')
                alert('چت مورد نظر را باز کنید !')
            else {
                Request
                    .post()
                    .url(this.sendCardApiUrl(card_id))
                    .data({
                        '_token': csrf,
                        'chat_id': this.openedChatId
                    })
                    .success(function (response, instance) {
                        let responseJson = JSON.parse(response);

                        alert(responseJson.message);
                    })
                    .error(function (response, instance) {
                        alert('وجود خطا در ارسال کارت !')

                        console.error(response)
                    })
                    .use(this)
                    .asyncSend();
            }
        },

        /**
         * Returns the send card API`s url
         *
         * @param card_id
         * @returns {string}
         */
        sendCardApiUrl(card_id) {
            return this.routes.send_card.replace('{card_id}', card_id);
        }
    },
    watch: {
        bodyHtml() {
            $("#" + this.bodyId).html(this.bodyHtml)
        },

        chatItemsHtml() {
            let thisClass = this;

            setTimeout(function () {
                thisClass.setChatItemBtnsEvent();
            }, 1500)
        },

        cardItemsHtml() {
            let thisClass = this;

            setTimeout(function () {
                thisClass.setCardItemBtnsEvent();
            }, 1500);
        },

        appReady() {
            let ele = this.$refs["chat-toggle"];

            $(ele).removeClass('display-none');
        },

        card_id_for_update() {
            this.openUpdateCardForm();
        }
    }
}
</script>

<style scoped>

</style>
