<template>

    <template v-if="order.status.is_new || order.status.is_manager">
        <SearchAddProduct
            :route="route('admin.order.add-product', {order: order.id})"
            :quantity="true"
            :preorder="true"
        />
        <SearchAddProducts :route="route('admin.order.add-products', {order: order.id})" class="ml-3"/>

        <SelectAddition :additions="additions" :order="order" />
    </template>
    <template v-if="order.status.is_awaiting">

    </template>
    <template v-if="order.status.is_prepaid">

    </template>
    <template v-if="order.status.is_paid">

    </template>
    <template v-if="order.status.is_completed">

    </template>
    <template v-if="order.status.is_canceled">

    </template>

</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {defineProps} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import {ElLoading} from "element-plus";
import SelectAddition from "@Page/Order/Order/Blocks/SelectAddition.vue";


const props = defineProps({
    order: Object,
    additions: Array,
})
/*
function onExpenses() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Создание документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.accounting.arrival.expense', {arrival: props.arrival.id}), {
        method: "post",
        onSuccess: page => {
            loading.close()
        }
    })
}
function onMovement() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Создание документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.accounting.arrival.movement', {arrival: props.arrival.id}), {
        method: "post",
        onSuccess: page => {
            loading.close()
        }
    })
}
function onPricing() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Создание документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.accounting.arrival.pricing', {arrival: props.arrival.id}), {
        method: "post",
        onSuccess: page => {
            loading.close()
        }
    })
}
function onRefund() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Создание документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.accounting.arrival.refund', {arrival: props.arrival.id}), {
        method: "post",
        onSuccess: page => {
            loading.close()
        }
    })
}
*/
</script>
