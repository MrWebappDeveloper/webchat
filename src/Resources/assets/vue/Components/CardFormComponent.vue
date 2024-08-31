<template>
    <!--BEGIN : response content-->
    <div v-if="successful" class="display-flex content-center padding-y-5">
        <div class="colu-5 box-shadow text-align-center border-radius padding-5">
            <i class="fa-regular fa-circle-check fa-5x text-color-success"></i>
            <p class="text-align-center padding-top-5" v-if="!successMessage">کارت جدید با موفقیت ثبت شد !</p>
            <p class="text-align-center padding-top-5" v-if="successMessage">{{ successMessage }}</p>
            <button class="button button-primary button-sm margin-top-3" @click="confirmSuccess">تایید</button>
        </div>
    </div>
    <!--END : response content-->
    <div class="create-cart-conatiner width-100 height-100 display-flex content-center align-center flex-dir-column margin-y-5" v-if="!successful">
        <div class="colu-8 border-radius box-shadow">
            <!-- BEIGN create cart form section header  -->
            <div class="chat-list-header back-red display-flex content-between padding-3 border-radius-top">
                <div class="display-flex align-center">
                    <i class="i-cards i-simple i-size-3 text-color-light"></i>
                    <span class="padding-right-3 text-color-light" v-if="!card.card_id">اضافه کردن کارت جدید</span>
                    <span class="padding-right-3 text-color-light" v-if="card.card_id">ویرایش کارت ' {{ formData.name }} '</span>
                </div>
            </div>
            <!-- END create cart form section header  -->
            <form action="" @submit="submit" class="padding-y-4 row-container content-between padding-x-5 card-form">
                <div class="frm-group colu-6">
                    <input type="text" class="frm-control" placeholder="عنوان ..." name="name" v-model="card.name">
                    <span class="invalid-message"></span>
                </div>
                <div class="frm-group colu-6">
                    <input type="text" class="frm-control" placeholder="میانبر ..." name="shortcut" v-model="card.shortcut">
                    <span class="invalid-message"></span>
                </div>
                <div class="frm-group colu-12">
                    <p class="text-color-dark padding-bottom-3">لیست پیام های کارت</p>
                    <table class="table-simple">
                        <thead>
                        <tr>
                            <th>نوع پیام</th>
                            <th>محتوا</th>
                            <th class="text-align-center">عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(message, index) in card.messages">
                            <td>
                                <div class="frm-group display-block">
                                    <select class="frm-control" v-model="message.type">
                                        <option value="text" :selected="message.type === 'text'">متن</option>
                                        <option value="file" :selected="message.type === 'file'">فایل</option>
                                    </select>
                                    <span class="invalid-message">Hello world !</span>
                                </div>
                            </td>
                            <td>
                                <div class="frm-group display-block padding-y-0 position-relative">
                                    <div class="frm-control margin-top-2 bordering padding-y-2 display-flex content-between align-center" v-if="message.type === 'file'">
                                        <span class="text-color-dark filename-text" v-if="!message.filename">لطفا فایلی را انتخاب کنید ...</span>
                                        <span class="text-color-dark filename-text" v-if="message.filename">{{ message.filename }}</span>
                                        <button class="button button-sm padding-x-3" type="button" :data-index="index" @click="selectFile"><i :data-index="index" class="i-simple i-upload padding-x-1"></i></button>
                                    </div>
                                    <input type="file" :name="'messages['+ index +'][value]'" v-if="message.type === 'file'" class="visibility-hidden width-0 height-0 position-absolute frm-control" :data-index="index" @change="messageFileSelected">
                                    <textarea :name="'messages['+ index +'][value]'" v-model="message.value" v-if="message.type === 'text'" :data-index="index" class="frm-control" rows="1" placeholder="متن پیام را وارد کنید ..." @keyup="updateMessagesRows"></textarea>
                                    <span class="invalid-message display-block"></span>
                                </div>
                            </td>
                            <td class="text-align-center padding-top-5">
                                <div class="padding-top-1">
                                    <i class="i-simple i-trash-danger text-color-danger cursor-pointer" :data-index="index" @click="removeMessageRow"></i>
                                    <i class="i-simple i-arrow-up padding-x-2 text-color-primary cursor-pointer" @click="addNewRowBeforeCurrent"></i>
                                    <a :href="message.link" v-if="message.link" target="_blank" class="text-color-primary padding-left-3">
                                        <i class="i-simple i-eye"></i>
                                    </a>
                                    <i class="i-simple i-arrow-bottom text-color-primary cursor-pointer" @click="addNewRowAfterCurrent" :data-index="index"></i>
                                </div>
                            </td>
                        </tr>
                        </tbody>

                    </table>
                </div>
                <div class="frm-group colu-3">
                    <button class="button button-danger width-100 text-center">
                        <span v-if="!card.card_id && !processing">ثبت</span>
                        <span v-if="card.card_id && !processing">ویرایش</span>
                        <img v-if="processing" src="../../imgs/loading.gif" alt=""  style="width: 15px; left: 10px; top: 30%">
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>


