<template>
    <div id="form-body" class="height-100">
        <div class="register-container display-flex items-center height-100">
            <form action="#" ref="register-form" @submit="submitRegisterFrom" onsubmit="return false" class="width-75 back-light margin-x-auto padding-y-3 padding-x-5 padding-bottom-5 border-rounded shadow fm-shabnam">
                <p class="text-md text-align-center">برای شروع گفتگو لطفا فرم زیر را پر کنید</p>
                <hr class="thin-line">
                <div class="frm-group margin-y-2 width-100">
                    <input type="text" v-model="data.name" name="name" class="frm-control text-sm" placeholder="نام ..."
                           aria-describedby="helpId">
                </div>
                <div class="frm-group margin-y-2 width-100">
                    <input type="email" v-model="data.email" name="email" class="frm-control text-sm text-right" placeholder="ایمیل ..."
                           aria-describedby="helpId">
                </div>

                <button class="button width-100 button-outline-primary btn-sm text-md">شروع گفتگو</button>
            </form>
        </div>
    </div>
</template>

<script>
import {Request} from "../js/Http/Request";
import $ from "jquery";
import {FormError} from "../js/Tools/FormError";

export default {
    data() {
        return {
            body:"",
            data: {
                email: '',
                name: '',
                _token: csrf,
            },
            routes: {
                register: '/chat'
            }
        }
    },
    methods: {
        submitRegisterFrom() {
            Request.post().url(this.routes.register).data(this.data).success(function (response, instance){
                instance.body = response
            }).error(function (response, instance){
                let errors = JSON.parse(response).errors;

                new FormError(errors, instance.$refs['register-form'])

                console.log('Register user for start chat API error !');
            }).use(this).send();

            return false
        }
    },
    watch: {
        body(){
            $("#form-body").html(this.body);
        }
    }
}
</script>

<style scoped>

</style>
