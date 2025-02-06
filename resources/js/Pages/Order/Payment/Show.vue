<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            <span v-if="payment.is_refund">
                Платеж за возврат № {{ payment.refund.number }} от {{ func.date(payment.refund.created_at) }}
            </span>
            <span v-else>
                Платеж за заказ {{ payment.order_id ? '№ ' + payment.order.number + ' от ' + func.date(payment.order.created_at) : '[Неопределен]' }}
            </span>
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <PaymentInfo :payment="payment" :methods="methods" :storages="storages" />
        </div>
        <el-affix target=".affix-container" :offset="64">
            <div class="bg-white rounded-lg my-2 p-1 shadow flex">
                <PaymentActions :payment="payment"/>
            </div>
        </el-affix>


    </el-config-provider>
</template>

<script setup lang="ts">
import {Head} from "@inertiajs/vue3";
import ru from 'element-plus/dist/locale/ru.mjs'
import PaymentInfo from "./Blocks/Info.vue";
import PaymentActions from "./Blocks/Actions.vue";
import {func} from  "@Res/func.js"

const props = defineProps({
    payment: Object,
    title: {
        type: String,
        default: 'Платежное поручение',
    },
    methods: Array,
    storages: Array,
    orders: Array, //Заказы awaiting
})

</script>

<style scoped>

</style>
