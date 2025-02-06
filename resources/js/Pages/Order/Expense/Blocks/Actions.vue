<template>

    <el-button type="primary" v-if="expense.status.is_new" @click="onAssembly">На сборку</el-button>
    <el-button type="warning" plain @click="onTrade12">Накладная</el-button>
    <el-button type="danger" plain v-if="!expense.status.is_completed && !expense.status.is_canceled" @click="onCancel">Отменить</el-button>

    <el-button type="primary" plain @click="onOrder" class="ml-auto mr-3">Вернуться к заказу</el-button>

    <el-popover  :visible="visible_refund" v-if="expense.status.is_completed"  placement="bottom-start" :width="246">
        <template #reference>
            <el-button type="danger" dark  class="ml-3" @click="visible_refund = !visible_refund" >
                Возврат
                <el-icon class="ml-1"><ArrowDown /></el-icon>
            </el-button>
        </template>
        <el-select v-model="reason" placeholder="Причина" class="mt-1">
            <el-option v-for="item in reasons" :label="item.label" :value="item.value"/>
        </el-select>
        <div class="mt-2">
            <el-button @click="visible_refund = false">Отмена</el-button>
            <el-button @click="onRefund" type="primary">Создать</el-button>
        </div>
    </el-popover>

    <OrderRelatedDocuments />
</template>

<script setup lang="ts">
import {defineProps, ref} from "vue";
import {ElLoading} from "element-plus";
import {Link, router} from "@inertiajs/vue3";
import axios from "axios";
import OrderRelatedDocuments from "@Comp/Order/RelatedDocuments.vue";

const props = defineProps({
    expense: Object,
    reasons: Array,
})
console.log(props.reasons)
const reason = ref(null)
const visible_refund = ref(false)
function onAssembly() {
    router.post(route('admin.order.expense.assembly', {expense: props.expense.id}))
}

function onCancel() {
    router.post(route('admin.order.expense.canceled', {expense: props.expense.id}))
}
function onOrder() {
    router.get(route('admin.order.show', {order: props.expense.order_id}))
}

function onTrade12() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Идет формирование накладной',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    // console.log(val)
    axios.post(route('admin.order.expense.trade12', {expense: props.expense.id}),null,
        {
            responseType: 'arraybuffer',
//            params: {class: val.class, method: val.method, id: $accounting.id},
        }
    ).then(response => {
        let blob = new Blob([response.data], {type: 'application/*'})
        let link = document.createElement('a')
        let headers = response.headers

        link.href = window.URL.createObjectURL(blob)
        link.download = headers['filename']
        link._target = 'blank'
        document.body.appendChild(link);
        link.click();
        loading.close()
        URL.revokeObjectURL(link.href)
    }).catch(reason => {
        loading.close()
    })
}
function onRefund() {
    router.post(route('admin.order.refund.store', {expense: props.expense}), {reason: reason.value})
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
