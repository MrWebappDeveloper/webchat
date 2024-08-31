<script>

import FAQTable from "./FAQ/Index.vue";
import CreateFaqFrm from './FAQ/Create.vue';
import EditFaqFrm from './FAQ/Edit.vue';
import DeleteFaqAlert from './FAQ/Delete.vue';
import ShowFaq from './FAQ/Show.vue';
import WizardTable from "./Wizard/Index.vue";
import CreateWizardFrm from "./Wizard/Create.vue";
import EditWizardFrm from "./Wizard/Edit.vue";
import DeleteWizardAlert from "./Wizard/Delete.vue";

export default {
    data() {
        return {
            openedSubSettingComponent: null,
            componentData: [],

            routes: {
                faqTb: FAQTable,
                createFaq: CreateFaqFrm,
                editFaq: EditFaqFrm,
                deleteFaq: DeleteFaqAlert,
                showFaq: ShowFaq,
                wizardTb: WizardTable,
                createWizard: CreateWizardFrm,
                editWizard: EditWizardFrm,
                deleteWizard: DeleteWizardAlert,
            }
        }
    },
    methods: {
        backToMenu() {
            this.openedSubSettingComponent = null;
        },
        openSubSetting(routeKey) {
            this.openedSubSettingComponent = this.routes[routeKey];

            this.componentData = []
        },
        openCreateWizardFrm(wizard = null) {
            this.openSubSetting('createWizard')
            if (wizard)
                this.componentData = {
                    parent: wizard
                }
        },
        onCreateNewWizardCanceled(wizard = null) {
            this.openSubSetting('wizardTb')
            if (wizard)
                this.componentData = {
                    parent: wizard
                }
        },
        onCreateNewWizardConfirm(wizard = null) {
            this.openSubSetting('wizardTb')
            if (wizard)
                this.componentData = {
                    parent: wizard
                }
        },
        openWizardEditFrm(wizard) {
            this.openSubSetting('editWizard')
            this.componentData = {
                wizard: wizard
            }
        },
        openDeleteWizardAlert(wizard) {
            this.openSubSetting('deleteWizard')
            this.componentData = {
                wizard: wizard
            }
        },


        openWizardFAQsTb(wizard) {
            this.openSubSetting('faqTb')
            this.componentData = {
                wizard: wizard
            };
        },
        openFaqWizardsTb(faq) {
            this.openSubSetting('wizardTb')
            this.componentData = {
                faq: faq
            };
        },
        openEditFaqFrm(faq) {
            this.openSubSetting('editFaq')
            this.componentData = {
                faq: faq
            };
        },
        openDeleteFaqAlert(faq) {
            this.openSubSetting('deleteFaq')
            this.componentData = {
                faq: faq
            };
        },
        onShowFaq(faq){
            this.openSubSetting('showFaq')
            this.componentData = {
                faq: faq
            };
        }
    }
}
</script>

<template>
    <div class="padding-5 bg-white">

        <div class="display-flex width-100 height-100 content-start setting-nav simple-navbar">
            <div class="menu">
                <div class="item display-flex flex-dir-column content-center" @click="openSubSetting('wizardTb')">
                    <span class="setting-link-txt">ویزارد ها</span>
                </div>
                <div class="item display-flex flex-dir-column content-center" @click="openSubSetting('faqTb')">
                    <span class="setting-link-txt">پاسخ ها</span>
                </div>
            </div>
            <div class="brand  back-red text-color-light">
                <span>تنظیمات ربات پشتیبان</span>
            </div>
        </div>
    </div>
    <div class="body">
        <component :is="openedSubSettingComponent" :data="componentData"
                   @ShowWizardFaqs="openWizardFAQsTb"
                   @ShowFAQWizards="openFaqWizardsTb"
                   @ShowCreateFAQFrm="openSubSetting('createFaq')"
                   @CreateFaqConfirm="openSubSetting('faqTb')"
                   @CreatedFaqCancel="openSubSetting('faqTb')"
                   @ShowEditFAQFrm="openEditFaqFrm"
                   @DeleteFAQ="openDeleteFaqAlert"
                   @DeleteFaqCanceled="openSubSetting('faqTb')"
                   @DeleteFaqConfirm="openSubSetting('faqTb')"
                   @CloseShowFAQ="openSubSetting('faqTb')"

                   @ShowCreateWizardFrm="openCreateWizardFrm"
                   @CreateWizardConfirm="onCreateNewWizardConfirm"
                   @CreateWizardCanceled="onCreateNewWizardCanceled"
                   @ShowEditWizardFrm="openWizardEditFrm"
                   @DeleteWizard="openDeleteWizardAlert"
                   @DeleteWizardConfirm="openSubSetting('wizardTb')"
                   @DeleteWizardCanceled="openSubSetting('wizardTb')"
                   @ShowFAQ="onShowFaq">
        </component>
    </div>
</template>

<style scoped>


</style>
