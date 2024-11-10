<template>
    <Layout>
        <Head><title>{{ title }} - {{ payment.distributor}}</title></Head>
        <h1 class="font-medium text-xl">
            Платежное поручение {{ payment.number }} от {{ func.date(payment.created_at) }} - {{ payment.distributor}}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <PaymentInfo :payment="payment" :traders="traders" />
        </div>
        <el-affix target=".affix-container" :offset="64">
            <div class="bg-white rounded-lg my-2 p-1 shadow flex">
                <PaymentActions :payment="payment" />
            </div>
        </el-affix>
    </Layout>
</template>

<script lang="ts" setup>
import {ref, defineProps, computed, reactive} from "vue";
import Layout from "@Comp/Layout.vue";
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
    traders: Array,
})

console.log(props.traders)
const form = reactive({
    number: props.payment.number,
    trader_id: props.payment.trader_id,
    created_at: props.payment.created_at,
    amount: props.payment.amount,
})

</script>
<style scoped>

</style>
