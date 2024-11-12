<template>
    <el-row :gutter="10">
        <el-col :span="12">
            <AccountingDocument v-model="info.document" :distributor="refund.distributor"
                                @update:modelValue="setInfo" v-model:saving="iSavingInfo" :edit="notEdit"/>

        </el-col>
        <el-col :span="12">
            <el-form-item label="Курс валюты">
                <el-tag type="primary">{{ refund.exchange_fix }}</el-tag>
            </el-form-item>
            <el-form-item label="Основание">
                <template v-if="refund.supply_id">
                    <el-input value="Заказ поставщику" readonly style="width: 160px"/>
                    <Link type="primary" :href="route('admin.accounting.supply.show', refund.supply_id)" class="ml-2">{{ refund.founded }}</Link>
                </template>
                <template v-if="refund.arrival_id">
                    <el-input value="Приходная накладная" readonly style="width: 160px"/>
                    <Link type="primary" :href="route('admin.accounting.arrival.show', refund.arrival_id)" class="ml-2">{{ refund.founded }}</Link>
                </template>
            </el-form-item>
            <el-form-item label="Хранилище">
                <el-select v-model="info.storage_id" @change="setInfo" :disabled="iSavingInfo || notEdit || refund.supply_id" style="width: 260px">
                    <el-option v-for="item in storages" :key="item.id" :value="item.id" :label="item.name"  :readonly="notEdit"/>
                </el-select>
            </el-form-item>
        </el-col>
    </el-row>
</template>

<script setup>
import {func} from '@Res/func.js'
import {computed, reactive, ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import AccountingDocument from "@Comp/Pages/AccountingDocument.vue";

const props = defineProps({
    refund: Object,
    storages: Array,
    operations: Array,
})
const iSavingInfo = ref(false)

const info = reactive({
    document: {
        number: props.refund.number,
        created_at: props.refund.created_at,
        incoming_number: props.refund.incoming_number,
        incoming_at: props.refund.incoming_at,
        comment: props.refund.comment,
    },
    storage_id: props.refund.storage_id,
})
const notEdit = computed(() => props.refund.completed);

function setInfo() {
    iSavingInfo.value = true
    router.visit(route('admin.accounting.refund.set-info', {refund: props.refund.id}), {
        method: "post",
        data: info,
        onSuccess: page => {
            iSavingInfo.value = false;
        }
    })
}
</script>
