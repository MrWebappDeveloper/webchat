<script>
    import SupporterBotMenuSetting from "./SupporterBot/SupporterBotMenuSettingComponent.vue";

    export default {
        name: 'SettingComponent',
        props:{

        },
        data(){
            return {
                openedSubSettingComponent:null,

                routes:{
                    bot : SupporterBotMenuSetting
                }
            }
        },
        methods:{
            openSubSetting(routeKey){
                this.openedSubSettingComponent = this.routes[routeKey];
            },
            backToMenu(){
                this.openedSubSettingComponent = null;
            }
        }
    }
</script>

<template>
    <div class="setting-header display-flex content-between">
        <h4 class="padding-right-5 padding-top-5">
            <span>تنظیمات</span>
        </h4>
        <i class="i-simple i-arrow-left i-size-3 margin-top-5 margin-left-5 cursor-pointer" v-if="openedSubSettingComponent" @click="backToMenu"></i>
    </div>
    <div class="padding-5 menu" v-if="!openedSubSettingComponent">
        <div class="display-flex width-100 content-center">
            <div class="setting-link display-flex flex-dir-column content-center cursor-pointer margin-x-2" @click="openSubSetting('bot')" data-route-key="bot">
                <img src="../../../../imgs/bot.png" alt="" class="setting-link-img">
                <span class="setting-link-txt">ربات پشتیبان</span>
            </div>
        </div>
    </div>
    <div class="setting-body padding-bottom-5 height-auto" v-if="openedSubSettingComponent">
        <component :is="openedSubSettingComponent"></component>
    </div>
</template>

<style scoped>

.setting-link{
    transition: ease all .1s;
    width: 140px;
    box-shadow: silver 0 0 10px;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
}

.setting-link:hover{
    box-shadow: red 0 0 10px;
    scale: 1.02;
}

.setting-link-img{
    //width: 80px;
    //height: 80px;
}

.setting-link-txt{
    padding-top: 10px;
    overflow: hidden;
    text-align: center;
}
</style>
