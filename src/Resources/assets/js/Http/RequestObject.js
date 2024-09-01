export class RequestObject
{
    /**
     * @type {string}
     */
    url = '';

    /**
     * @type {[]}
     */
    data = [];

    /**
     * @type {string}
     */
    method = '';

    /**
     * @type {{}}
     */
    headers = {
        'accept' : 'application/json'
    };

    /**
     * @message Get request object headers
     * @returns {{}}
     */
    get headers() {
        return this.headers;
    }

    /**
     * @message Set request object headers
     * @param value
     */
    set headers(value) {
        this.headers = value;
    }

    /**
     * @message set method property value
     * @param method
     */
    constructor(method = 'GET') {
        this.method = method;
    }

    /**
     * @message get request object url
     * @returns {string}
     */
    get url() {
        return this.url;
    }

    /**
     * @message set request object url
     * @param value
     */
    set url(value) {
        this.url = value;
    }

    /**
     * @message get request object data in correct format
     * @returns {string}
     */
    getStreamData() {
        let dataString = '';

        for(let key in this.data){
            dataString += dataString === '' ? key + '=' + this.data[key] : '&' + key + '=' + this.data[key];
        }
        return dataString;
    }

    /**
     * @message set request object data
     * @param value
     */
    set data(value) {
        this.data = value;
    }

    /**
     * @message get request object method
     * @returns {string}
     */
    get method() {
        return this.method;
    }

    /**
     * @message set request object method
     * @param value
     */
    set method(value) {
        this.method = value;
    }
}
