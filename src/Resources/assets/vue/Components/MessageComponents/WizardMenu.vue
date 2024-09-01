<script>

import {Request} from "../../../js/Http/Request";
export default {
    name: 'WizardMenuComponent',
    props:{
        menu: {
            type: Array,
            required: true
        },
        isConnectedToOperator:{
            type: Boolean,
            required: false
        }
    },
    data(){
      return {
          inProcessButtonId:null, // keeps id of clicked button
      }
    },
    methods:{
        /**
         * Emits event
         */
        connectToOperator(){
            this.$emit('ConnectToOperator');
        },
        /**
         * Send selected wizard to server for
         * broadcast next menu or wizard`s FAQs
         *
         * @param wizard_id
         */
        selectWizard(wizard_id){
            if(!this.inProcessButtonId){
                this.inProcessButtonId = wizard_id;

                Request
                    .get()
                    .url('/wizard/' + wizard_id)
                    .success(function(response, instance){
                        console.log(response);
                    })
                    .error(function(response, instance){
                        alert('خطا');
                    })
                    .asyncSend();
            }
        },

        /**
         * Emits go back to previous wizard menu event
         */
        goBack(){
            this.$emit('GoBackMenu');
        }
    }
}
</script>


<template>
    <p class="text-sm" v-if="menu.length > 0 && menu[0].parent_id == null">چگونه می توانم به شما کمک کنم ؟</p>
    <ul class="padding-0">
        <li v-for="(wizard, index) in menu" :key="index">
            <button class="button text-sm margin-y-1 width-100 text-align-center" @click="selectWizard(wizard.id)">
                <span v-if="inProcessButtonId !== wizard.id">{{ wizard.keyword }}</span>
                <img src="../../../imgs/loading.gif" style="width: 20px" v-if="inProcessButtonId === wizard.id">
            </button>
        </li>
        <li :key="menu.length + 1" v-if="menu.length === 0 || (menu.length > 0 && menu[0].parent_id != null)">
            <button class="button text-sm margin-y-1 width-100" @click="goBack">بازگشت به منوی قبل</button>
        </li>
    </ul>
    <button class="button button-sm button-danger width-100">
        <span class="text-sm" @click="connectToOperator" v-if="!isConnectedToOperator">ارتباط با کارشناس</span>
        <span class="text-sm" @click="connectToOperator" v-if="isConnectedToOperator">خروج</span>
    </button>
</template>

<style scoped>

</style>
