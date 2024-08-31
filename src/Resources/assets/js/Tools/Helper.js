export class Helper
{
    static inArray(needle, haystack) {
        let length = haystack.length;
        for(let i = 0; i < length; i++) {
            if(haystack[i] === needle) return true;
        }
        return false;
    }

    static getDomain(){
        return window.location.protocol + "//" + window.location.host;
    }

    static url(uri){
        return Helper.getDomain() + "/" + uri.trim()
    }

    static getCurrentTimeHoureMinute(){
        let date = new Date()

        let hours = date.getHours().toString().length === 1 ? '0' + date.getHours() : date.getHours();

        let minutes = date.getMinutes().toString().length === 1 ? '0' + date.getMinutes() : date.getMinutes();

        return  hours + ":" + minutes;
    }

    static lastIndex(array)
    {
        return array[array.length - 1];
    }

    static makeDocumentElement(tagName, classes = [], id = null)
    {
        let ele = document.createElement(tagName);

        if(id)
            ele.id = id;

        if(classes)
            classes.forEach(function(value, index){
                ele.classList.add(value);
            })

        return ele;
    }

    static makeLinkUrls(text){
        let pattern = /\b(?:https?|ftp):\/\/[-\w+&@#/%?=~_|!:,.;]*[-\w+&@#/%=~_|]/g;

        let links = text.match(pattern);

        if(links){
            links.forEach(function(item){
                text = text.replace(item , Helper.convertUlrTo_a_tag(item))
            });
        }


        return text;
    }

    static convertUlrTo_a_tag(url){
        return "<a href='" + url + "' target='_blank'>" + url + "</a>"
    }

    static generateRandomNumber(len){
        const min = Math.pow(10, (len-1));
        const max = Math.pow(10, (len));
        return Math.floor(Math.random() * (max - min) + min);
    }

    static getTimeNow(){
        let date = new Date;

        let hours = date.getHours();

        let minutes = date.getMinutes();

        return (hours < 10 ? "0" + hours : hours) + ":" +(minutes < 10 ? "0" + minutes : minutes)
    }
}