import {Request} from "../../js/Http/Request";
import {FormError} from "../../js/Tools/FormError";
import $ from "jquery";

export default {
    props:{
        formData:{
            card_id: Number,
            name: String,
            shortcut: String,
            messages: JSON,
        }
    },
    data(){
        return {
            csrf:null,
            successful: false,
            successMessage: null,
            processing:false, // will true when form submit

            card:{
                card_id: (this.formData.card_id ?? null), // if be filled , form will send to update card api
                name: this.formData.name,
                shortcut: this.formData.shortcut,
                messages:this.formData.messages,
            },

            routes:{
                'store_card' : '/card',
                'update_card' : '/card/{card_id}'
            }
        }
    },
    created() {
        if(!this.formData.card_id){
            this.reset();
        }

        this.csrf = csrf;
    },
    methods:{
        /**
         * resets component
         */
        reset(){
            this.successful = false;
            this.successMessage = null;
            this.processing = false;

            if(this.card.card_id)
                this.$emit('updated');

            this.card.card_id = null;
            this.card.name = '';
            this.card.shortcut = '';
            this.card.messages = [{
                type: "text",
                value: null
            }]

            this.resetErrors();
        },

        resetErrors(){
          $('.card-form .was-invalid').removeClass('was-invalid')
        },

        /**
         * Returns api url address for update
         */
        getUpdateApiUrl(){
            return this.routes.update_card.replace('{card_id}', this.card.card_id)
        },

        /**
         * Sends form to server for create new card
         */
        submit(e){
            e.preventDefault();


            if(this.atLeastMessageLengthValidation()){
                this.processing = true;

                this.removeEmptyMessages()

                let thisClass = this;

                setTimeout(function(){

                    let data = thisClass.collectFormDataInJson();

                    $.ajax({
                        type: 'POST',
                        cache: false,
                        contentType: false,
                        processData: false,
                        headers: {'X-CSRF-Token' : thisClass.csrf},
                        url: (thisClass.card.card_id ? thisClass.getUpdateApiUrl() : thisClass.routes.store_card),
                        // dataType: 'json',
                        data: data,
                        error(response){
                            if(response.status === 422){
                                let errors = JSON.parse(response.responseText).errors;

                                new FormError(errors, e.target)
                            }
                            else
                                alert('خطای سرور')
                        },
                        success(response){
                            thisClass.success(response);
                        },
                        complete(){
                            thisClass.processing = false;
                        }
                    });

                }, 2000)

            }
        },

        selectFile(e){
            let element = e.target;

            let index = element.getAttribute('data-index');

            let input = document.querySelector('input[type="file"][name="messages[' + index + '][value]"]');

            input.click();
        },

        /**
         * When submit request respond success
         */
        success(response){
            this.successful = true;

            this.successMessage = response.message;

            let thisClass = this;

            if(!this.card.card_id)
                setTimeout(function(){
                    thisClass.confirmSuccess();
                },5000)
        },

        /**
         * Remove success response and show again card form
         */
        confirmSuccess(){
            this.successful = false;
            this.reset();
        },

        /**
         * takes entry form element data and convert its data to json format then return that
         *
         * @returns {{}}
         */
        collectFormDataInJson(){
            let thisClass = this;

            let formData = new FormData;

            let messages = this.card.messages;

            messages.forEach(function(value, index){
               value.send_order_index = index
            });

            this.card.messages = messages

            formData.append('name', this.card.name)
            formData.append('shortcut', this.card.shortcut)


            this.card.messages.forEach(function(item, index){
                if(item.type === 'file'){
                    formData.append('messages['+ index +'][filename]', item.filename)
                }
                if(item.id){
                    formData.append('messages['+ index +'][id]', item.id)
                }
                formData.append('messages['+ index +'][type]', item.type)
                formData.append('messages['+ index +'][send_order_index]', item.send_order_index + 1)
                formData.append('messages['+ index +'][value]', item.value)
            })

            if(this.card.card_id){
                formData.append('_method', 'put')
            }

            return formData;
        },

        /**
         * Checks that have the create form any filled message row, If has not displays error message
         */
        atLeastMessageLengthValidation(){
            for(let x = 0; x < this.card.messages.length; x++){
                let value = this.card.messages[x].value;
                if(value != null && value !== "")
                    return true;
            }

            alert('برای ساخت کارت جدید حداقل باید یک پیام برای آن ثبت کنید !')

            return false;
        },

        /**
         * Adds new message row at top of current message
         *
         * @param e event
         */
        addNewRowBeforeCurrent(e){
            let index = Number.parseInt(e.target.getAttribute('data-index'));

            this.card.messages.splice((index - 1), 0, this.emptyIndexInstance())
        },

        /**
         * Adds new message row at bottom of current message
         *
         * @param e event
         */
        addNewRowAfterCurrent(e) {
            let index = Number.parseInt(e.target.getAttribute('data-index'));

            this.card.messages.splice((index + 1), 0, this.emptyIndexInstance())
        },

        /**
         * Returns an empty message index object
         */
        emptyIndexInstance(){
            return {
                type : 'text',
                value : null
            }
        },

        /**
         * this is on click event handler of the delete messages row
         *
         * @param e
         */
        removeMessageRow(e){
            let element = e.target;

            let index = element.getAttribute('data-index');

            if(this.card.messages.length === 1){
                this.card.messages = [];
                this.card.messages = [
                    this.emptyIndexInstance()
                ]
            }
            else
                this.card.messages.splice(index, 1);
        },

        removeEmptyMessages(){
            if(this.card.messages.length < 2)
                return true;

            for (let x = 0; x < this.card.messages.length; x++)
                if(this.card.messages[x].value === "" || this.card.messages[x].value === null)
                    this.card.messages.splice(x, 1);
        },

        /**
         * This event will fire when one of inputs of each messages row change
         */
        messageFileSelected(e){
            let input = e.target;

            let index = input.getAttribute('data-index');

            let file = input.files[0];

            this.card.messages[index].value = file;

            this.addFileMessageInfoToCollection(index, file);

            this.updateMessagesRows();
        },

        /**
         * Makes URL of selected file in input type file message then sets that in messages property in related index
         */
        addFileMessageInfoToCollection(index, file){
            this.card.messages[index].link = URL.createObjectURL(file);

            this.card.messages[index].filename = file.name;
        },

        /**
         *
         * @returns {boolean}
         */
        updateMessagesRows(){
            let lastMessage = this.card.messages[this.card.messages.length - 1];

            // add new message row if last row input is not empty
            if(lastMessage.value != null && lastMessage.value !== ''){
                this.card.messages.push({type: 'text', value: null})

                return true;
            }

            // remove end input if there is tow empty messages row
            if(this.card.messages.length > 1){
                let penultimateMessage = this.card.messages[this.card.messages.length - 2];

                if((lastMessage.value == null || lastMessage.value === "") && (penultimateMessage.value == null || penultimateMessage.value === ""))
                    this.card.messages.pop();
            }
        }
    },
    watch:{
        formData(){
            this.card = {
                card_id: (this.formData.card_id ?? null), // if be filled , form will send to update card api
                name: this.formData.name,
                shortcut: this.formData.shortcut,
                messages:this.formData.messages,
            };

            this.resetErrors()
        }
    }
}
</script>

<style scoped>

</style>
