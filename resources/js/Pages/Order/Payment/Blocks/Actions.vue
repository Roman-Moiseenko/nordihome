<template>

    <div v-if="payment.manual">
        <el-button v-if="!payment.completed" type="danger" plain class="ml-5" @click="onCompleted">Провести документ</el-button>
        <el-button v-if="payment.completed" type="danger" class="ml-5" @click="onWork">Отмена проведения</el-button>
    </div>
</template>

<script setup lang="ts">
import {defineProps} from "vue";
import {ElLoading} from "element-plus";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    payment: Object,
})

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
</script>
<style scoped>

</style>
