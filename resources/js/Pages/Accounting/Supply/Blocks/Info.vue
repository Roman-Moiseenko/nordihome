<template>
    <el-row :gutter="10">
        <el-col :span="12">
            <AccountingDocument v-model="info.document" :distributor="supply.distributor"
                                @update:modelValue="setInfo" v-model:saving="iSavingInfo" :edit="notEdit"/>
        </el-col>
        <el-col :span="12">
            <el-form label-width="auto">
                <el-form-item label="Курс валюты">
                    <el-input v-model="info.exchange_fix" @change="setInfo" :disabled="iSavingInfo" :readonly="notEdit"
                              style="width: 160px"/>
                    <el-tooltip effect="dark" placement="top-start" content="Курс изменился. Обновить?">
                        <el-button type="warning" class="ml-1"
                                   v-if="!notEdit && (info.exchange_fix !== supply.currency_exchange)"
                                   @click="setInfo(true)">
                            <i class="fa-light fa-arrows-rotate"></i>
                        </el-button>
                    </el-tooltip>
                </el-form-item>
                <el-form-item label="Плановая дата поступления">
                    <el-date-picker v-model="info.supply_at" type="date" clearable  @change="setInfo" :disabled="iSavingInfo" :readonly="notEdit"/>
                </el-form-item>
                <el-form-item label="Организация исполнитель">
                    <el-select v-model="info.organization_id"  @change="setInfo" :disabled="iSavingInfo || notEdit" >
                        <el-option v-for="item in supply.distributor.organizations" :key="item.id" :value="item.id" :label="item.short_name"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Организация заказчик">
                    <el-select v-model="info.customer_id"  @change="setInfo" :disabled="iSavingInfo || notEdit" filterable>
                        <el-option v-for="item in customers" :key="item.id" :value="item.id" :label="item.short_name + ' (' + item.inn +')'"/>
                    </el-select>
                </el-form-item>

            </el-form>
        </el-col>
    </el-row>
</template>

<script setup>
import {func} from '@Res/func.js'
import {computed, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import AccountingDocument from "@Comp/Accounting/Document.vue";

const props = defineProps({
    supply: Object,
    customers: Array,
})
const iSavingInfo = ref(false)
const info = reactive({
    document: {
        number: props.supply.number,
        created_at: props.supply.created_at,
        incoming_number: props.supply.incoming_number,
        incoming_at: props.supply.incoming_at,
        comment: props.supply.comment,
    },
    exchange_fix: props.supply.exchange_fix,
    supply_at: props.supply.supply_at,
    organization_id: props.supply.organization_id,
    customer_id: props.supply.customer_id,
})
const notEdit = computed(() => props.supply.completed || props.supply.trashed);

function setInfo(currency = null) {
    if (currency === true) info.currency = true
    if (info.supply_at !== null) info.supply_at = func.date(info.supply_at)
    iSavingInfo.value = true
    router.visit(route('admin.accounting.supply.set-info', {supply: props.supply.id}), {
        method: "post",
        data: info,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            iSavingInfo.value = false;
        }
    })
}
</script>
