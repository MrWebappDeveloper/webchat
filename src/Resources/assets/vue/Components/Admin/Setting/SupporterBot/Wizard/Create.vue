<script>
import FileUploaderComponent from "../../../FileUploader.vue";
import {Request} from "../../../../../../js/Http/Request";
import {FormError} from "../../../../../../js/Tools/FormError";
import 'jquery/dist/jquery.min'
import '../../../../../../packages/chosen/chosen.jquery.min'
export default {
    components: {FileUploaderComponent},
    props:{
        data: {
            type: Array,
            default: () => [],
            validator: function (value) {
                return value.every(item => typeof item.parent === 'object');
            }
        }
    },
    data(){
        return{
            keyword:"",
            parent:null,
            selectedFaqs:{},

            wizards:[],
            faqs:[],

            showSuccessResponse: false,
        }
    },
    async created() {
        await this.loadFaqs();

        await this.loadWizards();

        if(this.data.parent)
            this.parent = this.data.parent.id;
    },
    async mounted() {
        let app = this;

        await this.$nextTick(() => {
            // Use this.$refs to access the ref and initialize Chosen
            setTimeout(function(){
                app.mountParentWizardSelection()
                app.mountFaqsSelection();
            }, 1000);
        });

        // $('.multi-selection').chosen();
    },
    methods:{
        loadWizards(){
            Request
                .get()
                .url('/wizard')
                .success(function(response, instance){
                    let responseJson = JSON.parse(response)

                    instance.wizards = responseJson.wizards;

                })
                .use(this)
                .asyncSend();
        },
        loadFaqs(){
            Request
                .get()
                .url('/chat/faq')
                .success(function(response, instance){
                    let responseJson = JSON.parse(response)

                    instance.faqs = responseJson.faqs;
                })
                .use(this)
                .asyncSend();
        },
        mountParentWizardSelection(){
            let app = this;

            $(this.$refs.wizardSelect).chosen();

            $(this.$refs.wizardSelect).change(function(){
                $(this).find('option:selected').each(function(){
                    app.parent = $(this).val()
                });
            });
        },
        mountFaqsSelection() {
            let app = this;

            $(this.$refs.faqsSelect).chosen();
            $(this.$refs.faqsSelect).change(function(){
                let values = [];
                $(this).find('option:selected').each(function(index){
                    values.push($(this).val())
                });
                app.selectedFaqs = values;
            });
        },

        onSubmit(e){
            e.preventDefault();

            let formData = new FormData();

            formData.append('_token', csrf)

            formData.append('keyword', this.keyword)

            if(this.parent)
                formData.append('parent_id', this.parent)

            if(this.selectedFaqs.length > 0)
                this.selectedFaqs.forEach(function(value, index){
                    formData.append('faqs[' + index + ']', value)
                })

            let app = this;

            $.ajax({
                type: "POST",
                url: "/wizard",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    app.wasSuccessful(response)
                },
                error(response){
                    if(response.status === 422){
                        let responseJson = JSON.parse(response.responseText);

                        new FormError(responseJson.errors, e.target)
                    }else
                        alert('خطا')
                }
            });
        },
        wasSuccessful(response){
            this.showSuccessResponse = true;
        },
        onConfirm(){
            this.question = '';

            this.showSuccessResponse = false;

            if(this.data.parent && this.data.parent.id === this.parent)
                this.$emit('CreateWizardConfirm', this.data.parent)
            else
                this.$emit('CreateWizardConfirm')
        },
        onCancel(){
            if(this.data.parent)
                this.$emit('CreateWizardCanceled', this.data.parent)
            else
                this.$emit('CreateWizardCanceled')
        }
    }
}
</script>

<template>
    <div class="padding-x-5 ">
        <h5>ساخت ویزارد جدید</h5>
        <form action="" @submit="onSubmit" v-if="!showSuccessResponse" class="padding-right-2 display-flex flex-dir-column">
            <div class="frm-group colu-6 padding-x-1">
                <label for="" class="text-color-dark">کلیدواژه :*</label>
                <input type="text" class="frm-control" name="keyword" placeholder="کلیدواژه ویزارد جدید را وارد کنید ..." v-model="keyword">
                <span class="invalid-message"></span>
            </div>
            <div class="frm-group colu-6 padding-x-1">
                <label for="parent_wizard" class="text-color-dark padding-bottom-2">ویزارد پدر :</label>
                <select name="wizards" id="parent_wizard" class="multi-selection width-100 text-white" ref="wizardSelect">
                    <option value="" disabled selected>ویزارد پدر را انتخاب کنید (اختیاری)</option>
                    <option v-for="wizard in wizards" :value="wizard.id" :selected="parent && parent === wizard.id">{{ wizard.keyword }}</option>
                </select>
                <span class="invalid-message"></span>
            </div>
            <div class="frm-group colu-6 padding-x-1">
                <label for="" class="text-color-dark padding-bottom-2">پاسخ ها :</label>
                <select v-model="selectedFaqs" name="faqs" id="" class="multi-selection width-100 text-white" data-placeholder="پاسخ های ویزارد جدید را انتخاب کنید ..." ref="faqsSelect" multiple>
                    <option v-for="faq in faqs" :value="faq.id">{{ faq.question }}</option>
                </select>
                <span class="invalid-message"></span>
            </div>
            <div class="frm-group colu-12 display-flex content-end">
                <input type="submit" value="ایجاد" class="button button-success">
                <button class="button button-warning margin-right-1" type="button" @click="onCancel">انصراف</button>
            </div>
        </form>
        <div class="success-response width-100 display-flex content-center" v-if="showSuccessResponse">
            <div class="box-shadow text-align-center border-radius padding-5 colu-4">
                <i class="i-simple i-success i-size-5"></i>
                <p class="text-align-center padding-top-5">ویزارد جدید با موفقیت ثبت شد !</p>
                <button class="button button-primary button-sm margin-top-3" @click="onConfirm">تایید</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.select2.select2-container .select2-selection .select2-selection__rendered{
    color: red;
}
</style>
