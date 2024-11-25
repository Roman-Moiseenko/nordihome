<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">{{ title }}</h1>
    <div class="mt-3 p-3 bg-white rounded-lg">
        <el-form :model="form" label-width="auto">
            <el-row :gutter="10">
                <el-col :span="8">
                    <el-form-item label="Максимальная скидка в %% от сумы заказа" label-position="top">
                        <el-input v-model="form.coupon" :formatter="val => func.MaskInteger(val)"  style="width: 150px;"/>
                    </el-form-item>
                    <el-form-item label="Сумма в рублях на первую скидку при регистрации" label-position="top">
                        <el-input v-model="form.coupon_first_bonus" :formatter="val => func.MaskInteger(val)"  style="width: 150px;"/>
                    </el-form-item>
                    <el-form-item label="Сколько действует первый купон на покупку (в днях)" label-position="top">
                        <el-input v-model="form.coupon_first_time" :formatter="val => func.MaskInteger(val)"  style="width: 150px;"/>
                    </el-form-item>

                    <el-checkbox v-model="form.bonus_review" :checked="form.bonus_review" label="Бонусный купон за каждый отзыв при покупке"/>


                    <el-form-item label="Награждение в рублях за каждый отзыв" label-position="top">
                        <el-input v-model="form.bonus_amount" :formatter="val => func.MaskInteger(val)"  style="width: 150px;"/>
                    </el-form-item>
                    <el-form-item label="Время отправления запроса на отзыв после завершения заказа (в днях)" label-position="top">
                        <el-input v-model="form.bonus_discount_delay" :formatter="val => func.MaskInteger(val)"  style="width: 150px;"/>
                    </el-form-item>
                </el-col>
                <el-col :span="8">

                </el-col>
                <el-col :span="8">

                </el-col>
            </el-row>

            <el-button type="primary" @click="onSubmit">Сохранить</el-button>
        </el-form>
    </div>
</template>

<script lang="ts" setup>
import { Head, router } from '@inertiajs/vue3'
import {defineProps, reactive} from "vue";
import {func} from '@Res/func.js'

const props = defineProps({
    coupon: Object,
    title: {
        type: String,
        default: 'Настройка купонов',
    },
})

const form = reactive({
    slug: 'coupon',
    coupon: props.coupon.coupon,
    coupon_first_bonus: props.coupon.coupon_first_bonus,
    coupon_first_time: props.coupon.coupon_first_time,
    bonus_review: props.coupon.bonus_review,
    bonus_amount: props.coupon.bonus_amount,
    bonus_discount_delay: props.coupon.bonus_discount_delay,

})

function onSubmit() {
    router.put(route('admin.setting.update'), form)
}
</script>
