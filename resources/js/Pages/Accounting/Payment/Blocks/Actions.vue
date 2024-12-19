<template>
    <template v-if="payment.completed">
        <AccountingPrint />
        <AccountingWork :route="route('admin.accounting.payment.work', {payment: props.payment.id})" />
    </template>
    <template v-else>
        <el-button type="success" @click="onAdd">Добавить неоплаченные</el-button>
        <AccountingCompleted :route="route('admin.accounting.payment.completed', {payment: props.payment.id})" />


    </template>
    <AccountingOnBased />
</template>

<script setup>
import {defineProps} from "vue";
import {router} from "@inertiajs/vue3";
import AccountingOnBased from "@Comp/Accounting/OnBased.vue";
import AccountingPrint from "@Comp/Accounting/Print.vue";
import AccountingCompleted from "@Comp/Accounting/Completed.vue";
import AccountingWork from "@Comp/Accounting/Work.vue";

const props = defineProps({
    payment: Object,
})

function onAdd() {
    router.post(route('admin.accounting.payment.not-paid', {payment: props.payment.id}))
}
</script>
