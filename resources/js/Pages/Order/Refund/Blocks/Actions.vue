<template>
    <el-button v-if="!refund.completed" @click="onCompleted" type="danger" plain>
        Провести
    </el-button>
    <el-button v-if="!refund.completed" type="warning" class="ml-2" @click="onThrow">
        Сбросить
    </el-button>
    <el-button v-if="refund.completed" @click="onPayment" type="success" plain>
        Платежный документ
    </el-button>
</template>

<script setup lang="ts">
import {defineProps} from "vue";
import {ElLoading} from "element-plus";
import {Link, router} from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
    refund: Object,
})

function onCompleted() {
    router.post(route('admin.order.refund.completed', {refund: props.refund.id}))
}

function onThrow() {
    router.post(route('admin.order.refund.throw', {refund: props.refund.id}))
}


function onPayment() {
    //TODO router.post(route('admin.order.refund.throw', {refund: props.refund.id}))
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
