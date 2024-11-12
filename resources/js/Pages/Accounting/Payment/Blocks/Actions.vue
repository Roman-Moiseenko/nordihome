<template>
    <template v-if="payment.completed">
        <el-button type="danger" @click="onWork">Отменить проведение</el-button>
    </template>
    <template v-else>
        <el-button type="danger" plain class="" @click="onCompleted">Провести документ</el-button>
    </template>
</template>

<script setup>
import {defineProps} from "vue";
import {router} from "@inertiajs/vue3";

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
