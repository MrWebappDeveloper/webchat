import {Helper} from "../Helper";
import $ from "jquery";

export class MessageElementBuilder
{
    static #instance

    static #messageContainerElement()
    {
        return Helper.makeDocumentElement('div' ,['d-block'])
    }

    static #messageElement()
    {
        return Helper.makeDocumentElement('div', ['message']);
    }

    static #textMessageContentElement(text)
    {
        let ele = Helper.makeDocumentElement('span');

        ele.innerHTML = Helper.makeLinkUrls(text);

        return ele;
    }

    static #imgMessageContentElement(src, alt = null)
    {
        let ele = Helper.makeDocumentElement('img');

        ele.src = src;

        if(alt)
            ele.alt = alt;

        ele.classList.add('image-file-message')

        return ele;
    }

    static #fileMessageContentElement(linkAddress, filename)
    {
        let row = Helper.makeDocumentElement('div', ['row', 'd-flex', 'align-items-center', 'px-3']);

        let container = Helper.makeDocumentElement('div', ['col-3', 'mr-3', 'd-flex', 'align-items-center', 'justify-content-center', 'bg-light', 'rounded-circle'])

        container.style.width = '50px'
        container.style.height = '50px'

        row.appendChild(container);

        let link = Helper.makeDocumentElement('a');
        link.setAttribute('target', '_blank');
        link.href = linkAddress;

        let icon = Helper.makeDocumentElement('i', ['i-simple', 'i-download', 'text-secondary']);
        icon.style.fontSize = "20px";
        link.appendChild(icon)

        container.appendChild(link)

        let filenameContainer = Helper.makeDocumentElement('div', ['col', 'px-3']);

        let filenameEle = Helper.makeDocumentElement('span', ['d-block']);
        filenameEle.innerText = filename;

        filenameContainer.appendChild(filenameEle);

        row.appendChild(filenameContainer);

        return row;
    }

    static #messageDetailsElement(time = null, seen = false)
    {
        let container = Helper.makeDocumentElement('div', ['message-detail']);

        let statusIcon = Helper.makeDocumentElement('span', ['status-icon', 'i-simple', (seen ? 'i-check-double' : 'i-check')])
        container.appendChild(statusIcon);

        let timeEle = Helper.makeDocumentElement('span');
        timeEle.innerText = time ?? Helper.getCurrentTimeHoureMinute()
        container.appendChild(timeEle);

        return container;
    }

    static fileMessage(path, filename, time = null, seen = null)
    {
        let instance = MessageElementBuilder;

        let fileMsg = instance.#fileMessageContentElement(Helper.getDomain() + '/' + path, filename);

        return instance.#outputMessage(fileMsg, time, seen);
    }

    static textMessage(text, time = null, seen = null)
    {
        let instance = MessageElementBuilder;

        let txtMsg = instance.#textMessageContentElement(text);

        return instance.#outputMessage(txtMsg, time, seen);
    }

    static imageMessage(path, time = null, seen = null)
    {
        let instance = MessageElementBuilder;

        let imgMsg = instance.#imgMessageContentElement(Helper.getDomain() + '/' + path)

        return instance.#outputMessage(imgMsg, time, seen);
    }

    static #outputMessage(contentElement, messageTime, messageSeenStatus)
    {
        let instance = MessageElementBuilder;

        let container = instance.#messageContainerElement();

        let message = instance.#messageElement();

        let detail = instance.#messageDetailsElement(messageTime, messageSeenStatus);

        container.appendChild(message);

        message.appendChild(contentElement);

        message.appendChild(detail)

        return container;
    }

    static receiveMessagesContainer(message)
    {
        let ele = Helper.makeDocumentElement('div', ['receive']);

        ele.appendChild(message);

        return ele;
    }

    static sendMessagesContainer(message)
    {
        let ele = Helper.makeDocumentElement('div', ['send']);

        ele.appendChild(message);

        return ele;
    }

    static oldSendMessagesSeen()
    {
        $('.send .status-icon:not(.i-check-double)').removeClass('i-check').addClass('i-check-double')
    }
}





