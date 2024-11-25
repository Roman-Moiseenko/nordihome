<template>
    <template v-if="departure.completed">

        <AccountingPrint />
        <AccountingWork v-if="!departure.inventory"  :route="route('admin.accounting.departure.work', {departure: props.departure.id})" />
    </template>
    <template v-else>
        <SearchAddProduct
            :route="route('admin.accounting.departure.add-product', {departure: departure.id})"
            :quantity="true"
        />
        <SearchAddProducts :route="route('admin.accounting.departure.add-products', {departure: departure.id})" class="ml-3"/>
        <AccountingCompleted :route="route('admin.accounting.departure.completed', {departure: props.departure.id})" />
    </template>
    <AccountingOnBased :based="departure.based" :founded="departure.founded"/>
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
import AccountingCompleted from "@Comp/Pages/AccountingCompleted.vue";
import AccountingWork from "@Comp/Pages/AccountingWork.vue";

const props = defineProps({
    departure: Object,
})


</script>
