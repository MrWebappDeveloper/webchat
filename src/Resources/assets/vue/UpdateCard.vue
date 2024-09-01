<script setup>

</script>

<template>
    <div class="create-cart-conatiner width-100 height-100 display-flex content-center align-center flex-dir-column">
        <div class="colu-8 bordering border-radius">
            <!-- BEIGN create cart form section header  -->
            <div class="chat-list-header back-red display-flex content-between padding-3 border-radius-top">
                <div class="display-flex align-center">
                    <i class="i-cards i-simple i-size-3 text-color-light"></i>
                    <span class="padding-right-3 text-color-light">اضافه کردن کارت جدید</span>
                </div>
            </div>
            <!-- END create cart form section header  -->
            <form action="#" class="padding-y-4 row-container content-between padding-x-5 card-form">
                <div class="frm-group colu-6">
                    <input type="text" class="frm-control" placeholder="عنوان ...">
                    <span class="invalid-message">عنوان وارد شده صحصیح نیست !</span>
                </div>
                <div class="frm-group colu-6">
                    <input type="text" class="frm-control" placeholder="میانبر ...">
                    <span class="invalid-message">عنوان وارد شده صحصیح نیست !</span>
                </div>
                <div class="frm-group colu-12">
                    <p class="text-color-dark padding-bottom-3">لیست پیام های کارت</p>
                    <table class="table-simple">
                        <tr>
                            <th>نوع پیام</th>
                            <th>محتوا</th>
                            <th class="text-align-center">حذف</th>
                        </tr>
                        <tr>
                            <td>
                                <select name="" id="" class="frm-control">
                                    <option value="">متن</option>
                                    <option value="">فایل</option>
                                </select>
                            </td>
                            <td>
                                <textarea name="" id="" class="frm-control" rows="1" placeholder="متن پیام را وارد کنید ..."></textarea>
                            </td>
                            <td class="text-align-center padding-top-5">
                                <i class="fa-solid fa-trash text-color-danger"></i>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="" id="" class="frm-control">
                                    <option value="">متن</option>
                                    <option value="" selected>فایل</option>
                                </select>
                            </td>
                            <td>
                                <input type="file" name="" id="" class="frm-control text-dark">
                            </td>
                            <td class="text-align-center padding-top-5">
                                <i class="fa-solid fa-trash text-color-danger"></i>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="frm-group colu-3">
                    <button class="button button-danger width-100">ثبت</button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>

</style>
