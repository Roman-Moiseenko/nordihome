<template>
    <template v-if="payment.completed">
        <AccountingOnBased :based="payment.based" />
        <AccountingPrint :print="payment.print" />
        <el-button type="danger" @click="onWork" class="ml-5">Отменить проведение</el-button>
    </template>
    <template v-else>
        <el-button type="danger" plain class="" @click="onCompleted">Провести документ</el-button>
    </template>
</template>

<script setup>
import {defineProps} from "vue";
import {router} from "@inertiajs/vue3";
import AccountingOnBased from "@Comp/Pages/AccountingOnBased.vue";
import AccountingPrint from "@Comp/Pages/AccountingPrint.vue";

const props = defineProps({
    payment: Object,
})

function onCompleted() {
    router.visit(route('admin.accounting.payment.completed', {payment: props.payment.id}), {
        method: "post",
    })
}

function onWork() {
    router.visit(route('admin.accounting.payment.work', {payment: props.payment.id}), {
        method: "post",
    })
}

</script>
