<template>
    <el-row :gutter="10">
        <el-col :span="12">
            <AccountingDocument v-model="info.document" :distributor="expense.distributor"
                                @update:modelValue="setInfo" v-model:saving="iSavingInfo" :edit="notEdit"/>
        </el-col>
        <el-col :span="12">
            <el-form label-width="auto">
                <el-form-item label="В валюте поставщика">
                    <el-checkbox v-model="info.currency" @change="setInfo" :disabled="iSavingInfo || notEdit" :checked="info.currency"
                              style="width: 160px"/>
                </el-form-item>
                <el-form-item label="На основании">
                    <Link type="primary" :href="route('admin.accounting.arrival.show', {arrival: expense.arrival_id})">
                        Приходная накладная № {{ expense.arrival.number }} от {{ func.date(expense.arrival.created_at) }}
                    </Link>
                </el-form-item>
            </el-form>
        </el-col>
    </el-row>
</template>

<script setup>
import {computed, reactive, ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import AccountingDocument from "@Comp/Pages/AccountingDocument.vue";
import {func} from '@Res/func.js'

const props = defineProps({
    expense: Object,
})
const iSavingInfo = ref(false)

const info = reactive({
    document: {
        number: props.expense.number,
        created_at: props.expense.created_at,
        incoming_number: props.expense.incoming_number,
        incoming_at: props.expense.incoming_at,
        comment: props.expense.comment,
    },
    currency: props.expense.currency,
})
const notEdit = computed(() => props.expense.completed);

function setInfo() {
    iSavingInfo.value = true
    router.visit(route('admin.accounting.arrival.expense.set-info', {expense: props.expense.id}), {
        method: "post",
        data: info,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            iSavingInfo.value = false;
        }
    })
}
</script>
