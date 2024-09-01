<script>
import FileUploaderComponent from "../../../FileUploader.vue";
import {Request} from "../../../../../../js/Http/Request";
import {FormError} from "../../../../../../js/Tools/FormError";
import '../../../../../../packages/quill/typography.css'
import '../../../../../../packages/quill/katex.css'
import '../../../../../../packages/quill/editor.css'
import '../../../../../../packages/quill/katex.js'
import Quill from 'quill'

export default {
    components: {FileUploaderComponent},
    data() {
        return {
            showUploader: false,
            question: '',
            requestProcessing: false,

            quill:null,

            // ck:{
            //     editor: Editor,
            //     editorData: '',
            //     editorConfig: {
            //         // The configuration of the editor.
            //     }
            // },

            showSuccessResponse: false,
        }
    },
    mounted() {
        const fullToolbar = [
            [
                {
                    font: []
                },
                {
                    size: []
                }
            ],
            ['bold', 'italic', 'underline', 'strike'],
            [
                {
                    color: []
                },
                {
                    background: []
                }
            ],
            [
                {
                    script: 'super'
                },
                {
                    script: 'sub'
                }
            ],
            [
                {
                    header: '1'
                },
                {
                    header: '2'
                },
                'blockquote',
                'code-block'
            ],
            [
                {
                    list: 'ordered'
                },
                {
                    list: 'bullet'
                },
                {
                    indent: '-1'
                },
                {
                    indent: '+1'
                }
            ],
            [
                'direction',
                {
                    align: []
                }
            ],
            ['link', 'image', 'video', 'formula'],
            ['clean']
        ];

        // const fullEditor = new Quill('#full-editor', {
        //     bounds: '#full-editor',
        //     placeholder: 'Type Something...',
        //     modules: {
        //         formula: true,
        //         toolbar: fullToolbar
        //     },
        //     theme: 'snow'
        // });

        this.quill =
            new Quill('#snow-editor', {
                bounds: '#snow-editor',
                modules: {
                    toolbar: fullToolbar
                },
                theme: 'snow'
            });
    },
    methods: {
        openUploader() {
            this.showUploader = true;
        },
        closeUploader() {
            this.showUploader = false;
        },
        async onSubmit(e) {
            e.preventDefault()

            this.requestProcessing = true;

            let app = this;

            setTimeout(async function () { // set timeout is for wait for CkEditor to commit changes

                await Request
                    .post()
                    .url('/chat/faq')
                    .data({
                        _token: csrf,
                        question: app.question,
                        answer: app.quill.getSemanticHTML()
                    })
                    .success(function (response, instance) {
                        instance.wasSuccessful(response)
                    })
                    .error(function (response) {
                        let responseJson = JSON.parse(response)

                        new FormError(responseJson.errors, e.target)
                    })
                    .use(app)
                    .asyncSend();

                app.requestProcessing = false;
            }, 1000)
        },
        wasSuccessful(response) {
            this.showSuccessResponse = true;
        },
        onConfirm() {
            this.question = '';

            this.showSuccessResponse = false;

            this.$emit('CreateFaqConfirm')
        },
        onCancel() {
            this.$emit('CreatedFaqCancel')
        }
    }
}
</script>

<template>
    <div class="padding-x-5 ">
        <h5>ایجاد پاسخ جدید</h5>
        <form action="" @submit="onSubmit" v-if="!showSuccessResponse">
            <div class="frm-group colu-12 display-flex content-end">
                <button :type="requestProcessing ? 'button' : 'submit'" class="button button-success">
                    <span v-show="!requestProcessing">ایجاد</span>
                    <img src="../../../../../../imgs/loading.gif" alt="" width="15px" v-show="requestProcessing">
                </button>
                <button class="button button-warning margin-right-1" type="button" @click="onCancel">انصراف</button>
            </div>
            <div class="frm-group colu-12">
                <label for="" class="text-color-dark">سوال | عنوان :</label>
                <input type="text" class="frm-control" name="question"
                       placeholder="عنوان یا سوال پاسخ جدید را وارد کنید ..." v-model="question">
                <span class="invalid-message"></span>
            </div>
            <div class="frm-group colu-12">
                <div class="display-flex content-between align-center margin-bottom-2">
                    <label for="" class="text-color-dark">متن پاسخ :</label>
                    <button class="button button-primary button-sm" type="button" @click="openUploader">آپلود فایل
                    </button>
                    <FileUploaderComponent v-if="showUploader" @close="closeUploader" @cancel="closeUploader"
                                           @confirm="closeUploader"></FileUploaderComponent>
                </div>
                <!--                <ckeditor :editor="ck.editor" v-model="ck.editorData" :config="ck.editorConfig"></ckeditor>-->
                <div id="snow-editor" class="border border-secondary">
                </div>
                <span class="invalid-message"></span>
            </div>
        </form>
        <div class="success-response width-100 display-flex content-center" v-if="showSuccessResponse">
            <div class="box-shadow text-align-center border-radius padding-5 colu-4">
                <i class="i-simple i-success i-size-5"></i>
                <p class="text-align-center padding-top-5">پاسخ جدید با موفقیت ثبت شد !</p>
                <button class="button button-primary button-sm margin-top-3" @click="onConfirm">تایید</button>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
