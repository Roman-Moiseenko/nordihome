<template>
    <template v-if="departure.completed">
        <AccountingOnBased :based="departure.based" :founded="departure.founded"/>
        <AccountingPrint :print="print" />
        <el-button type="danger" class="ml-5" @click="onWork">Отменить проведение</el-button>
    </template>
    <template v-else>
        <SearchAddProduct
            :route="route('admin.accounting.departure.add-product', {departure: departure.id})"
            :quantity="true"
        />
        <SearchAddProducts :route="route('admin.accounting.departure.add-products', {departure: departure.id})" class="ml-3"/>
        <el-button type="danger" plain class="ml-5" @click="onCompleted">Провести документ</el-button>
    </template>
    <span class="ml-auto">
        Сумма <el-tag type="danger" size="large">{{ func.price(departure.amount) }}</el-tag>
    </span>

</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {defineProps} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import AccountingOnBased from "@Comp/Pages/AccountingOnBased.vue";
import AccountingPrint from "@Comp/Pages/AccountingPrint.vue";

const props = defineProps({
    departure: Object,
    print: Array,
})

function onCompleted() {
    router.visit(route('admin.accounting.departure.completed', {departure: props.departure.id}), {
        method: "post",
    })
}
function onWork() {
    router.visit(route('admin.accounting.departure.work', {departure: props.departure.id}), {
        method: "post",
    })
}

</script>
