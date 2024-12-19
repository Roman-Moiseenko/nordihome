<template>
    <Head><title>{{ title }} - {{ payment.distributor.name}}</title></Head>
    <h1 class="font-medium text-xl">
        Платежное поручение {{ payment.number }} от {{ func.date(payment.created_at) }} - {{ payment.distributor.name }}
        ({{ payment.distributor.short_name }})
    </h1>
    <div class="mt-3 p-3 bg-white rounded-lg ">
        <PaymentInfo :payment="payment" :payers="payers"/>
    </div>
    <el-affix target=".affix-container" :offset="64">
        <div class="bg-white rounded-lg my-2 p-1 shadow flex">
            <PaymentActions :payment="payment"/>
        </div>
    </el-affix>

    <el-table :data="[...payment.decryptions]"
              header-cell-class-name="nordihome-header"
              style="width: 100%;">
        <el-table-column label="Заказ поставщика (основание)" width="360">
            <template #default="scope">
                Заказ поставщика {{ scope.row.supply.number }} от {{ func.date(scope.row.supply.created_at) }}
            </template>
        </el-table-column>
        <el-table-column prop="amount" label="Сумма расчетов" width="180">
            <template #default="scope">
                <el-input v-model="scope.row.amount"
                          :formatter="(value) => func.MaskFloat(value)"
                          @change="setAmount(scope.row)"
                          :disabled="iSaving"
                          :readonly="!isEdit"
                >
                    <template #append>{{ payment.currency }}</template>
                </el-input>
            </template>
        </el-table-column>
        <el-table-column label="Действия" align="right" width="300">
            Для удаления установите сумму в 0
        </el-table-column>
    </el-table>
</template>

<script lang="ts" setup>
import {ref, defineProps, computed, reactive, provide} from "vue";
import {Head, router} from '@inertiajs/vue3'
import {func} from '@Res/func.js'
import PaymentInfo from "./Blocks/Info.vue";
import PaymentActions from "./Blocks/Actions.vue";

const props = defineProps({
    payment: Object,
    title: {
        type: String,
        default: 'Платежное поручение',
    },
    payers: Array,
    printed: Object,
})
//provide('$filters', props.filters) //Фильтр товаров в списке документа
provide('$printed', props.printed) //Для печати
provide('$accounting', props.payment) //Для общих действий
const iSaving = ref(false)
const isEdit = computed<Boolean>(() => !props.payment.completed);
const form = reactive({
    number: props.payment.number,
    payer_id: props.payment.payer_id,
    created_at: props.payment.created_at,
    amount: props.payment.amount,
})

function setAmount(row) {
    iSaving.value = true;
    router.visit(route('admin.accounting.payment.set-amount', {decryption: row.id}), {
        method: "post",
        data: {
            amount: row.amount,
        },
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            iSaving.value = false;
        }
    })
}
</script>
