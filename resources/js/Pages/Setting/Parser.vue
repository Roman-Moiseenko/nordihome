<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">{{ title }}</h1>
    <div class="mt-3 p-3 bg-white rounded-lg">
        <el-form :model="form" label-width="auto">
            <el-row :gutter="10">
                <el-col :span="8">
                    <el-form-item label="Внутренний курс злота - коэффициент наценки на стоимость" label-position="top">
                        <el-input v-model="form.parser_coefficient" :formatter="val => func.MaskFloat(val)"  style="width: 150px;"/>
                    </el-form-item>
                    <el-form-item label="Минимальная стоимость доставки" label-position="top">
                        <el-input v-model="form.parser_delivery" :formatter="val => func.MaskInteger(val)" style="width: 300px;"/>
                    </el-form-item>
                    <el-form-item label="Доб.стоимость товара за 1 кг веса (руб.)" label-position="top">
                        <el-input v-model="form.cost_weight" :formatter="val => func.MaskInteger(val)" style="width: 300px;"/>
                    </el-form-item>
                    <el-form-item label="Доб.стоимость товара за 1 кг веса для ХРУПКОГО товара (руб.)" label-position="top">
                        <el-input v-model="form.cost_weight_fragile" :formatter="val => func.MaskInteger(val)" style="width: 300px;"/>
                    </el-form-item>
                    <el-form-item label="Коэффициент наценки за санкционный (%)" label-position="top">
                        <el-input v-model="form.cost_sanctioned" :formatter="val => func.MaskInteger(val)" style="width: 300px;"/>
                    </el-form-item>
                    <el-form-item label="Коэффициент наценки на розницу" label-position="top">
                        <el-input v-model="form.cost_retail" :formatter="val => func.MaskInteger(val)" style="width: 300px;"/>
                    </el-form-item>
                    <el-checkbox v-model="form.with_proxy" :checked="form.with_proxy" label="Через proxy"/>
                    <el-form-item label="Адрес прокси-сервера. Формат записи ip:port" label-position="top">
                        <el-input v-model="form.proxy_ip"   style="width: 300px;"/>
                    </el-form-item>
                    <el-form-item label="Доступ к прокси-серверу. Формат записи логин:пароль" label-position="top">
                        <el-input v-model="form.proxy_user"   style="width: 300px;"/>
                    </el-form-item>

                </el-col>

                <el-col :span="8">
                    <h2 class="font-medium">Стоимость доставки за 1 кг </h2>
                    <el-form-item label="при весе от 0 до 5">
                        <el-input v-model="form.parser_delivery_0" :formatter="val => func.MaskInteger(val)" style="width: 150px;"/>
                    </el-form-item>



                    <el-form-item label="при весе от 5 до 10">
                        <el-input v-model="form.parser_delivery_1" :formatter="val => func.MaskInteger(val)" style="width: 150px;"/>
                    </el-form-item>
                    <el-form-item label="при весе от 10 до 15">
                        <el-input v-model="form.parser_delivery_2" :formatter="val => func.MaskInteger(val)" style="width: 150px;"/>
                    </el-form-item>
                    <el-form-item label="при весе от 15 до 30">
                        <el-input v-model="form.parser_delivery_3" :formatter="val => func.MaskInteger(val)" style="width: 150px;"/>
                    </el-form-item>
                    <el-form-item label="при весе от 30 до 40">
                        <el-input v-model="form.parser_delivery_4" :formatter="val => func.MaskInteger(val)" style="width: 150px;"/>
                    </el-form-item>
                    <el-form-item label="при весе от 40 до 50">
                        <el-input v-model="form.parser_delivery_5" :formatter="val => func.MaskInteger(val)" style="width: 150px;"/>
                    </el-form-item>
                    <el-form-item label="при весе от 50 до 200">
                        <el-input v-model="form.parser_delivery_6" :formatter="val => func.MaskInteger(val)" style="width: 150px;"/>
                    </el-form-item>
                    <el-form-item label="при весе от  200 до 300">
                        <el-input v-model="form.parser_delivery_7" :formatter="val => func.MaskInteger(val)" style="width: 150px;"/>
                    </el-form-item>
                    <el-form-item label="при весе от 300 до 400">
                        <el-input v-model="form.parser_delivery_8" :formatter="val => func.MaskInteger(val)" style="width: 150px;"/>
                    </el-form-item>
                    <el-form-item label="при весе от 400 до 600">
                        <el-input v-model="form.parser_delivery_9" :formatter="val => func.MaskInteger(val)" style="width: 150px;"/>
                    </el-form-item>
                    <el-form-item label="при весе от 600 до 9999999">
                        <el-input v-model="form.parser_delivery_10" :formatter="val => func.MaskInteger(val)" style="width: 150px;"/>
                    </el-form-item>
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
    parser: Object,
    title: {
        type: String,
        default: 'Настройка парсера',
    },
})

const form = reactive({
    slug: 'parser',
    parser_coefficient: props.parser.parser_coefficient,
    parser_delivery: props.parser.parser_delivery,
    cost_weight: props.parser.cost_weight,
    cost_weight_fragile: props.parser.cost_weight_fragile,
    cost_sanctioned: props.parser.cost_sanctioned,
    cost_retail: props.parser.cost_retail,
    with_proxy: props.parser.with_proxy,
    proxy_ip: props.parser.proxy_ip,
    proxy_user: props.parser.proxy_user,

    parser_delivery_0: props.parser.parser_delivery_0,
    parser_delivery_1: props.parser.parser_delivery_1,
    parser_delivery_2: props.parser.parser_delivery_2,
    parser_delivery_3: props.parser.parser_delivery_3,
    parser_delivery_4: props.parser.parser_delivery_4,
    parser_delivery_5: props.parser.parser_delivery_5,
    parser_delivery_6: props.parser.parser_delivery_6,
    parser_delivery_7: props.parser.parser_delivery_7,
    parser_delivery_8: props.parser.parser_delivery_8,
    parser_delivery_9: props.parser.parser_delivery_9,
    parser_delivery_10: props.parser.parser_delivery_10,

})

function onSubmit() {
    router.put(route('admin.setting.update'), form)
}
</script>
