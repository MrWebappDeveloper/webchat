export class FormError {
    // validation errors
    errors = {};

    // under validation form element
    form = null;

    // const
    constructor(errors , form) {
        // set errors property
        this.errors = errors;

        // set form property
        this.form = form;

        // reset form error validation
        this.resetFormErrors();

        // print errors
        this.printErrors();
    }

    // this function gonna to remove (is-invalid) class of all input,select,textarea and etc
    resetFormErrors() {
        // set self var for make access to class in loop
        let self = this;

        // select all inputs
        let inputs = this.form.querySelectorAll('select,input,textarea');

        // iterate inputs
        inputs.forEach(function(input){
            // remove (is-invalid) class
            $(input).removeClass('was-invalid');

            // add (is-valid) class if input valid is`t emtpy
            if($(input).val() !== "")
                $(input).addClass('was-valid');

            // find feedback element
            let feedbackElement = self.findFeedbackElement(input);

            // remove feedbackElement
            $(feedbackElement).remove();
        });
    }

    printErrors() {

        for(let [key , message] of Object.entries(this.errors)){
            // get related input to it error
            let input = this.getInput(key)

            if(input){
                // define as invalid
                this.addInvalidClass(input)

                // append feedback message for print error message
                this.appendFeedbackMessage(input , message[0])
            }
        }
    }

    getInput(name){
        if (name.includes('.')){
            let nameArray = name.split('.');

            name = nameArray[0];

            nameArray.splice(0,1);

            nameArray.forEach(function(indexName){
                name += '[' + indexName + ']';
            })

            console.log(name)
        }

        // select input from form
        let input = this.form.querySelector('[name="' + name + '"]')

        // return input
        return input;
    }

    addInvalidClass(input) {
        // remove is-valid class
        $(input).removeClass('was-valid');

        // add is-invalid class to input
        $(input).addClass('was-invalid');
    }

    appendFeedbackMessage(input , message) {
        // check that feedback element is exists
        let feedbackElement = this.findFeedbackElement(input)

        // check feedback element exists or no
        if(feedbackElement) {
            // set text for display message
            $(feedbackElement).text(message);
        } else {
            // let parent element of input
            let parent = input.parentElement;

            // make element
            feedbackElement = this.makeInvalidFeedbackElement();

            // set text for display message
            $(feedbackElement).text(message);

            // append feedback element to input parent element
            $(parent).append(feedbackElement);
        }
    }

    // this function gonna to find element that has (invlida-feedback) class on input parent element
    findFeedbackElement(input){
        // select input parent element
        let parent = input.parentElement;

        // check that feedback element is exists
        let feedbackElement = parent.querySelector('.invalid-message')

        // return element
        return feedbackElement;
    }

    makeInvalidFeedbackElement() {
        // make element
        let ele = document.createElement('span');

        // add class
        $(ele).addClass('invalid-message');

        // return element
        return ele;
    }
}
