<script>
import {Request} from "../../../../../../js/Http/Request";

export default {
    name: 'WizardTable',
    props:{
        data: {
            type: Array,
            default: () => [],
            validator: function (value) {
                return value.every(item => typeof item.faq === 'object');
            }
        }
    },
    data(){
        return{
            faq: (this.data !== [] ? this.data.faq : null), // Displays only related wizards to it faq if it is set
            page: 1, // define paginate id
            perpage: 15,
            totalPages: null,
            totalItems: null,
            parent: null,
            wizards:[]
        }
    },
    created() {
        if(this.data.parent)
            this.parent = this.data.parent

        this.load();
    },
    methods:{
        /**
         * Sends request to server for fetch list and put on table
         */
        load(){
            Request
                .get()
                .url('/wizard')
                .data({
                    faq: (this.faq ? this.faq.id : ''),
                    just_independent: !this.parent && !this.faq ? 1 : 0,
                    parent : this.parent ? this.parent.id : '',
                    perpage: this.perpage,
                    page : this.page,
                })
                .success(function (response, instance){
                    let responseJson = JSON.parse(response);

                    instance.wizards = responseJson.wizards.data;
                    instance.totalItems = responseJson.wizards.total;
                    instance.totalPages = responseJson.wizards.last_page;
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
        showChildren(wizard){
            if(this.parent){
                wizard.parent_id = this.parent;
            }
            this.parent = wizard;
        },
        showFaqs(wizard){
            this.$emit('ShowWizardFaqs', wizard)
        },
        showCreateFrm(){
            this.$emit('ShowCreateWizardFrm', this.parent)
        },
        showEditFrm(wizard){
            this.$emit('ShowEditWizardFrm', wizard);
        },
        deleteWizard(wizard){
            this.$emit('DeleteWizard', wizard);
        },
        goBackOfParentFocus(){
            this.parent = this.parent.parent_id;
        },
        removeFaqFilter(){
            this.faq = null;

            this.page = 1;

            this.load();
        }
    },
    watch:{
        parent(){
            this.load()
        }
    }
}
</script>

<template>
    <div class="padding-x-5 padding-top-1">
        <div class="setting-header display-flex content-between margin-bottom-5 align-center">
            <h5>لیست ویزارد ها
                {{ parent ? parent.keyword : '' }}
                {{ faq ? " ی پاسخ شماره" + faq.id : '' }}
            </h5>
            <div>
                <button class="button button-primary" @click="showCreateFrm">ویزارد جدید</button>
                <button class="button button-danger margin-right-1" v-show="parent" @click="goBackOfParentFocus">بازگشت</button>
                <button class="button button-danger margin-right-1" @click="removeFaqFilter" v-if="faq">حذف فیلتر</button>
            </div>
        </div>
        <table class="table-simple height-50 overflow-hidden">
            <thead>
            <tr>
                <th>کلیدواژه</th>
                <th class="text-center">روابط</th>
                <th class="text-center">عملیات</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="wizard in wizards">
                <td>{{ wizard.keyword}}</td>
                <td class="text-center">
                    <button class="button button-primary button-sm margin-x-1" @click="showFaqs(wizard)">
                        پاسخ ها
                    </button>
                    <button class="button button-danger button-sm margin-x-1" @click="showChildren(wizard)">
                        ویزارد ها
                    </button>
                </td>
                <td class="text-center">
                    <i class="i-simple i-size-2 i-modify margin-x-1 cursor-pointer" @click="showEditFrm(wizard)"></i>
                    <i class="i-simple i-size-2 i-trash-danger margin-x-1 cursor-pointer" @click="deleteWizard(wizard)"></i>
                </td>
            </tr>
            </tbody>
        </table>
        <p class="text-center width-100 padding-top-2" v-if="!totalItems">هیچ ویزاردی تا کنون ثبت نکرده اید !</p>
        <div class="width-100 display-flex content-center" v-if="totalItems">
            <ul class="pagination padding-top-5">
                <li><a href="#" :class="page === totalPages ? 'disabled' : ''" @click="nextPage">بعدی</a></li>
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
