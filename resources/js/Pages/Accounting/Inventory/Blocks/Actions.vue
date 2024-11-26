<template>
    <template v-if="inventory.completed">

        <AccountingPrint />
        <AccountingWork :route="route('admin.accounting.inventory.work', {inventory: props.inventory.id})" />
    </template>
    <template v-else>

        <AccountingCompleted :route="route('admin.accounting.inventory.completed', {inventory: props.inventory.id})" />
    </template>
    <AccountingOnBased :based="inventory.based" :founded="inventory.founded"/>
    <span class="ml-auto">
        Сумма <el-tag type="danger" size="large">{{ func.price(inventory.amount_formal ) }}</el-tag>
    </span>
    <span class="ml-2">
        Сумма факт. <el-tag type="success" size="large">{{ func.price(inventory.amount_actually ) }}</el-tag>
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
    inventory: Object,
})

</script>
