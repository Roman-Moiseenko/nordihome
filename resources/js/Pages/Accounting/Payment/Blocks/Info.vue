<template>
    <el-row :gutter="10">
        <el-col :span="12">
            <el-form label-width="auto">
                <AccountingDocument v-model="info.document" :distributor="payment.distributor"
                                    @update:modelValue="setInfo" v-model:saving="iSavingInfo" :edit="notEdit"/>

            </el-form>
        </el-col>
        <el-col :span="12">
            <el-form label-width="auto">
                <el-form-item label="Получатель">
                    <el-input :model-value="payment.distributor.short_name" :readonly="true" style="width: 300px"/>
                </el-form-item>
                <el-form-item label="Плательщик">
                    <el-select v-model="info.payer_id" @change="setInfo" :disabled="iSavingInfo || notEdit" style="width: 260px">
                        <el-option v-for="item in payers" :key="item.organization_id" :value="item.organization_id" :label="item.shot_name" :readonly="notEdit">{{ item.full_name }}</el-option>
                    </el-select>
                    <span class="ml-2 mr-1">Счет</span>
                    <span class="text-blue-900">{{ payment.payer_account }}</span>
                </el-form-item>
                <el-form-item label="Сумма оплаты">
                    <el-input v-model="info.amount" @change="setInfo" :disabled="iSavingInfo" :readonly="notEdit || !payment.manual" style="width: 160px"/>
                </el-form-item>
                <el-form-item label="Документ к зачету">
                    <el-input v-model="payment.order_bank" :readonly="true" style="width: 300px"/>
                </el-form-item>

                <el-form-item label="Назначение">
                    <el-input :model-value="payment.bank_purpose" :readonly="true"/>
                </el-form-item>
                <el-form-item label="Платежное поручение">
                    <el-input v-if="payment.bank_number" :model-value="'№' + payment.bank_number + ' от ' + func.date(payment.bank_number)" :readonly="true" style="width: 300px"/>
                </el-form-item>
            </el-form>
        </el-col>
    </el-row>
</template>

<script setup>
import {func} from '@Res/func.js'
import {computed, reactive, ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import AccountingDocument from "@Comp/Pages/AccountingDocument.vue";

const props = defineProps({
    payment: Object,
    payers: Array,
})
console.log(props.payers, props.payment.payer_id)
const iSavingInfo = ref(false)
const info = reactive({
    document: {
        number: props.payment.number,
        created_at: props.payment.created_at,
        incoming_number: props.payment.incoming_number,
        incoming_at: props.payment.incoming_at,
        comment: props.payment.comment,
    },
    //number: props.payment.number,
   // created_at: props.payment.created_at,
    amount: props.payment.amount,
    payer_id: props.payment.payer_id,
    //comment:  props.payment.comment,
})
const notEdit = computed(() => props.payment.completed);

function setInfo() {
    iSavingInfo.value = true
    info.created_at = func.datetime(info.created_at)
    router.visit(route('admin.accounting.payment.set-info', {payment: props.payment.id}), {
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
