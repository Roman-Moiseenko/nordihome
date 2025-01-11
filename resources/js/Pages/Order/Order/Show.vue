<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Заказ покупателя [{{ order.status_text }}]
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <OrderInfo :order="order" :storages="storages" :mainStorage="mainStorage" :traders="traders" />
        </div>
        <el-affix target=".affix-container" :offset="64">
            <div class="bg-white rounded-lg my-2 p-1 shadow flex">
                <OrderActions :order="order" :additions="additions" :storages="storages" />
            </div>
        </el-affix>

        <div v-if="is_new || is_awaiting">
            <div v-if="order.in_stock.length > 0" class="mt-1 px-3 py-1 bg-white rounded-md">
                <h2 class="font-medium text-cyan-800">Товары в наличии</h2>
                <OrderItemsNew :items=[...order.in_stock] />
            </div>
            <div v-if="order.pre_order.length > 0" class="mt-1 px-3 py-1 bg-white rounded-md">
                <h2 class="font-medium text-orange-800">Товары под заказ</h2>
                <OrderItemsNew :items=[...order.pre_order]  />
            </div>
        </div>
        <div v-if="is_issued">
            <div v-if="order.items.length > 0" class="mt-1 px-3 py-1 bg-white rounded-md">
                <h2 class="font-medium text-cyan-800">Товары на выдачу</h2>
                <OrderItemsIssued :items=[...order.items] />
            </div>
        </div>
        <div v-if="order.additions.length > 0" class="mt-1 px-3 py-1 bg-white rounded-md">
            <h2 class="font-medium text-green-800">Услуги</h2>
            <OrderAdditions v-if="is_new || is_awaiting" :additions=[...order.additions] />
            <OrderAdditionsIssued v-if="is_issued" :additions=[...order.additions] />
        </div>

    </el-config-provider>
</template>
<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head} from "@inertiajs/vue3";
import {computed, defineProps, provide} from "vue";
import OrderActions from "./Blocks/Actions.vue";
import OrderInfo from "./Blocks/Info.vue";
import Active from "@Comp/Elements/Active.vue";
import OrderItemsNew from "./Blocks/ItemsNew.vue"
import OrderItemsIssued from "./Blocks/ItemsIssued.vue"
import OrderAdditions from  "./Blocks/Additions.vue"
import OrderAdditionsIssued from  "./Blocks/AdditionsIssued.vue"
const props = defineProps({
    order: Object,
    title: {
        type: String,
        default: 'Заказ покупателя',
    },
    additions: Array,
    storages: Array,
    staffs: Array,
    mainStorage: Object,
    traders: Array,
})
const is_new = computed(() => {
    return props.order.status.is_new || props.order.status.is_manager
})
const is_awaiting = computed(() => {
    return props.order.status.is_awaiting
})

const is_issued = computed(() => {
    return props.order.status.is_prepaid || props.order.status.is_paid
})

const is_view = computed(() => {
    return !is_new.value && !is_issued.value && !is_awaiting.value
})
provide("$status", {
    is_new,
    is_awaiting,
    is_issued,
    is_view,
})

</script>
<style scoped>

</style>
