<template>

    <el-button type="primary" v-if="expense.status.is_new" @click="onAssembly">На сборку</el-button>
    <el-button type="warning" plain @click="onReport">Накладная</el-button>
    <el-button type="danger" plain v-if="!expense.status.is_completed && !expense.status.is_canceled" @click="onCancel">Отменить</el-button>

    <el-button type="primary" plain @click="onOrder" class="ml-auto mr-3">Вернуться к заказу</el-button>
</template>

<script setup lang="ts">
import {defineProps} from "vue";
import {ElLoading} from "element-plus";
import {Link, router} from "@inertiajs/vue3";

const props = defineProps({
    expense: Object,
})

function onAssembly() {
    router.post(route('admin.order.expense.assembly', {expense: props.expense.id}))
}

function onCancel() {
    router.post(route('admin.order.expense.canceled', {expense: props.expense.id}))
}
function onOrder() {
    router.get(route('admin.order.show', {order: props.expense.order_id}))
}
/*
function onCompleted() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Проведение документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.order.payment.completed', {payment: props.payment}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            loading.close()
        }
    })
}
function onWork() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Отмена проведения',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.order.payment.work', {payment: props.payment}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            loading.close()
        },
        onFinish: page => {
            loading.close()
        },
    })
}
*/
</script>
<style scoped>

</style>
