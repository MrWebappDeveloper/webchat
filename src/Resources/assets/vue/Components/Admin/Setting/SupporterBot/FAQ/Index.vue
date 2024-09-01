<script>
import {Request} from "../../../../../../js/Http/Request";

export default {
    name: "FAQTable",
    props:{
        data: {
            type: Array,
            default: () => [],
            validator: function (value) {
                return value.every(item => typeof item.wizard === 'object');
            }
        }
    },
    data() {
        return {
            wizard: (this.data !== [] ? this.data.wizard : ''), // Displays only related faqs to it wizard if it is set
            page: 1, // define paginate id
            perpage: 15,
            totalPages: null,
            totalItems: null,
            faqs:[],
        }
    },
    created() {
        this.load()
    },
    methods:{
        /**
         * Sends request to server for fetch list and put on table
         */
        load(){
            Request
                .get()
                .url('/chat/faq')
                .data({
                    wizard: (this.wizard ? this.wizard.id : ''),
                    perpage: this.perpage,
                    page : this.page,
                })
                .success(function (response, instance){
                    let responseJson = JSON.parse(response);

                    instance.faqs = responseJson.faqs.data;
                    instance.totalPages = responseJson.faqs.last_page;
                    instance.totalItems = responseJson.faqs.total;
                })
                .error(function (response, instance){

                })
                .use(this)
                .asyncSend()
        },
        nextPage(){
            if(this.page < this.totalPages){
                this.page ++;

                this.load();
            }
        },
        lastPage(){
            if(this.page > 1){
                this.page --;

                this.load();
            }
        },
        showFAQWizards(faq){
            this.$emit('ShowFAQWizards', faq)
        },
        showCreateFrm(){
            this.$emit('ShowCreateFAQFrm')
        },
        showEditFrm(faq){
            this.$emit('ShowEditFAQFrm', faq)
        },
        showFAQ(faq){
            this.$emit('ShowFAQ', faq)
        },
        removeWizardFilter(){
            this.wizard = null;

            this.page = 1;

            this.load();
        },
        deleteFaq(faq){
            this.$emit('DeleteFAQ', faq)
        }
    }
}
</script>

<template>
    <div class="padding-x-5 padding-top-1">
        <div class="setting-header display-flex content-between margin-bottom-5 align-center">
            <h5>لیست پاسخ ها{{ this.wizard ? 'ی ویزارد ' + this.wizard.keyword : '' }}</h5>
            <div>
                <button class="button button-primary" @click="showCreateFrm">ایجاد پاسخ</button>
                <button class="button button-danger margin-right-1" @click="removeWizardFilter" v-if="wizard">حذف فیلتر</button>
            </div>
        </div>
        <table class="table-simple overflow-hidden">
            <thead>
            <tr>
                <th>سوال</th>
                <th class="text-center">روابط</th>
                <th class="text-center">عملیات</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="faq in faqs">
                <td>{{ faq.question.substring(0, 100) + (faq.question.length > 100 ? " ..." : "") }}</td>
                <td class="text-center">
                    <button class="button button-primary button-sm" @click="showFAQWizards(faq)">ویزارد ها</button>
                </td>
                <td class="text-center">
                    <i class="i-simple i-size-2 i-eye margin-x-1 cursor-pointer" @click="showFAQ(faq)"></i>
                    <i class="i-simple i-size-2 i-modify margin-x-1 cursor-pointer" @click="showEditFrm(faq)"></i>
                    <i class="i-simple i-size-2 i-trash-danger margin-x-1 cursor-pointer" @click="deleteFaq(faq)"></i>
                </td>
            </tr>
            </tbody>
        </table>
        <p class="text-center width-100 padding-top-2" v-if="!totalItems">هیچ پاسخی تا کنون ثبت نکرده اید !</p>
        <div class="width-100 display-flex content-center" v-if="totalItems">
            <ul class="pagination padding-top-5">
                <li><a href="#" :class="page == totalPages ? 'disabled' : ''" @click="nextPage">بعدی</a></li>
                <a href="#" class="active">{{ page }}</a>
                <li><a href="#" :class="page < 2 ? 'disabled' : ''" @click="lastPage">قبلی</a></li>
            </ul>
        </div>
    </div>
</template>

<style scoped>
.pagination {
    display: flex;
    list-style: none;
    padding: 0;
}

.pagination li {
    margin: 0 5px;
}

.pagination a {
    display: block;
    padding: 8px 12px;
    text-decoration: none;
    background-color: #3498db;
    color: #fff;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.pagination a.active {
    background-color: #3437db !important;
}

.pagination a.disabled {
    background-color: silver !important;
    cursor: default;
}

.pagination a:hover {
    background-color: #2980b9;
}

</style>
