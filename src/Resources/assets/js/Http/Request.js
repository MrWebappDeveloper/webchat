import {RequestObject} from "./RequestObject.js";

export class Request
{
    /**
     * @message request object instance
     */
    #requestObject;

    /**
     * @message the success callback function when response is 200
     * @type {function}
     */
    #successHandler;

    /**
     * @message the error callback function when response is not 200
     * @type {function}
     */
    #errorHandler;

    /**
     * @message The use variable that attach to request response handler for access in callback function
     * @type {*}
     */
    #use;

    /**
     * @message singleton property
     * @type {Request}
     */
    static instance;

    /**
     * @message singleton function for get the instance of class
     * @returns {Request}
     */
    static #instance()
    {
        Request.instance = null;

        Request.instance = new Request();

        return Request.instance;
    }

    /**
     * @message Get the request object value
     * @returns {RequestObject}
     */
    get requestObject() {
        return this.#requestObject;
    }

    /**
     * @message Set the request object property value
     * @param value
     */
    set requestObject(value) {
        this.#requestObject = value;
    }



    /**
     * @message create new RequestObject instance by post method and set to the property
     * @returns {Request}
     */
    static post()
    {
        // get singleton instance
        let instance = Request.#instance();

        instance.#requestObject = new RequestObject('POST');

        // return instance for make chain method and open access to non-static property and methods for client
        return instance;
    }

    /**
     * @message create new RequestObject instance by get method and set to the property
     * @returns {Request}
     */
    static get()
    {
        // get singleton instance
        let instance = Request.#instance();

        instance.#requestObject = new RequestObject('GET');

        // return instance for make chain method and open access to non-static property and methods for client
        return instance;
    }

    /**
     * @message create new RequestObject instance by put method and set to the property
     * @returns {Request}
     */
    static put()
    {
        // get singleton instance
        let instance = Request.#instance();

        instance.#requestObject = new RequestObject('PUT');

        // return instance for make chain method and open access to non-static property and methods for client
        return instance;
    }

    /**
     * @message create new RequestObject instance by delete method and set to the property
     * @returns {Request}
     */
    static delete()
    {
        // get singleton instance
        let instance = Request.#instance();

        instance.#requestObject = new RequestObject('DELETE');

        // return instance for make chain method and open access to non-static property and methods for client
        return instance;
    }

    /**
     * @message set request object url value
     * @param value
     * @returns {Request}
     */
    url(value)
    {
        this.#requestObject.url = value;

        return this;
    }

    /**
     * @message set request object data value
     * @param value
     * @returns {Request}
     */
    data(value)
    {
        this.#requestObject.data = value;

        return this;
    }

    getData()
    {
        let formdata = new FormData();

        let data = this.#requestObject.data;

        Object.keys(data).forEach(function(key) {
            formdata.append(key, data[key]);
        })

        return formdata;
    }

    /**
     * @message set request object method value
     * @param value
     * @returns {Request}
     */
    method(value)
    {
        this.#requestObject.method = value;

        return this;
    }

    /**
     * @message set request success response handler
     * @param handler
     * @returns {Request}
     */
    success(handler)
    {
        this.#successHandler = handler;

        return this;
    }

    /**
     * @message set request error response handler
     * @param handler
     * @returns {Request}
     */
    error(handler)
    {
        this.#errorHandler = handler;

        return this;
    }

    /**
     * @message set request use (attach) variable property
     * @param mixed
     * @returns {Request}
     */
    use(mixed)
    {
        this.#use = mixed;

        return this;
    }

    /**
     * @message Set custom header for request
     * @param json
     */
    headers(json)
    {
        this.#requestObject.headers(json);

        return this;
    }

    /**
     * @message Push a new header to request object headers
     * @param key
     * @param value
     * @returns {Request}
     */
    addHeader(key, value)
    {
        let header = JSON.parse('{ "' + key + '" : "' + value + '" }');

        this.#requestObject.headers = {...header, ...this.#requestObject.headers};

        return this;
    }

    /**
     * @message Iterate object request headers and set for xhr
     * @param xhr
     */
    #assignHeadersToRequest(xhr)
    {
        let headers = this.#requestObject.headers;

        for(let key in headers)
        {
            xhr.setRequestHeader(key, headers[key]);
        }
    }

    /**
     * @message Send AJAX request
     */
    send()
    {
        let thisClass = this;

        let xhr = new XMLHttpRequest();

        xhr.open(this.requestObject.method, this.requestObject.url + (this.#requestObject.method === 'GET' ? ('?' + this.#requestObject.getStreamData()) : ''), true);

        this.#assignHeadersToRequest(xhr)

        xhr.onload = function () {
            if (xhr.status === 200){
                if(thisClass.#successHandler)
                    thisClass.#successHandler(xhr.response, thisClass.#use) // call success response handler
            }
            else{
                if(thisClass.#errorHandler)
                    thisClass.#errorHandler(xhr.response, thisClass.#use) // call error response handler
            }
        };

        xhr.onerror = function () {
            console.error('Request failed');
        };

        this.#sendXHR(xhr) // send request with data
    }

    /**
     * @message Send AJAX request async
     */
    async asyncSend()
    {
        try{
            let thisClass = this;

            let xhr = new XMLHttpRequest();

            xhr.open(this.requestObject.method, this.requestObject.url + (this.#requestObject.method = 'GET' ? ('?' + this.#requestObject.getStreamData()) : ''), true);

            this.#assignHeadersToRequest(xhr)

            const responsePromise = new Promise(((resolve, reject) => {
                xhr.onload = function () {
                    resolve(xhr);
                };

                xhr.onerror = function () {
                    reject('AJAX request error !') // call error response handler
                };
            }))

            this.#sendXHR(xhr) // send request with data

            const response = await responsePromise;

            if (response.status === 200){
                if(thisClass.#successHandler)
                    thisClass.#successHandler(xhr.response, thisClass.#use) // call success response handler
            }
            else{
                if(thisClass.#errorHandler)
                    thisClass.#errorHandler(xhr.response, thisClass.#use) // call error response handler
            }
        } catch (error){
            console.error(error)
        }
    }

    #sendXHR(xhr)
    {
        if (this.#requestObject.method === 'GET'){
            xhr.send(); // send request by data
        }
        else{
            xhr.send(this.getData());
        }
    }
}
