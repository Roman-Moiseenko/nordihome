<template>
    <template v-if="surplus.completed">

        <AccountingPrint />
        <AccountingWork v-if="!surplus.inventory"  :route="route('admin.accounting.surplus.work', {surplus: props.surplus.id})" />
    </template>
    <template v-else>
        <SearchAddProduct
            :route="route('admin.accounting.surplus.add-product', {surplus: surplus.id})"
            :quantity="true"
        />
        <SearchAddProducts :route="route('admin.accounting.surplus.add-products', {surplus: surplus.id})" class="ml-3"/>
        <AccountingCompleted :route="route('admin.accounting.surplus.completed', {surplus: props.surplus.id})" />
    </template>
    <AccountingOnBased />
    <AccountingFilter />
    <span class="ml-auto">
        Сумма <el-tag type="danger" size="large">{{ func.price(surplus.amount) }}</el-tag>
    </span>

</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {defineProps} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import AccountingOnBased from "@Comp/Accounting/OnBased.vue";
import AccountingPrint from "@Comp/Accounting/Print.vue";
import AccountingCompleted from "@Comp/Accounting/Completed.vue";
import AccountingWork from "@Comp/Accounting/Work.vue";
import AccountingFilter from "@Comp/Accounting/Filter.vue";

const props = defineProps({
    surplus: Object,
})


</script>
