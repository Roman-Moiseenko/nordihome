<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">{{ title }}</h1>
    <div class="mt-3 p-3 bg-white rounded-lg">
        <el-form :model="form" label-width="auto">
            <el-row :gutter="10">
                <el-col :span="8">
                    <el-form-item label="Время резерва товара в минутах" label-position="top">
                        <el-input v-model="form.reserve" :formatter="val => func.MaskInteger(val)"  style="width: 150px;"/>
                    </el-form-item>

                    <el-checkbox v-model="form.pre_order" :checked="form.pre_order" label="Возможность оформлять предзаказ, когда товара нет в наличии"/>
                    <el-checkbox v-model="form.only_offline" :checked="form.only_offline" label="Продажа товаров только оффлайн, ИМ недоступен"/>
                    <el-checkbox v-model="form.delivery_local" :checked="form.delivery_local" label="Осуществляется доставка товаров по региону собственными силами"/>
                    <el-checkbox v-model="form.delivery_all" :checked="form.delivery_all" label="Осуществляется доставка товара Транспортными компаниями"/>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="Дата последней загрузки из банка" label-position="top">
                        <el-input v-model="form.date_bank"  style="width: 150px;"/>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="Группа в которую переносятся остатки товаров снятых с продажи" label-position="top">
                        <el-select v-model="form.group_last_id">
                            <el-option v-for="item in groups" :key="item.id" :label="item.name" :value="item.id"/>
                        </el-select>
                    </el-form-item>
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
    common: Object,
    title: {
        type: String,
        default: 'Общие настройки',
    },
    groups: Array,
})

const form = reactive({
    slug: 'common',
    reserve: props.common.reserve,
    pre_order: props.common.pre_order,
    only_offline: props.common.only_offline,
    delivery_local: props.common.delivery_local,
    delivery_all: props.common.delivery_all,
    accounting: props.common.accounting,
    date_bank: props.common.date_bank,
    group_last_id: props.common.group_last_id,
})

function onSubmit() {
    router.put(route('admin.setting.update'), form)
}
</script>
