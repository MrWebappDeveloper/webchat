<template>
    <!--BEGIN : response content-->
    <div v-if="successful" class="display-flex content-center padding-y-5">
        <div class="colu-5 box-shadow text-align-center border-radius padding-5">
            <i class="i-simple i-success"></i>
            <p class="text-align-center padding-top-5">کارت جدید با موفقیت ثبت شد !</p>
        </div>
    </div>
    <!--END : response content-->
    <div class="create-cart-conatiner width-100 height-100 display-flex content-center align-center flex-dir-column margin-y-5" v-if="!successful">
        <div class="colu-8 border-radius box-shadow">
            <!-- BEIGN create cart form section header  -->
            <div class="chat-list-header back-red display-flex content-between padding-3 border-radius-top">
                <div class="display-flex align-center">
                    <i class="i-cards i-simple i-size-3 text-color-light"></i>
                    <span class="padding-right-3 text-color-light">اضافه کردن کارت جدید</span>
                </div>
            </div>
            <!-- END create cart form section header  -->
            <form action="" @submit="submit" class="padding-y-4 row-container content-between padding-x-5 card-form">
                <input type="hidden" name="_token" :value="this.csrf">
                <div class="frm-group colu-6">
                    <input type="text" class="frm-control" placeholder="عنوان ..." name="name">
                    <span class="invalid-message"></span>
                </div>
                <div class="frm-group colu-6">
                    <input type="text" class="frm-control" placeholder="میانبر ..." name="shortcut">
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
                            <tr v-for="(message, index) in messages">
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
                                    <div class="frm-group display-block padding-y-0">
                                        <input type="file" :name="'messages_files['+ index +']'" v-if="message.type === 'file'" class="frm-control text-dark margin-y-2" :data-index="index" @change="messageValueChanged">
                                        <textarea :name="'messages_texts['+ index +']'" v-model="message.value" @keyup="messageValueChanged" v-if="message.type === 'text'" :data-index="index" class="frm-control" rows="1" placeholder="متن پیام را وارد کنید ..."></textarea>
                                        <span class="invalid-message">Hello world !</span>
                                    </div>
                                </td>
                                <td class="text-align-center padding-top-5">
                                    <div class="padding-top-1">
                                        <i class="fa-solid fa-up-long text-color-primary padding-left-3 cursor-pointer" @click="addNewRowBeforeCurrent"></i>
                                        <i class="fa-solid fa-trash text-color-danger cursor-pointer" :data-index="index" @click="removeMessageRow"></i>
                                        <a :href="message.link" v-if="message.link" target="_blank" class="text-color-primary padding-right-3">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                        <i class="fa-solid fa-down-long text-color-primary padding-right-3 cursor-pointer" @click="addNewRowAfterCurrent" :data-index="index"></i>
                                    </div>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
                <div class="frm-group colu-3">
                    <button class="button button-danger width-100 position-relative">
                        <span>ثبت</span>
                        <img v-if="processing" src="../imgs/loading.gif" alt="" class="position-absolute" style="width: 15px; left: 10px; top: 30%">
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>


import {Request} from "../js/Http/Request";
import {FormError} from "../js/Tools/FormError";

export default {
        data(){
            return {
                csrf:null,
                successful: false,
                processing:false, // will true when form submit
                messages:[{
                    type: 'text',
                    value: null,
                }],
                routes:{
                    'store_card' : '/card'
                }
            }
        },
        created() {
            this.csrf = csrf;
        },
        methods:{
            /**
             * Sends form to server for create new card
              */
            submit(e){
                e.preventDefault();

                this.processing = true;

                if(this.atLeastMessageLengthValidation()){
                    this.removeEmptyMessages()

                    let thisClass = this;

                    setTimeout(function(){

                        let data = thisClass.collectFormDataInJson(e.target);

                        Request
                            .post()
                            .url(thisClass.routes.store_card)
                            .data(data)
                            .success(function(response, instance){
                                thisClass.successful = true;
                            })
                            .error(function (response, instance){
                                let jsonResponse = JSON.parse(response);

                                new FormError(jsonResponse.errors, e.target)
                            })
                            .asyncSend();

                        this.processing = false;
                    }, 2000)

                }
            },

            /**
             * takes entry form element data and convert its data to json format then return that
             *
             * @param formElement
             * @returns {{}}
             */
            collectFormDataInJson(formElement){
                let formData = new FormData(formElement)

                // Convert FormData to JSON
                let jsonData = {};
                formData.forEach((value, key) => {
                    jsonData[key] = value;
                });

                return jsonData
            },

            /**
             * Checks that have the create form any filled message row, If has not displays error message
             */
            atLeastMessageLengthValidation(){
                for(let x = 0; x < this.messages.length; x++){
                    let value = this.messages[x].value;
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
                this.removeEmptyMessages()

                let index = Number.parseInt(e.target.getAttribute('data-index'));

                this.messages.splice((index - 1), 0, this.emptyIndexInstance())
            },

            /**
             * Adds new message row at bottom of current message
             *
             * @param e event
             */
            addNewRowAfterCurrent(e) {
                this.removeEmptyMessages()

                let index = Number.parseInt(e.target.getAttribute('data-index'));

                this.messages.splice((index + 1), 0, this.emptyIndexInstance())
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

                if(this.messages.length === 1){
                    this.messages[0].value = '';
                    this.messages[0].type = 'text';
                }
                else
                    this.messages.splice(index, 1);
            },

            removeEmptyMessages(){
                if(this.messages.length < 2)
                    return true;

                for (let x = 0; x < this.messages.length; x++)
                    if(this.messages[x].value === "" || this.messages[x].value === null)
                        this.messages.splice(x, 1);
            },

            /**
             * This event will fire when one of inputs of each messages row change
             */
            messageValueChanged(e){
                let input = e.target;

                let index = input.getAttribute('data-index');

                if(input.type.toLowerCase() === 'file'){
                    let file = input.files[0];

                    this.messages[index].value = file;

                    this.addFileMessageLinkToArray(index, file);
                }
                else
                    this.messages[index].value = input.value;

                this.updateMessagesRows();
            },

            /**
             * Makes URL of selected file in input type file message then sets that in messages property in related index
             */
            addFileMessageLinkToArray(index, file){
                this.messages[index].link = URL.createObjectURL(file);
            },

            /**
             *
             * @returns {boolean}
             */
            updateMessagesRows(){
                let lastMessage = this.messages[this.messages.length - 1];

                // this.removeEmptyMessages();

                // add new message row if last row input is not empty
                if(lastMessage.value != null && lastMessage.value !== ''){
                    this.messages.push({type: 'text', value: null})

                    return true;
                }

                // remove end input if there is tow empty messages row
                if(this.messages.length > 1){
                    let penultimateMessage = this.messages[this.messages.length - 2];

                    if((lastMessage.value == null || lastMessage.value === "") && (penultimateMessage.value == null || penultimateMessage.value === ""))
                        this.messages.pop();
                }
            }
        }

    }
</script>

<style scoped>

</style>
