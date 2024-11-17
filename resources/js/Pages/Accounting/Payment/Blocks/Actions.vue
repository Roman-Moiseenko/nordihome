<template>
    <template v-if="payment.completed">
        <AccountingOnBased :based="payment.based" :founded="payment.founded"/>
        <AccountingPrint />
        <AccountingWork :route="route('admin.accounting.payment.work', {payment: props.payment.id})" />
    </template>
    <template v-else>
        <AccountingCompleted :route="route('admin.accounting.payment.completed', {payment: props.payment.id})" />
    </template>
</template>

<script setup>
import {defineProps} from "vue";
import {router} from "@inertiajs/vue3";
import AccountingOnBased from "@Comp/Pages/AccountingOnBased.vue";
import AccountingPrint from "@Comp/Pages/AccountingPrint.vue";
import AccountingCompleted from "@Comp/Pages/AccountingCompleted.vue";
import AccountingWork from "@Comp/Pages/AccountingWork.vue";

const props = defineProps({
    payment: Object,
})

</script>
