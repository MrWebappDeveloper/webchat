<script>
    export default {
        props:{
            type: {
                type: String,
                required: true,
                validator(value) {
                    ['error', 'success', 'info', 'warning'].includes(value)
                }
            },
            message: String
        },
        data(){
          return {
          }
        },
        methods:{
            typeClass(){
                let classname = '';
                switch (this.type){
                    case 'error':{
                        classname = 'error'
                        break;
                    }
                    case 'success':{
                        classname = 'success'
                        break;
                    }
                    case 'info':{
                        classname = 'info'
                        break;
                    }
                    case 'warning':{
                        classname = 'warning'
                        break;
                    }
                    default:{
                        classname = 'error';
                        break
                    }
                }
                return classname
            }
        }
    }
</script>

<template>
    <span :class="typeClass()" class="alert">{{ this.message }}</span>
</template>

<style scoped>
    .alert{
        display: block;
        padding: 10px;
        color: white;
        border-radius: 10px;
        text-align: center;
    }
    .error{
        background-color: #f83434;
    }
    .success{
        background-color: #18d218;
    }
    .info{
        background-color: #2c2cef;
    }
    .warning{
        background-color: #ffff2d;
        color: black !important;
    }
</style>
