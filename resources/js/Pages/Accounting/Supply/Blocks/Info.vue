<template>
    <el-row :gutter="10">
        <el-col :span="12">
            <AccountingDocument v-model="info.document" :distributor="supply.distributor"
                                @update:modelValue="setInfo" v-model:saving="iSavingInfo" :edit="notEdit"/>
        </el-col>
        <el-col :span="12">
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

        </el-col>
    </el-row>
</template>

<script setup>
import {func} from '@Res/func.js'
import {computed, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import AccountingDocument from "@Comp/Pages/AccountingDocument.vue";

const props = defineProps({
    supply: Object,
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
})
const notEdit = computed(() => props.supply.completed);

function setInfo(currency = null) {
    if (currency === true) info.currency = true
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
