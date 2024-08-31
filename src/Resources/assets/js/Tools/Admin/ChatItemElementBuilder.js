export class ChatItemElementBuilder
{
    static makeOnlineChatItem(chat_id, email, phone, lastMsgText = "", unseenMessagesCount = null){
        return ChatItemElementBuilder.#createItem(chat_id, email, phone, lastMsgText, unseenMessagesCount)
    }

    static makeOfflineChatItem(chat_id, email, phone, lastMsgText = "", unseenMessagesCount = null){
       return ChatItemElementBuilder.#createItem(chat_id, email, phone, lastMsgText, unseenMessagesCount)
    }

    static #createItem(chat_id, email, phone, lastMsgText = "", unseenMessagesCount = null){
        return " <li class=\"chat-item bordering-bottom padding-3 position-relative display-flex align-center\">\n" +
            "                        <p class=\"text-md padding-1 text-color-dark\">\n" +
            "                            <span>ایمیل :</span>\n" +
            "                            <span>" + email + "</span>\n" +
            "                        </p>\n" +
            "                        <div class=\"width-100 display-flex justify-content-end\">\n" +
            "                            <button class=\"button button-sm button-danger margin-x-1 del-chat-btn\" data-chat-id='" + chat_id + "''><i class=\"fa-solid fa-trash padding-x-1 text-sm\"></i></button>\n" +
            "                            <button class=\"button button-sm button-primary open-chat-btn\" data-chat-id='" + chat_id + "''><i class=\"fa-solid fa-message padding-x-1 text-sm\"></i></button>\n" +
            "                        </div>\n" +
            "                        <span class=\"unseen-messages-count" + (unseenMessagesCount != 0 ? '' : ' display-none') + "\">" + unseenMessagesCount + "</span>\n" +
            "                    </li>"
    }
}

